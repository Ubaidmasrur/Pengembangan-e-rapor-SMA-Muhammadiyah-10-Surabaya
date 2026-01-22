<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeacherRequest;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\ClassStudentAssignment;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $query = Teacher::withTrashed();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nip', 'like', '%' . $search . '%');
            });
        }

        $teachers = $query->paginate(15)->appends(['q' => $request->input('q')]);
        return view('admin.teachers.index', compact('teachers'));
    }

    public function create()
    {
        return view('admin.teachers.create');
    }

    public function store(TeacherRequest $request)
    {
        $validated = $request->validated();

        Teacher::create($validated);

        return redirect()->route('admin.teachers.index')->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function edit(Teacher $teacher)
    {
        return view('admin.teachers.edit', compact('teacher'));
    }

    public function update(TeacherRequest $request, Teacher $teacher)
    {
        $validated = $request->validated();

        $teacher->update($validated);

        return redirect()->route('admin.teachers.index')->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Teacher $teacher)
    {
        $teacher->delete();

        return redirect()->route('admin.teachers.index')->with('success', 'Data guru berhasil dihapus.');
    }

    public function restore($id)
    {
        $teacher = Teacher::withTrashed()->findOrFail($id);
        $teacher->restore();

        return redirect()->route('admin.teachers.index')->with('success', 'Data guru berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $teacher = Teacher::withTrashed()->findOrFail($id);
        $teacher->forceDelete();

        return redirect()->route('admin.teachers.index')->with('success', 'Data guru berhasil dihapus permanen.');
    }

    // -----------------------------------
    // Fitur untuk guru: akses ke siswa, nilai, dan kelas
    // -----------------------------------

    public function getClassStudent(Request $request)
    {
        try {
            $user = auth()->user();
            $teacher = Teacher::where('user_id', $user->id)->firstOrFail();

            $assignments = $teacher->classTeacherAssignments()->with(['class', 'academicYear'])->get();

            $class_id = $request->input('class_id');
            $academic_year_id = $request->input('academic_year_id');
            $student_search = $request->input('student_search');

            $assignment = $assignments->first(function ($a) use ($class_id, $academic_year_id) {
                $matchClass = $class_id ? $a->class_id == $class_id : true;
                $matchYear = $academic_year_id ? $a->academic_year_id == $academic_year_id : true;
                return $matchClass && $matchYear;
            });

            $students = collect();
            $class = null;
            $academicYear = null;

            if ($assignment) {
                $class = $assignment->class;
                $academicYear = $assignment->academicYear;
                $students = ClassStudentAssignment::searchStudentAssignmentData($student_search, $class->id, $academicYear->id);
            }

            $recentReports = [];
            if ($students->count()) {
                $studentIds = $students->pluck('student_id')->unique();

                $gradeIds = StudentGrade::whereIn('student_id', $studentIds)->pluck('id');

                if ($gradeIds->count()) {
                    $details = \App\Models\StudentGradeDetail::whereIn('student_grade_id', $gradeIds)
                        ->with(['studentGrade', 'subject'])
                        ->latest('updated_at')
                        ->limit(100)
                        ->get();

                    $grouped = $details->groupBy(fn($d) => optional($d->studentGrade)->student_id);
                    foreach ($grouped as $sid => $group) {
                        if ($sid) $recentReports[$sid] = $group->take(3)->values();
                    }
                }
            }

            $classes = $assignments->pluck('class')->unique('id')->values();
            $academicYears = $assignments->pluck('academicYear')->unique('id')->values();

            return view('guru.studentlistteacher', compact('teacher', 'students', 'classes', 'academicYears', 'recentReports'));
        } catch (\Exception $e) {
            return view('guru.studentlistteacher', ['errorMessage' => $e->getMessage()]);
        }
    }

    public function ajaxClasses(Request $request)
    {
        $yearId = $request->query('academic_year_id');
        $teacher = Teacher::where('user_id', auth()->id())->firstOrFail();

        $assignments = $teacher->classTeacherAssignments()
            ->where('academic_year_id', $yearId)
            ->with('class')
            ->get();

        $classes = $assignments->pluck('class')->filter()->unique('id')->values();
        return response()->json($classes);
    }

    public function ajaxStudents(Request $request)
    {
        $ssa = ClassStudentAssignment::with('student')
            ->where('academic_year_id', $request->query('academic_year_id'))
            ->where('class_id', $request->query('class_id'))
            ->get();

        $students = $ssa->pluck('student')->filter()->unique('id')->values()->map(function ($s) {
            return ['id' => $s->id, 'name' => $s->name];
        });

        return response()->json($students);
    }

    public function studentHistory(Request $request, $id)
    {
        $student = Student::with('classes')->findOrFail($id);

        $masters = StudentGrade::where('student_id', $id)
            ->when($request->query('academic_year_id'), fn($q) => $q->where('academic_year_id', $request->query('academic_year_id')))
            ->with(['details.subject', 'academicYear'])
            ->latest('updated_at')
            ->get();

        $openId = $request->query('open');
        return view('guru.grades', compact('student', 'masters', 'openId'));
    }
}
