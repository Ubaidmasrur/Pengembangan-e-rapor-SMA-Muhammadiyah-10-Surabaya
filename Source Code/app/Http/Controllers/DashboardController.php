<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\ClassStudentAssignment;
use App\Models\AcademicYear;
use App\Models\StudentGrade;
use App\Models\StudentGradeDetail;
use App\Models\Subject;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $role = auth()->user()->role ?? 'guest';

        if ($role === 'admin') {
            $totalStudents = Student::count();
            $totalTeachers = Teacher::count();
            $reportsCreated = 0;
            $reportsPending = 0;
            $activities = Activity::whereDate('activity_date', '>=', Carbon::today())
                ->orderBy('activity_date')
                ->take(5)
                ->get();

            // Grafik Perkembangan: rata-rata nilai per-tipe subject (umum, khusus, ekstra) per semester
            $academicYears = AcademicYear::orderByDesc('year')->orderByDesc('semester')->take(4)->get()->sortBy(function($y) {
                return $y->year . ($y->semester === 'Ganjil' ? '1' : '2');
            });
            $perkembanganLabels = [];
            $subjectTypes = ['umum', 'khusus', 'ekstra'];
            $perkembanganDatasets = [];
            foreach ($subjectTypes as $type) {
                $data = [];
                foreach ($academicYears as $year) {
                    $label = $year->year . ' ' . $year->semester;
                    if (!in_array($label, $perkembanganLabels)) {
                        $perkembanganLabels[] = $label;
                    }
                    $subjectIds = \App\Models\Subject::where('type', $type)->pluck('id');
                    $avgScore = StudentGradeDetail::whereIn('subject_id', $subjectIds)
                        ->whereHas('studentGrade', function($q) use ($year) {
                            $q->where('academic_year_id', $year->id);
                        })->avg('score');
                    $data[] = $avgScore ? round($avgScore, 2) : 0;
                }
                $perkembanganDatasets[] = [
                    'label' => ucfirst($type),
                    'data' => $data,
                ];
            }

            // Grafik Perbandingan: rata-rata motorik, kognitif, sosial per semester
            $perbandinganLabels = ['Motorik', 'Kognitif', 'Sosial'];
            $perbandinganDatasets = [];
            foreach ($academicYears as $year) {
                $avgMotorik = StudentGrade::where('academic_year_id', $year->id)->avg('motorik');
                $avgKognitif = StudentGrade::where('academic_year_id', $year->id)->avg('kognitif');
                $avgSosial = StudentGrade::where('academic_year_id', $year->id)->avg('sosial');
                $perbandinganDatasets[] = [
                    'label' => $year->year . ' ' . $year->semester,
                    'data' => [
                        $avgMotorik ? round($avgMotorik, 2) : 0,
                        $avgKognitif ? round($avgKognitif, 2) : 0,
                        $avgSosial ? round($avgSosial, 2) : 0,
                    ],
                ];
            }

            return view('dashboard.admin', [
                'totalStudents' => $totalStudents,
                'totalTeachers' => $totalTeachers,
                'reportsCreated' => $reportsCreated,
                'reportsPending' => $reportsPending,
                'activities' => $activities,
                'perkembanganLabels' => $perkembanganLabels,
                'perkembanganDatasets' => $perkembanganDatasets,
                'perbandinganLabels' => $perbandinganLabels,
                'perbandinganDatasets' => $perbandinganDatasets,
            ]);
        }

        if ($role === 'guru') {
            // teacher dashboard logic
            $user = auth()->user();
            $teacher = null;
            if (method_exists($user, 'teacher') && $user->teacher) {
                $teacher = $user->teacher;
            } else {
                $teacher = \App\Models\Teacher::where('user_id', $user->id)->first();
            }

            $assignments = $teacher ? $teacher->classTeacherAssignments()->with(['class', 'academicYear'])->get() : collect();

            // choose primary class (first assignment) and current academic year (most recent)
            $primaryClassId = $assignments->first() ? $assignments->first()->class_id : null;
            $currentAcademicYear = AcademicYear::orderByDesc('id')->first();

            // students assigned to the primary class in current academic year — limit to top 3
            $students = collect();
            if ($primaryClassId && $currentAcademicYear) {
                $students = ClassStudentAssignment::where('class_id', $primaryClassId)
                    ->where('academic_year_id', $currentAcademicYear->id)
                    ->join('students', 'class_student_assignments.student_id', '=', 'students.id')
                    ->select('students.id', 'students.name')
                    ->distinct()
                    ->orderBy('students.name')
                    ->limit(3)
                    ->get();
            }

            // total students across assignments (for stats)
            $totalStudents = $assignments->count() ? ClassStudentAssignment::whereIn('class_id', $assignments->pluck('class_id')->toArray())->distinct('student_id')->count('student_id') : 0;

            // reports/completed: count of student_grade_details authored by this teacher
            $reportsCompleted = 0;
            $reportsPending = 0;
            if ($teacher) {
                // count of detail rows authored by this teacher via master relation
                $reportsCompleted = StudentGradeDetail::whereHas('studentGrade', function($q) use ($teacher) {
                    $q->where('teacher_id', $teacher->id);
                })->count();

                // number of distinct students for which this teacher has masters
                $distinctMasters = StudentGrade::where('teacher_id', $teacher->id)
                    ->distinct()
                    ->count('student_id');

                $reportsPending = max(0, $totalStudents - $distinctMasters);
            }

            // recent reports (latest details)
            $recentReports = collect();
            if ($teacher) {
                // include the master student_grade id so the dashboard can link to the full view and open that master
                $recentReports = StudentGradeDetail::with(['studentGrade.student', 'subject'])
                    ->whereHas('studentGrade', function($q) use ($teacher) {
                        $q->where('teacher_id', $teacher->id);
                    })
                    ->orderByDesc('created_at')
                    ->limit(10)
                    ->get()
                    ->map(function($d) {
                        return (object) [
                            'student_name' => optional($d->studentGrade->student)->name ?? 'Siswa',
                            'created_at' => $d->created_at,
                            'subject_name' => optional($d->subject)->name ?? '-',
                            'score' => $d->score,
                            'student_grade_id' => $d->student_grade_id,
                            'student_id' => optional($d->studentGrade)->student_id,
                        ];
                    });
            }

            // per-student average progress (simple overall average)
            $studentProgress = [];
            foreach ($students as $s) {
                $avg = StudentGradeDetail::whereHas('studentGrade', function($q) use ($s) {
                    $q->where('student_grades.student_id', $s->id);
                })->avg('score');
                $studentProgress[$s->id] = $avg ? round($avg) : 0;
            }

            return view('dashboard.teacher', compact('assignments', 'students', 'totalStudents', 'reportsCompleted', 'reportsPending', 'recentReports', 'studentProgress'));
        }

        if ($role === 'siswa' || $role === 'wali') {
            // student dashboard logic: load student profile, activities, and progress metrics
            $user = auth()->user();
            $student = null;
            if (method_exists($user, 'student') && $user->student) {
                $student = $user->student;
            } else {
                // fallback: try to find a student record by user_id
                $student = Student::where('user_id', $user->id)->first();
            }

            // upcoming activities
            $activities = Activity::whereDate('activity_date', '>=', Carbon::today())
                ->orderBy('activity_date')
                ->take(5)
                ->get();

            // prepare recent reports (top 3) and master summary
            $recentReports = collect();
            $master = null;
            $kognitif = $motorik = $sosial = null;
            $hasProgress = false;
            $summary = null;

            if ($student) {
                // Fetch recent student grades and collapse duplicates by month-year
                $grades = StudentGrade::where('student_id', $student->id)
                    ->orderByDesc('updated_at')
                    ->get();

                // Group grades by year-month (based on updated_at) and take the latest entry per month
                $grouped = $grades->groupBy(function ($g) {
                    return optional($g->updated_at)->format('Y-m');
                })->map(function ($group) {
                    $g = $group->first();
                    return (object) [
                        'id' => $g->id,
                        'month_label' => optional($g->updated_at) ? \Carbon\Carbon::parse($g->updated_at)->format('F Y') : null,
                        'notes' => $g->notes ?? null,
                        'kognitif' => $g->kognitif,
                        'motorik' => $g->motorik,
                        'sosial' => $g->sosial,
                        'status' => $g->status ?? null,
                        'student_id' => $g->student_id,
                        'created_at' => $g->created_at,
                    ];
                })->values();

                $recentReports = $grouped->take(3);

                    $master = StudentGrade::where('student_id', $student->id)->latest()->first();
                if ($master) {
                    $motorik = $master->motorik !== null ? round($master->motorik, 2) : null;
                    $kognitif = $master->kognitif !== null ? round($master->kognitif, 2) : null;
                    $sosial = $master->sosial !== null ? round($master->sosial, 2) : null;
                    $hasProgress = $motorik !== null || $kognitif !== null || $sosial !== null;
                    $summary = $master->notes ?? null;
                }

                if (!$hasProgress) {
                    $avg = StudentGradeDetail::whereHas('studentGrade', function($q) use ($student) {
                        $q->where('student_grades.student_id', $student->id);
                    })->avg('score');
                    if ($avg !== null) {
                        $hasProgress = true;
                        $kognitif = $motorik = $sosial = round($avg, 2);
                    }
                }
            }

                // Compute class name, age and active guardian (teacher) server-side to keep the view simple
                $className = '—';
                $age = null;
                $guardianName = null;
                $academicYearLabel = null;
                if ($student) {
                    // className via accessor if available
                    $className = $student->class ? ($student->class->name ?? '—') : (optional($student)->class_name ?? '—');

                    // age from birth_date
                    if (!empty($student->birth_date)) {
                        try {
                            $age = \Carbon\Carbon::parse($student->birth_date)->age;
                        } catch (\Exception $e) {
                            $age = null;
                        }
                    }

                    // Find latest student assignment and then the class-teacher assignment for same class+academic year
                    $assignment = \App\Models\ClassStudentAssignment::where('student_id', $student->id)
                        ->orderByDesc('id')
                        ->first();
                    if ($assignment) {
                        // derive academic year label
                        if ($assignment->academicYear) {
                            $academicYearLabel = optional($assignment->academicYear)->year ? (optional($assignment->academicYear)->year . ' ' . (optional($assignment->academicYear)->semester === 1 ? 'Ganjil' : 'Genap')) : null;
                        }
                        $cta = \App\Models\ClassTeacherAssignment::where('class_id', $assignment->class_id)
                            ->where('academic_year_id', $assignment->academic_year_id)
                            ->whereNull('deleted_at')
                            ->first();
                        if ($cta && $cta->teacher) {
                            $guardianName = $cta->teacher->name;
                        }
                    }
                }

                // Prepare semester averages (IPK) for Perkembangan Bulanan
                $semesterAverages = collect();
                if ($student) {
                    $grades = StudentGrade::where('student_id', $student->id)
                        ->with('academicYear')
                        ->orderByDesc('updated_at')
                        ->get();

                    $uniqueByYear = $grades->unique('academic_year_id')->values();
                    $semesterAverages = $uniqueByYear->map(function ($g) {
                        // Prefer master kognitif/motorik/sosial if available
                        $parts = [];
                        if ($g->kognitif !== null) $parts[] = $g->kognitif;
                        if ($g->motorik !== null) $parts[] = $g->motorik;
                        if ($g->sosial !== null) $parts[] = $g->sosial;
                        if (count($parts)) {
                            $ipk = round(array_sum($parts) / count($parts), 2);
                        } else {
                            $ipk = round(
                                \App\Models\StudentGradeDetail::where('student_grade_id', $g->id)->avg('score') ?? 0
                            , 2);
                        }

                        $label = $g->academicYear ? (optional($g->academicYear)->year . ' ' . (optional($g->academicYear)->semester === 1 ? 'Ganjil' : 'Genap')) : null;
                        return (object) [
                            'academic_year_id' => $g->academic_year_id,
                            'label' => $label,
                            'ipk' => $ipk,
                            'updated_at' => $g->updated_at,
                        ];
                    })->values()->take(6);
                }

                // Prepare area capability history for last 6 months (motorik, kognitif, sosial)
                $areaHistory = collect();
                if ($student) {
                    $start = \Carbon\Carbon::now()->subMonths(6)->startOfMonth();
                    $gradesRecent = StudentGrade::where('student_id', $student->id)
                        ->where('updated_at', '>=', $start)
                        ->orderByDesc('updated_at')
                        ->get();

                    // group by year-month
                    $groups = $gradesRecent->groupBy(function ($g) {
                        return optional($g->updated_at)->format('Y-m');
                    })->map(function ($group, $key) {
                        // compute averages for master fields if present
                        $motorikVals = $group->pluck('motorik')->filter(function ($v) { return $v !== null; })->all();
                        $kognitifVals = $group->pluck('kognitif')->filter(function ($v) { return $v !== null; })->all();
                        $sosialVals = $group->pluck('sosial')->filter(function ($v) { return $v !== null; })->all();

                        $motorik = count($motorikVals) ? round(array_sum($motorikVals) / count($motorikVals), 2) : null;
                        $kognitif = count($kognitifVals) ? round(array_sum($kognitifVals) / count($kognitifVals), 2) : null;
                        $sosial = count($sosialVals) ? round(array_sum($sosialVals) / count($sosialVals), 2) : null;

                        // if master fields missing, fall back to detail average per group
                        if ($motorik === null || $kognitif === null || $sosial === null) {
                            $detailAvg = StudentGradeDetail::whereIn('student_grade_id', $group->pluck('id')->all())->avg('score');
                            $fallback = $detailAvg !== null ? round($detailAvg, 2) : 0;
                            $motorik = $motorik ?? $fallback;
                            $kognitif = $kognitif ?? $fallback;
                            $sosial = $sosial ?? $fallback;
                        }

                        $first = $group->first();
                        $label = optional($first->updated_at) ? \Carbon\Carbon::parse($first->updated_at)->format('M Y') : $key;
                        return (object)[
                            'label' => $label,
                            'motorik' => $motorik,
                            'kognitif' => $kognitif,
                            'sosial' => $sosial,
                        ];
                    })->values();

                    // ensure we have exactly up to 6 months in chronological order
                    $areaHistory = $groups->sortBy(function($g){ return \Carbon\Carbon::parse($g->label)->format('Y-m'); })->values();
                    if ($areaHistory->count() > 6) {
                        $areaHistory = $areaHistory->slice(0, 6);
                    }
                }

    return view('dashboard.student', compact('student', 'activities', 'recentReports', 'master', 'kognitif', 'motorik', 'sosial', 'hasProgress', 'summary', 'className', 'age', 'guardianName', 'academicYearLabel', 'semesterAverages', 'areaHistory'));
        }

        // Default guest or unknown role
        return view('auth.login');
    }
}
