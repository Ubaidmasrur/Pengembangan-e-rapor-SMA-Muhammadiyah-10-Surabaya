<?php

use App\Http\Controllers\ReportStudentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:siswa'])
    ->prefix('siswa')
    ->name('siswa.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('siswa.dashboard'))->name('dashboard');
        // Full laporan list for siswa with optional academic year filter
        Route::get('/laporan', function () {
            $user = auth()->user();
            $student = null;
            if (method_exists($user, 'student') && $user->student) $student = $user->student;
            if (!$student) $student = \App\Models\Student::where('user_id', $user->id)->first();

            $years = \App\Models\AcademicYear::orderByDesc('id')->get();
            $yearId = request()->query('academic_year_id');

            $query = \App\Models\StudentGrade::query()->where('student_id', $student->id);
            if ($yearId) $query->where('academic_year_id', $yearId);
            $masters = $query->orderByDesc('updated_at')->withCount('details')->paginate(10)->withQueryString();

            return view('siswa.laporan.index', compact('student', 'years', 'masters', 'yearId'));
        })->name('laporan.index');

        // Student-specific report download/preview (uses ReportStudentController)
        Route::get('/reports', [ReportStudentController::class, 'index'])->name('reports.index');
        Route::get('/reports/{grade}/export', [ReportStudentController::class, 'export'])->name('reports.export');
        Route::get('/reports/{grade}/preview', [ReportStudentController::class, 'preview'])->name('reports.preview');
    });
