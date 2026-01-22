<?php

namespace App\Http\Controllers;

use App\Models\{AcademicYear, ClassTeacherAssignment, SchoolClass, StudentGrade, StudentGradeDetail, StudentReport};
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class ReportTeacherController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $teacher = $user && method_exists($user, 'teacher') ? $user->teacher : null;

        $classIds = $teacher ? $teacher->classTeacherAssignments()->pluck('class_id')->unique()->all() : [];

        // 🔁 Base query gabung academic_years agar semester bisa dipakai dalam grouping
        $base = DB::table('student_grades as sg')
            ->join('academic_years as ay', 'sg.academic_year_id', '=', 'ay.id')
            ->selectRaw('sg.student_id, sg.academic_year_id, sg.class_id, ay.semester, ay.start_month, ay.end_month, MAX(sg.id) as sample_grade_id')
            ->whereNull('sg.deleted_at')
            ->groupBy('sg.student_id', 'sg.academic_year_id', 'sg.class_id', 'ay.semester', 'ay.start_month', 'ay.end_month');


        if (!empty($classIds)) {
            $base->whereIn('sg.class_id', $classIds);
        }

        if ($request->filled('academic_year')) {
            $base->where('sg.academic_year_id', $request->academic_year);
        }

        if ($request->filled('class_id')) {
            if (!empty($classIds) && !in_array($request->class_id, $classIds)) {
                return view('report.teacher', [
                    'reports' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15),
                    'academicYears' => AcademicYear::orderBy('year', 'desc')->get(),
                    'classes' => SchoolClass::whereIn('id', $classIds)->get()
                ]);
            }
            $base->where('sg.class_id', $request->class_id);
        }

        if ($request->filled('semester')) {
            $base->where('ay.semester', $request->semester);
        }

        // 🔁 Query utama
        $query = DB::table(DB::raw('(' . $base->toSql() . ') as grouped'))
            ->mergeBindings($base)
            ->join('students', 'grouped.student_id', '=', 'students.id')
            ->join('school_classes', 'grouped.class_id', '=', 'school_classes.id')
            ->join('academic_years', 'grouped.academic_year_id', '=', 'academic_years.id')
            ->leftJoin('student_grades', 'grouped.sample_grade_id', '=', 'student_grades.id')
            ->select(
                'grouped.student_id',
                'students.name as student_name',
                'students.nisn as nis',
                'school_classes.name as class_name',
                'academic_years.year as academic_year',
                'grouped.semester',
                'grouped.start_month',
                'grouped.end_month',
                'grouped.class_id',
                'grouped.academic_year_id',
                'grouped.sample_grade_id as sample_id'
            )
            ->orderBy('students.name', 'asc')
            ->orderBy('academic_years.year', 'desc')
            ->orderBy('grouped.semester', 'desc');

        // 🔁 Pagination
        $perPage = 15;
        $page = $request->input('page', 1);
        $results = $query->forPage($page, $perPage)->get();
        $total = $query->count();

        // 🔍 Detail check
        $sampleIds = $results->pluck('sample_id')->filter()->all();

        $hasDetails = [];
        if (!empty($sampleIds)) {
            $rows = DB::table('student_grade_details')
                ->select('student_grade_id', DB::raw('count(*) as cnt'))
                ->whereIn('student_grade_id', $sampleIds)
                ->groupBy('student_grade_id')
                ->pluck('cnt', 'student_grade_id')
                ->toArray();

            $hasDetails = array_filter($rows, fn($v) => $v > 0);
        }

        foreach ($results as $report) {
            $report->has_report = isset($hasDetails[$report->sample_id]);

            $report->periods = \App\Models\StudentGrade::query()
                ->where('student_id', $report->student_id)
                ->where('class_id', $report->class_id)
                ->where('academic_year_id', $report->academic_year_id)
                ->whereNotNull('period')
                ->when($report->start_month && $report->end_month, function ($query) use ($report) {
                    $query->whereBetween('period', [$report->start_month, $report->end_month]);
                })
                ->select('period')
                ->distinct()
                ->orderBy('period')
                ->pluck('period')
                ->toArray();
        }

        $reports = new \Illuminate\Pagination\LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $classes = SchoolClass::whereIn('id', $classIds)->orderBy('name')->get();

        return view('report.teacher', compact('reports', 'academicYears', 'classes'));
    }


    public function previewByMonthYear(Request $request)
    {
        $month = $request->get('period');
        $studentId = $request->get('student_id');

        if (!$month || !$studentId) {
            abort(404);
        }

        $rawDetails = StudentGradeDetail::with([
            'subject',
            'studentGrade.student',
            'studentGrade.schoolClass',
            'studentGrade.academicYear'
        ])
            ->whereHas('studentGrade', function ($query) use ($studentId, $month) {
                $query->where('student_id', $studentId)
                    ->where('period', $month);
            })
            ->get();

        if ($rawDetails->isEmpty()) {
            abort(404);
        }

        $details = $rawDetails->groupBy('subject_id')->map(function ($group) {
            $first = $group->first();
            $first->score = round($group->avg('score'), 2);
            return $first;
        })->values();

        $first = $rawDetails->first();
        $student = $first->studentGrade->student;
        $schoolClass = $first->studentGrade->schoolClass;
        $academicYear = $first->studentGrade->academicYear;

        // Gunakan StudentReport untuk ambil school
        $report = new \App\Models\StudentReport([
            'student_id' => $student->id,
            'class_id' => $schoolClass->id,
            'academic_year_id' => $academicYear->id,
            'semester' => $academicYear->semester,
        ]);

        $school = $report->school; // ✅ fix di sini

        $fase = $details->first()?->fase ?? '-';

        $totalStudents = StudentGrade::where('class_id', $schoolClass->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('period', $month)
            ->distinct('student_id')
            ->count('student_id');

        $wali = ClassTeacherAssignment::with('teacher')
            ->where('class_id', $schoolClass->id)
            ->where('academic_year_id', $academicYear->id)
            ->where('is_wali', true)
            ->first();

        $homeroomName = $wali?->teacher?->name ?? '_________________';
        $homeroomNip  = $wali?->teacher?->nip ?? 'NIP/NUPTK/NBM';

        $summaryHtml = view('report.summaryPDF', compact(
            'student',
            'schoolClass',
            'academicYear',
            'details',
            'fase',
            'school',
            'totalStudents',
            'homeroomName',
            'homeroomNip'
        ))->render();

        $attachmentHtml = view('report.attachmentPDF', compact(
            'student',
            'schoolClass',
            'academicYear',
            'details',
            'fase',
            'school'
        ))->render();

        $fullHtml = $summaryHtml . '<!--pagebreak-->' . $attachmentHtml;

        $pdf = Pdf::loadHTML($fullHtml)->setPaper('F4', 'portrait');

        return $pdf->stream("Preview Rapor Bulanan {$month} - {$student->name}.pdf");
    }


    public function export($id)
    {
        $grade = StudentGrade::with('academicYear')->findOrFail($id);


        $user = auth()->user();
        $teacher = $user && method_exists($user, 'teacher') ? $user->teacher : null;


        if ($teacher) {
            $allowed = $teacher->classTeacherAssignments()
                ->where('class_id', $grade->class_id)
                ->exists();


            if (!$allowed) {
                abort(403, 'Anda tidak memiliki akses untuk siswa ini.');
            }
        }


        $report = new StudentReport([
            'student_id' => $grade->student_id,
            'class_id' => $grade->class_id,
            'academic_year_id' => $grade->academic_year_id,
            'semester' => $grade->academicYear->semester,
        ]);


        $student = $report->student;
        $schoolClass = $report->schoolClass;
        $academicYear = $report->academicYear;
        $school = $report->school;


        $rawDetails = StudentGradeDetail::with('subject')
            ->whereIn('student_grade_id', function ($query) use ($report) {
                $query->select('id')
                    ->from('student_grades')
                    ->where('student_id', $report->student_id)
                    ->where('class_id', $report->class_id)
                    ->where('academic_year_id', $report->academic_year_id);
            })
            ->get();

        // Group by subject ID and calculate average
        $details = $rawDetails->groupBy('subject_id')->map(function ($group) {
            $first = $group->first(); // Ambil info subject dsb dari entri pertama
            $averageScore = round($group->avg('score'), 2); // Rata-rata nilai

            // Clone to reuse view binding
            $first->score = $averageScore;
            return $first;
        })->values();

        $fase = $details->first()?->fase ?? $details->first()?->fase ?? '-';

        $totalStudents = \App\Models\StudentGrade::query()
            ->where('class_id', $report->class_id)
            ->where('academic_year_id', $report->academic_year_id)
            ->select('student_id')
            ->distinct()
            ->count();

        $wali = ClassTeacherAssignment::with('teacher')
            ->where('class_id', $report->class_id)
            ->where('academic_year_id', $report->academic_year_id)
            ->where('is_wali', true)
            ->first();

        $homeroomName = $wali?->teacher?->name ?? '_________________';
        $homeroomNip  = $wali?->teacher?->nip ?? 'NIP/NUPTK/NBM';

        $summaryHtml = view('report.summaryPDF', compact(
            'student',
            'schoolClass',
            'academicYear',
            'details',
            'fase',
            'school',
            'totalStudents',
            'homeroomName',
            'homeroomNip'
        ))->render();


        $attachmentHtml = view('report.attachmentPDF', compact(
            'student',
            'schoolClass',
            'academicYear',
            'details',
            'fase',
            'school'
        ))->render();

        $fullHtml = $summaryHtml . '<!--pagebreak-->' . $attachmentHtml;

        $pdf = Pdf::loadHTML($fullHtml)->setPaper('F4', 'portrait');

        $year = str_replace('/', '-', $academicYear->year);
        return $pdf->download("Rapor Semester {$academicYear->semester} - {$year} - {$student->name}.pdf");
    }

    public function preview($id)
    {
        $grade = StudentGrade::with('academicYear')->findOrFail($id);

        $user = auth()->user();
        $teacher = $user && method_exists($user, 'teacher') ? $user->teacher : null;

        if ($teacher) {
            $allowed = $teacher->classTeacherAssignments()
                ->where('class_id', $grade->class_id)
                ->exists();

            if (!$allowed) {
                abort(403, 'Anda tidak memiliki akses untuk siswa ini.');
            }
        }

        $report = new StudentReport([
            'student_id' => $grade->student_id,
            'class_id' => $grade->class_id,
            'academic_year_id' => $grade->academic_year_id,
            'semester' => $grade->academicYear->semester,
        ]);

        $student = $report->student;
        $schoolClass = $report->schoolClass;
        $academicYear = $report->academicYear;
        $school = $report->school;

        $rawDetails = StudentGradeDetail::with('subject')
            ->whereIn('student_grade_id', function ($query) use ($report) {
                $query->select('id')
                    ->from('student_grades')
                    ->where('student_id', $report->student_id)
                    ->where('class_id', $report->class_id)
                    ->where('academic_year_id', $report->academic_year_id);
            })
            ->get();

        // Group by subject ID and calculate average
        $details = $rawDetails->groupBy('subject_id')->map(function ($group) {
            $first = $group->first(); // Ambil info subject dsb dari entri pertama
            $averageScore = round($group->avg('score'), 2); // Rata-rata nilai

            // Clone to reuse view binding
            $first->score = $averageScore;
            return $first;
        })->values();

        $fase = $details->first()?->fase ?? '-';

        $totalStudents = \App\Models\StudentGrade::query()
            ->where('class_id', $report->class_id)
            ->where('academic_year_id', $report->academic_year_id)
            ->select('student_id')
            ->distinct()
            ->count();

        $wali = ClassTeacherAssignment::with('teacher')
            ->where('class_id', $report->class_id)
            ->where('academic_year_id', $report->academic_year_id)
            ->where('is_wali', true)
            ->first();

        $homeroomName = $wali?->teacher?->name ?? '_________________';
        $homeroomNip  = $wali?->teacher?->nip ?? 'NIP/NUPTK/NBM';

        $summaryHtml = view('report.summaryPDF', compact(
            'student',
            'schoolClass',
            'academicYear',
            'details',
            'fase',
            'school',
            'totalStudents',
            'homeroomName',
            'homeroomNip'
        ))->render();

        $attachmentHtml = view('report.attachmentPDF', compact(
            'student',
            'schoolClass',
            'academicYear',
            'details',
            'fase',
            'school'
        ))->render();

        $fullHtml = $summaryHtml . '<!--pagebreak-->' . $attachmentHtml;

        $pdf = Pdf::loadHTML($fullHtml)->setPaper('F4', 'portrait');

        $year = str_replace('/', '-', $academicYear->year);
        return $pdf->stream("Preview Rapor Semester {$academicYear->semester} - {$year} - {$student->name}.pdf");
    }
}
