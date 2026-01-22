<?php

namespace App\Http\Controllers;

use App\Models\{AcademicYear, SchoolClass, StudentGrade, StudentGradeDetail, StudentReport, ClassTeacherAssignment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportAdminController extends Controller
{
    public function index(Request $request)
    {
        $base = \DB::table('student_grades')
            ->selectRaw('student_id, academic_year_id, class_id, MAX(id) as sample_grade_id')
            ->whereNull('student_grades.deleted_at')
            ->groupBy('student_id', 'academic_year_id', 'class_id');

        if ($request->filled('academic_year')) {
            $base->where('academic_year_id', $request->academic_year);
        }

        if ($request->filled('class_id')) {
            $base->where('class_id', $request->class_id);
        }

        $query = \DB::table(\DB::raw('(' . $base->toSql() . ') as grouped'))
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
                'academic_years.semester as semester',
                'grouped.sample_grade_id as sample_id'
            )
            ->orderBy('academic_years.year', 'desc')
            ->orderBy('academic_years.semester', 'desc');

        if ($request->filled('semester')) {
            $query->where('academic_years.semester', $request->semester);
        }

        $perPage = 15;
        $page = $request->input('page', 1);
        $results = $query->forPage($page, $perPage)->get();
        $total = $query->count();

        $reports = new \Illuminate\Pagination\LengthAwarePaginator(
            $results,
            $total,
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );

        $sampleIds = $results->pluck('sample_id')->filter()->all();
        $hasDetails = [];
        if (!empty($sampleIds)) {
            $rows = \DB::table('student_grade_details')
                ->select('student_grade_id', \DB::raw('count(*) as cnt'))
                ->whereIn('student_grade_id', $sampleIds)
                ->groupBy('student_grade_id')
                ->pluck('cnt', 'student_grade_id')
                ->toArray();

            $hasDetails = array_filter($rows, fn($v) => $v > 0);
        }

        $reports->getCollection()->transform(function ($item) use ($hasDetails) {
            $item->has_report = isset($hasDetails[$item->sample_id]);
            return $item;
        });

        $academicYears = AcademicYear::orderBy('year', 'desc')->get();
        $classes = SchoolClass::orderBy('name')->get();

        return view('report.admin', compact('reports', 'academicYears', 'classes'));
    }

    public function export($id)
    {
        $grade = StudentGrade::with('academicYear')->findOrFail($id);

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

        $totalStudents = StudentGrade::query()
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

        $totalStudents = StudentGrade::query()
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
        return $pdf->stream("Rapor Semester {$academicYear->semester} - {$year} - {$student->name}.pdf");
    }
}
