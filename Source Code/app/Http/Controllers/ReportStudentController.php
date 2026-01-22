<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\ClassTeacherAssignment;
use App\Models\Student;
use App\Models\StudentGrade;
use App\Models\StudentGradeDetail;
use App\Models\StudentReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class ReportStudentController extends Controller
{
    public function index(Request $request)
    {
        $student = auth()->user()->student ?? Student::where('user_id', auth()->id())->firstOrFail();

        if (!$student->grades()->exists()) {
            $reports = new LengthAwarePaginator([], 0, 10, 1);
            $academicYears = AcademicYear::latest('year')->get();
            return view('report.student', compact('reports', 'student', 'academicYears'));
        }

        $base = StudentGrade::query()
            ->selectRaw('academic_year_id, class_id, MAX(id) as sample_grade_id')
            ->where('student_id', $student->id)
            ->whereNull('deleted_at')
            ->groupBy('academic_year_id', 'class_id');

        if ($request->filled('academic_year')) {
            $base->where('academic_year_id', $request->academic_year);
        }

        $query = DB::table(DB::raw("({$base->toSql()}) as grouped"))
            ->mergeBindings($base->getQuery())
            ->join('academic_years', 'grouped.academic_year_id', '=', 'academic_years.id')
            ->join('school_classes', 'grouped.class_id', '=', 'school_classes.id')
            ->leftJoin('student_grades', 'grouped.sample_grade_id', '=', 'student_grades.id')
            ->select(
                'grouped.sample_grade_id as sample_id',
                'academic_years.year as academic_year',
                'academic_years.semester',
                'school_classes.name as class_name'
            )
            ->orderByDesc('academic_years.year')
            ->orderByDesc('academic_years.semester');

        if ($request->filled('semester')) {
            $query->where('academic_years.semester', $request->semester);
        }

        $perPage = 10;
        $page = $request->integer('page', 1);
        $results = $query->forPage($page, $perPage)->get();
        $total = $query->count();

        $reports = new LengthAwarePaginator($results, $total, $perPage, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'query' => $request->query()
        ]);

        $sampleIds = $results->pluck('sample_id')->filter()->values();
        $hasDetails = [];

        if ($sampleIds->isNotEmpty()) {
            $rows = DB::table('student_grade_details')
                ->select('student_grade_id', DB::raw('COUNT(*) as cnt'))
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

        $academicYears = AcademicYear::latest('year')->get();

        return view('report.student', compact('reports', 'student', 'academicYears'));
    }

    public function export(StudentGrade $grade)
    {
        $student = auth()->user()->student ?? Student::where('user_id', auth()->id())->firstOrFail();
        abort_if($grade->student_id !== $student->id, 403);

        $report = new StudentReport([
            'student_id' => $grade->student_id,
            'class_id' => $grade->class_id,
            'academic_year_id' => $grade->academic_year_id,
            'semester' => $grade->academicYear->semester,
        ]);

        $school = $report->school;
        $academicYear = $report->academicYear;
        $schoolClass = $report->schoolClass;
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

        $totalStudents = StudentGrade::where('class_id', $report->class_id)
            ->where('academic_year_id', $report->academic_year_id)
            ->select('student_id')->distinct()->count();

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

    public function preview(StudentGrade $grade)
    {
        $student = auth()->user()->student ?? Student::where('user_id', auth()->id())->firstOrFail();
        abort_if($grade->student_id !== $student->id, 403);

        $report = new StudentReport([
            'student_id' => $grade->student_id,
            'class_id' => $grade->class_id,
            'academic_year_id' => $grade->academic_year_id,
            'semester' => $grade->academicYear->semester,
        ]);

        $school = $report->school;
        $academicYear = $report->academicYear;
        $schoolClass = $report->schoolClass;
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

        $totalStudents = StudentGrade::where('class_id', $report->class_id)
            ->where('academic_year_id', $report->academic_year_id)
            ->select('student_id')->distinct()->count();

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
