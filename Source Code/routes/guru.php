<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\TeacherController;
use \App\Http\Controllers\GradingController;
use App\Http\Controllers\ReportTeacherController;

Route::middleware(['auth', 'role:guru', 'verified'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('dashboard.teacher'))->name('dashboard');

        Route::get('/class_member', [TeacherController::class, 'getClassStudent'])
            ->name('class_member');

        // Student grades index (shows dropdowns and optionally grades) - handled by GradingController
        Route::get('/studentgrades', [GradingController::class, 'showStudentGrades'])
            ->name('studentgrades.index');

        // Show student grades (by id) - teacher view for input/overview
        Route::get('/grades/{student}', [TeacherController::class, 'showStudentGrades'])
            ->name('grade.studentgrades');

        // Teacher-facing full history page for a student (read-only)
        Route::get('/student/{student}/history', [TeacherController::class, 'studentHistory'])
            ->name('student.history');

        // Additional named route for templates that expect 'guru.history.grades'
        Route::get('/student/{student}/history/grades', [TeacherController::class, 'studentHistory'])
            ->name('history.grades');

        // Teacher-facing reports (limited to classes the teacher is assigned to)
        Route::get('/reports', [\App\Http\Controllers\ReportTeacherController::class, 'index'])
            ->name('report.index');
        Route::get('/preview-monthly', [ReportTeacherController::class, 'previewByMonthYear'])
            ->name('report.preview.month');
        Route::get('/report/export/{id}', [ReportTeacherController::class, 'export'])
            ->name('report.export');
        Route::get('/reports/{id}/preview', [\App\Http\Controllers\ReportTeacherController::class, 'preview'])
            ->name('report.preview');
    });
