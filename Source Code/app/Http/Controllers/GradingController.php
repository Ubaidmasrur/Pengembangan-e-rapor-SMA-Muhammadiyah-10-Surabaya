<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SchoolClass;
use App\Models\AcademicYear;
use App\Models\ClassStudentAssignment;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\StudentGradeDetail;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\GradeParameter;

class GradingController extends Controller
{
    public function gradeInput(Request $request)
    {
        $students = Student::orderBy('name')->get();
        $subjects = Subject::orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('year')->orderBy('semester')->get();
        $schoolClasses = SchoolClass::orderBy('name')->get();
        $teachers = Teacher::orderBy('name')->get();

        return view('guru.gradeInput', compact(
            'students',
            'subjects',
            'academicYears',
            'schoolClasses',
            'teachers'
        ));
    }

    // Store a new grade from modal
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'class_id' => 'required|exists:school_classes,id',
            'score' => 'required|numeric|min:0|max:100',
            'grade_letter' => 'nullable|string|max:5',
            'notes' => 'nullable|string|max:1000',
            'fase' => 'nullable|string|max:50',
            'fase_desc' => 'nullable|string|max:2000',
            // period is stored on the master StudentGrade; expect canonical format YYYY-MM
            'period' => ['nullable', 'regex:/^\d{4}-\d{2}$/'],
        ]);

        $teacherId = auth()->user()->teacher ? auth()->user()->teacher->id : null;

        // Find or create the master student_grade record (per student/year/class/teacher)
        // include period in the uniqueness key so each period creates its own master record
        $studentGrade = StudentGrade::firstOrCreate([
            'student_id' => $validated['student_id'],
            'academic_year_id' => $validated['academic_year_id'],
            'class_id' => $validated['class_id'],
            'teacher_id' => $teacherId,
            'period' => $validated['period'] ?? null,
        ], [
            'notes' => $validated['notes'] ?? null,
            'period' => $validated['period'] ?? null,
        ]);

        // create the detail row representing the subject score
        // derive grade letter from GradeParameter (server-side authoritative)
        $derivedLetter = null;
        if (isset($validated['score'])) {
            $param = GradeParameter::where('min_score', '<=', $validated['score'])
                ->where('max_score', '>=', $validated['score'])
                ->orderByDesc('min_score')
                ->first();
            $derivedLetter = $param ? $param->grade_letter : null;
        }

        try {
            $detail = StudentGradeDetail::create([
                'student_grade_id' => $studentGrade->id,
                'subject_id' => $validated['subject_id'],
                'score' => $validated['score'],
                'grade_letter' => $derivedLetter, // enforce derived value
                'notes' => $validated['notes'] ?? null,
                'fase' => $validated['fase'] ?? null,
                'fase_desc' => $validated['fase_desc'] ?? null,
            ]);
        } catch (\Illuminate\Database\QueryException $qe) {
            // possible unique constraint violation: update existing detail instead
            $existing = StudentGradeDetail::where('student_grade_id', $studentGrade->id)
                ->where('subject_id', $validated['subject_id'])
                ->first();
            if ($existing) {
                $existing->update([
                    'score' => $validated['score'],
                    'grade_letter' => $derivedLetter,
                    'notes' => $validated['notes'] ?? null,
                    'fase' => $validated['fase'] ?? null,
                    'fase_desc' => $validated['fase_desc'] ?? null,
                ]);
                $detail = $existing;
            } else {
                // rethrow if unexpected
                throw $qe;
            }
        }

        $detail->load('subject');

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'message' => 'Grade added successfully.',
                'grade' => [
                    'id' => $detail->id,
                    'student_grade_id' => $studentGrade->id,
                    'student_id' => $studentGrade->student_id,
                    'subject_id' => $detail->subject_id,
                    'subject_name' => optional($detail->subject)->name,
                    'score' => $detail->score,
                    'grade_letter' => $detail->grade_letter,
                    // period is stored at master level
                    'notes' => $detail->notes,
                    'created_at' => $detail->created_at,
                ]
            ], 201);
        }

        return redirect()->back()->with('success', 'Grade added successfully.');
    }

    /**
     * Bulk store pending grades submitted from the page.
     */
    public function bulkStore(Request $request)
    {
        $data = $request->validate([
            'grades' => 'required|array|min:1',
            'grades.*.student_id' => 'nullable|exists:students,id',
            'grades.*.subject_id' => 'required|exists:subjects,id',
            'grades.*.academic_year_id' => 'nullable|exists:academic_years,id',
            'grades.*.class_id' => 'nullable|exists:school_classes,id',
            'grades.*.score' => 'nullable|numeric|min:0|max:100',
            'grades.*.grade_letter' => 'nullable|string|max:5',
            'grades.*.notes' => 'nullable|string|max:1000',
            'grades.*.fase' => 'nullable|string|max:50',
            'grades.*.fase_desc' => 'nullable|string|max:2000',
            'grades.*.detail_id' => 'nullable|integer|exists:student_grade_details,id',
            'deleted_detail_ids' => 'nullable|array',
            'deleted_detail_ids.*' => 'integer|exists:student_grade_details,id',
            // detail-level period removed; master_period will be used instead
            'master_academic_year_id' => 'nullable|exists:academic_years,id',
            'master_class_id' => 'nullable|exists:school_classes,id',
            'master_student_id' => 'nullable|exists:students,id',
            'master_note' => 'nullable|string|max:1000',
            'master_motorik' => 'nullable|numeric|min:0|max:100',
            'master_kognitif' => 'nullable|numeric|min:0|max:100',
            'master_sosial' => 'nullable|numeric|min:0|max:100',
            // master_period expected in canonical YYYY-MM format
            'master_period' => ['nullable','regex:/^\d{4}-\d{2}$/'],
        ]);

        // master values (may be used as defaults when individual grade rows omit them)
    $masterYear = $request->input('master_academic_year_id');
    $masterClass = $request->input('master_class_id');
    $masterStudent = $request->input('master_student_id');
    $masterMotorik = $request->input('master_motorik');
    $masterKognitif = $request->input('master_kognitif');
    $masterSosial = $request->input('master_sosial');
    $masterPeriod = $request->input('master_period');

    $teacherId = auth()->user()->teacher ? auth()->user()->teacher->id : null;
    $saved = [];

        \DB::beginTransaction();
        try {
            // process any requested deletions first
            if (!empty($data['deleted_detail_ids']) && is_array($data['deleted_detail_ids'])) {
                foreach ($data['deleted_detail_ids'] as $delId) {
                    try {
                        $d = StudentGradeDetail::find($delId);
                        if ($d) {
                            $d->delete();
                        }
                    } catch (\Exception $ex) {
                        // ignore individual deletion errors and continue
                    }
                }
            }

            foreach ($data['grades'] as $g) {
                // prefer per-row values; fall back to master values when omitted
                $studentId = $g['student_id'] ?? $masterStudent;
                $yearId = $g['academic_year_id'] ?? $masterYear;
                $classId = $g['class_id'] ?? $masterClass;

                if (empty($studentId) || empty($yearId) || empty($classId)) {
                    throw new \Exception('Missing master or grade-level student/class/academic year for one or more grades.');
                }

                // create or find the master student_grade record, include master fields on create
                // include period in uniqueness key so each month gets its own master record
                $studentGrade = StudentGrade::firstOrCreate([
                    'student_id' => $studentId,
                    'academic_year_id' => $yearId,
                    'class_id' => $classId,
                    'teacher_id' => $teacherId,
                    'period' => $g['period'] ?? $data['master_period'] ?? $masterPeriod ?? null,
                ], [
                    'notes' => $data['master_note'] ?? $g['notes'] ?? null,
                    'motorik' => $masterMotorik ?? null,
                    'kognitif' => $masterKognitif ?? null,
                    'sosial' => $masterSosial ?? null,
                    'period' => $g['period'] ?? $data['master_period'] ?? $masterPeriod ?? null,
                ]);

                // If the master record already existed, update any provided master fields (without overwriting with empty values)
                $update = [];
                if (($data['master_note'] ?? null) !== null && ($studentGrade->notes !== ($data['master_note'] ?? null))) {
                    $update['notes'] = $data['master_note'];
                }
                if ($masterMotorik !== null && $masterMotorik !== '') {
                    $update['motorik'] = $masterMotorik;
                }
                if ($masterKognitif !== null && $masterKognitif !== '') {
                    $update['kognitif'] = $masterKognitif;
                }
                if ($masterSosial !== null && $masterSosial !== '') {
                    $update['sosial'] = $masterSosial;
                }
                // If master_period was supplied, ensure we update it on the found record (but normally period is part of the key)
                if (($data['master_period'] ?? null) !== null && ($studentGrade->period !== ($data['master_period'] ?? null))) {
                    $update['period'] = $data['master_period'];
                }
                if (!empty($update)) {
                    $studentGrade->update($update);
                }

                // check for existing detail: prefer explicit detail_id if provided
                $existingDetail = null;
                if (!empty($g['detail_id'])) {
                    $existingDetail = StudentGradeDetail::find($g['detail_id']);
                    // ensure it belongs to our master; if not, ignore and fallback to lookup
                    if ($existingDetail && $existingDetail->student_grade_id != $studentGrade->id) {
                        $existingDetail = null;
                    }
                }
                if (!$existingDetail) {
                    $existingDetail = StudentGradeDetail::where('student_grade_id', $studentGrade->id)
                        ->where('subject_id', $g['subject_id'])
                        ->first();
                }

                if ($existingDetail) {
                    // update only if any of the tracked fields changed
                    $toUpdate = [];
                    $newScore = array_key_exists('score', $g) ? $g['score'] : null;
                    $newNotes = array_key_exists('notes', $g) ? $g['notes'] : null;
                    if ($newScore !== null && $existingDetail->score != $newScore) $toUpdate['score'] = $newScore;
                    // Re-derive grade letter from GradeParameter when score changes or always enforce
                    if ($newScore !== null) {
                        $param = GradeParameter::where('min_score', '<=', $newScore)
                            ->where('max_score', '>=', $newScore)
                            ->orderByDesc('min_score')
                            ->first();
                        $derived = $param ? $param->grade_letter : null;
                        if ($existingDetail->grade_letter != $derived) $toUpdate['grade_letter'] = $derived;
                    }
                    if ($newNotes !== null && $existingDetail->notes != $newNotes) $toUpdate['notes'] = $newNotes;
                    // fase fields
                    if (array_key_exists('fase', $g) && $existingDetail->fase !== ($g['fase'] ?? null)) $toUpdate['fase'] = $g['fase'] ?? null;
                    if (array_key_exists('fase_desc', $g) && $existingDetail->fase_desc !== ($g['fase_desc'] ?? null)) $toUpdate['fase_desc'] = $g['fase_desc'] ?? null;

                    if (!empty($toUpdate)) {
                        $existingDetail->update($toUpdate);
                    }

                    $existingDetail->load('subject');
                    $saved[] = [
                        'id' => $existingDetail->id,
                        'student_grade_id' => $studentGrade->id,
                        'student_id' => $studentGrade->student_id,
                        'subject_id' => $existingDetail->subject_id,
                        'subject_name' => optional($existingDetail->subject)->name,
                        'score' => $existingDetail->score,
                        'grade_letter' => $existingDetail->grade_letter,
                        'notes' => $existingDetail->notes,
                        'fase' => $existingDetail->fase,
                        'fase_desc' => $existingDetail->fase_desc,
                        'created_at' => $existingDetail->created_at,
                        'updated' => true,
                    ];
                } else {
                    // derive letter for new detail
                    $derived = null;
                    if (array_key_exists('score', $g)) {
                        $param = GradeParameter::where('min_score', '<=', $g['score'])
                            ->where('max_score', '>=', $g['score'])
                            ->orderByDesc('min_score')
                            ->first();
                        $derived = $param ? $param->grade_letter : null;
                    }

                    try {
                        $detail = StudentGradeDetail::create([
                            'student_grade_id' => $studentGrade->id,
                            'subject_id' => $g['subject_id'],
                            'score' => $g['score'] ?? null,
                            'grade_letter' => $derived,
                            'notes' => $g['notes'] ?? null,
                            'fase' => $g['fase'] ?? null,
                            'fase_desc' => $g['fase_desc'] ?? null,
                        ]);
                    } catch (\Illuminate\Database\QueryException $qe) {
                        // if unique constraint violation, update the existing detail instead
                        $existing = StudentGradeDetail::where('student_grade_id', $studentGrade->id)
                            ->where('subject_id', $g['subject_id'])
                            ->first();
                        if ($existing) {
                            $existing->update([
                                'score' => $g['score'] ?? $existing->score,
                                'grade_letter' => $derived ?? $existing->grade_letter,
                                'notes' => $g['notes'] ?? $existing->notes,
                                'fase' => $g['fase'] ?? $existing->fase,
                                'fase_desc' => $g['fase_desc'] ?? $existing->fase_desc,
                            ]);
                            $detail = $existing;
                        } else {
                            throw $qe;
                        }
                    }

                    $detail->load('subject');

                    $saved[] = [
                        'id' => $detail->id,
                        'student_grade_id' => $studentGrade->id,
                        'student_id' => $studentGrade->student_id,
                        'subject_id' => $detail->subject_id,
                        'subject_name' => optional($detail->subject)->name,
                        'score' => $detail->score,
                        'grade_letter' => $detail->grade_letter,
                        // period is stored on master record
                        'notes' => $detail->notes,
                        'fase' => $detail->fase,
                        'fase_desc' => $detail->fase_desc,
                        'created_at' => $detail->created_at,
                        'updated' => false,
                    ];
                }
            }

            \DB::commit();
        } catch (\Exception $ex) {
            \DB::rollBack();
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['message' => 'Failed to save bulk grades.', 'error' => $ex->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Failed to save bulk grades: ' . $ex->getMessage());
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['message' => 'Saved', 'saved' => $saved], 201);
        }

        // for regular form POST, redirect back with success message
        return redirect()->back()->with('success', 'Saved ' . count($saved) . ' grades.');
    }

    // Show student grades for the selected student/class/academic year (moved from TeacherController)
    public function showStudentGrades(Request $request, $encodedId = null)
    {
        // Get teacher
        $user = auth()->user();
        $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

        // Prepare assignments for dropdowns
        $assignments = $teacher->classTeacherAssignments()->with(['class', 'academicYear'])->get();
        $classes = $assignments->pluck('class')->unique('id')->values();
        $academicYears = $assignments->pluck('academicYear')->unique('id')->values();

        // Prefer form/query params if present
        $studentId = $request->input('student_id');
        $classId = $request->input('class_id');
        $yearId = $request->input('academic_year_id');

        // If no explicit selection provided, derive from payload (if present) or from first assignment
        if (!$studentId && $encodedId) {
            $payload = json_decode(base64_decode($encodedId), true);
            if ($payload && isset($payload['student_id'])) {
                $studentId = $payload['student_id'] ?? null;
                $classId = $payload['class_id'] ?? $classId;
                $yearId = $payload['academic_year_id'] ?? $yearId;
            }
        }

        if (!$classId || !$yearId) {
            $first = $assignments->first();
            if ($first) {
                $classId = $classId ?? $first->class_id;
                $yearId = $yearId ?? $first->academic_year_id;
            }
        }

        // Build student list for selected class/year. Default to all students so dropdown
        // isn't empty on first load; when class/year are selected prefer assigned students.
        $students = Student::orderBy('name')->get();
        if ($classId && $yearId) {
            $assigned = ClassStudentAssignment::searchStudentAssignmentData(null, $classId, $yearId);
            if (!empty($assigned) && is_countable($assigned) && count($assigned) > 0) {
                $students = $assigned;
            } else {
                // fallback to assignment relation if helper returned empty
                $ssa = ClassStudentAssignment::with('student')
                    ->where('class_id', $classId)
                    ->where('academic_year_id', $yearId)
                    ->get();
                if ($ssa->isNotEmpty()) {
                    $students = $ssa->pluck('student')->filter()->unique('id')->values();
                }
            }

        }

        $selectedStudent = $studentId ? Student::find($studentId) : null;
        $grades = collect();
        if ($studentId && $classId && $yearId) {
            $grades = \App\Models\StudentGradeDetail::join('student_grades', 'student_grade_details.student_grade_id', '=', 'student_grades.id')
                ->join('subjects', 'student_grade_details.subject_id', '=', 'subjects.id')
                ->where('student_grades.student_id', $studentId)
                ->where('student_grades.class_id', $classId)
                ->where('student_grades.academic_year_id', $yearId)
                ->orderByDesc('student_grade_details.id')
                ->select([
                    'student_grade_details.id as id',
                    'student_grades.id as student_grade_id',
                    'student_grades.student_id',
                    'student_grade_details.subject_id',
                    'subjects.name as subject_name',
                    'student_grade_details.score',
                    'student_grade_details.grade_letter',
                    'student_grade_details.notes',
                    'student_grade_details.created_at',
                ])
                ->paginate(15);
        }

        $selectedAcademicYear = $yearId ? $academicYears->firstWhere('id', $yearId) : null;
        $selectedClass = $classId ? $classes->firstWhere('id', $classId) : null;

        $subjects = Subject::orderBy('name')->get();

        return view('guru.grade.studentgrades', compact(
            'teacher', 'students', 'classes', 'academicYears', 'subjects',
            'selectedStudent', 'grades', 'selectedAcademicYear', 'selectedClass'
        ));
    }

    /**
     * Show current authenticated student's grades page (read-only)
     */
    public function myGrades(Request $request)
    {
        $authUser = auth()->user();
        $student = null;
        if ($authUser && method_exists($authUser, 'student') && $authUser->student) {
            $student = $authUser->student;
        }
        if (!$student && $authUser && $authUser->role === 'siswa') {
            $student = Student::where('user_id', $authUser->id)->first();
        }

        $masters = collect();
        if ($student && $student->id) {
            $masters = StudentGrade::where('student_id', $student->id)
                ->with(['details.subject', 'academicYear'])
                ->orderByDesc('updated_at')
                ->get();
        }

        $openId = $request->query('open');
        return view('student.grades', compact('student', 'masters', 'openId'));
    }

    // AJAX: return grade letter for a subject given a numeric score
    public function getGradeLetter(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'score' => 'required|numeric|min:0|max:100',
        ]);

        $subjectId = $request->input('subject_id');
        $score = floatval($request->input('score'));

        $param = GradeParameter::where('min_score', '<=', $score)
            ->where('max_score', '>=', $score)
            ->first();

        $letter = $param ? $param->grade_letter : null;
        return response()->json(['grade_letter' => $letter]);
    }

    // AJAX: return existing master student_grade for given student/class/year (if any)
    public function getMasterRecord(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:school_classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'period' => ['nullable','regex:/^\d{4}-\d{2}$/'],
        ]);

        $studentId = $request->input('student_id');
        $classId = $request->input('class_id');
        $yearId = $request->input('academic_year_id');

        $teacherId = auth()->user()->teacher ? auth()->user()->teacher->id : null;

        $masterQ = StudentGrade::where('student_id', $studentId)
            ->where('class_id', $classId)
            ->where('academic_year_id', $yearId)
            ->where('teacher_id', $teacherId);
        if ($request->filled('period')) {
            $masterQ->where('period', $request->input('period'));
        }
        $master = $masterQ->first();

        if (!$master) {
            return response()->json(['found' => false]);
        }

        return response()->json([
            'found' => true,
            'id' => $master->id,
            'notes' => $master->notes,
            'motorik' => $master->motorik,
            'kognitif' => $master->kognitif,
            'sosial' => $master->sosial,
            'period' => $master->period,
        ]);
    }

    // AJAX: return grade detail rows for a student/class/year (JSON)
    public function getGrades(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'class_id' => 'required|exists:school_classes,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'period' => ['nullable','regex:/^\d{4}-\d{2}$/'],
        ]);

        $studentId = $request->input('student_id');
        $classId = $request->input('class_id');
        $yearId = $request->input('academic_year_id');

        $rowsQ = StudentGradeDetail::join('student_grades', 'student_grade_details.student_grade_id', '=', 'student_grades.id')
            ->join('subjects', 'student_grade_details.subject_id', '=', 'subjects.id')
            ->where('student_grades.student_id', $studentId)
            ->where('student_grades.class_id', $classId)
            ->where('student_grades.academic_year_id', $yearId);

        if ($request->filled('period')) {
            $rowsQ->where('student_grades.period', $request->input('period'));
        }

        $rows = $rowsQ->orderByDesc('student_grade_details.id')
            ->select([
                'student_grade_details.id as id',
                'student_grades.id as student_grade_id',
                'student_grades.student_id',
                'student_grade_details.subject_id',
                'subjects.name as subject_name',
                'student_grade_details.score',
                'student_grade_details.grade_letter',
                'student_grade_details.notes',
                'student_grade_details.fase',
                'student_grade_details.fase_desc',
                'student_grade_details.created_at',
            ])
            ->get();

        return response()->json(['grades' => $rows]);
    }

    // Update an individual detail row
    public function updateDetail(Request $request, $id)
    {
        $detail = StudentGradeDetail::findOrFail($id);

        $validated = $request->validate([
            'score' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:1000',
            'fase' => 'nullable|string|max:50',
            'fase_desc' => 'nullable|string|max:2000',
        ]);

        $toUpdate = [];
        if (array_key_exists('score', $validated)) {
            $toUpdate['score'] = $validated['score'];
            // re-derive grade letter
            $param = GradeParameter::where('min_score', '<=', $validated['score'])
                ->where('max_score', '>=', $validated['score'])
                ->orderByDesc('min_score')
                ->first();
            $toUpdate['grade_letter'] = $param ? $param->grade_letter : null;
        }
        if (array_key_exists('notes', $validated)) $toUpdate['notes'] = $validated['notes'];
        if (array_key_exists('fase', $validated)) $toUpdate['fase'] = $validated['fase'];
        if (array_key_exists('fase_desc', $validated)) $toUpdate['fase_desc'] = $validated['fase_desc'];

        if (!empty($toUpdate)) {
            $detail->update($toUpdate);
        }

        $detail->load('subject');

        return response()->json([
            'message' => 'Detail updated',
            'grade' => [
                'id' => $detail->id,
                'student_grade_id' => $detail->student_grade_id,
                'subject_id' => $detail->subject_id,
                'subject_name' => optional($detail->subject)->name,
                'score' => $detail->score,
                'grade_letter' => $detail->grade_letter,
                'notes' => $detail->notes,
                'fase' => $detail->fase,
                'fase_desc' => $detail->fase_desc,
                'updated_at' => $detail->updated_at,
            ]
        ], 200);
    }

    // Delete an individual detail row
    public function destroyDetail(Request $request, $id)
    {
        $detail = StudentGradeDetail::findOrFail($id);
        try {
            $detail->delete();
        } catch (\Exception $ex) {
            return response()->json(['message' => 'Failed to delete', 'error' => $ex->getMessage()], 500);
        }

        return response()->json(['message' => 'Deleted', 'id' => $id], 200);
    }
}