<?php

use App\Http\Controllers\{
    AcademicYearController,
    ActivityController,
    ClassAssignmentController,
    GradeParameterController,
    GuardianController,
    SchoolClassController,
    SchoolController,
    StudentController,
    SubjectController,
    TeacherController,
    UserController
};
use App\Models\Activity;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');

        // Subjects
        Route::resource('subjects', SubjectController::class);
        Route::post('subjects/{id}/restore', [SubjectController::class, 'restore'])->name('subjects.restore');
        Route::delete('subjects/{id}/force-delete', [SubjectController::class, 'forceDelete'])->name('subjects.forceDelete');

        // Students
        Route::resource('students', StudentController::class);
        Route::post('students/{id}/restore', [StudentController::class, 'restore'])->name('students.restore');
        Route::delete('students/{id}/force-delete', [StudentController::class, 'forceDelete'])->name('students.forceDelete');

        // Teachers
        Route::resource('teachers', TeacherController::class);
        Route::post('teachers/{id}/restore', [TeacherController::class, 'restore'])->name('teachers.restore');
        Route::delete('teachers/{id}/force-delete', [TeacherController::class, 'forceDelete'])->name('teachers.forceDelete');

        // Guardians
        Route::resource('guardians', GuardianController::class);
        Route::post('guardians/{id}/restore', [GuardianController::class, 'restore'])->name('guardians.restore');
        Route::delete('guardians/{id}/force-delete', [GuardianController::class, 'forceDelete'])->name('guardians.forceDelete');

        // Users
        Route::resource('users', UserController::class)->except(['show']);
        Route::post('users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
        Route::delete('users/{id}/force-delete', [UserController::class, 'forceDelete'])->name('users.forceDelete');
        Route::get('users/get-entities', [UserController::class, 'getEntities'])->name('users.get-entities');

        // SchoolClasss
        Route::resource('school_classes', SchoolClassController::class);
        Route::post('school_classes/{id}/restore', [SchoolClassController::class, 'restore'])->name('school_classes.restore');
        Route::delete('school_classes/{id}/force-delete', [SchoolClassController::class, 'forceDelete'])->name('school_classes.forceDelete');

        // School
        Route::resource('schools', SchoolController::class);
        Route::post('schools/{id}/restore', [SchoolController::class, 'restore'])->name('schools.restore');
        Route::delete('schools/{id}/force-delete', [SchoolController::class, 'forceDelete'])->name('schools.forceDelete');

        // Grade Parameters
        Route::resource('grade_parameters', GradeParameterController::class);
        Route::post('grade_parameters/{id}/restore', [GradeParameterController::class, 'restore'])->name('grade_parameters.restore');
        Route::delete('grade_parameters/{id}/force-delete', [GradeParameterController::class, 'forceDelete'])->name('grade_parameters.forceDelete');

        // Academic Years
        Route::resource('academic_years', AcademicYearController::class);
        Route::post('academic_years/{id}/restore', [AcademicYearController::class, 'restore'])->name('academic_years.restore');
        Route::delete('academic_years/{id}/force-delete', [AcademicYearController::class, 'forceDelete'])->name('academic_years.forceDelete');

        // Class Assigment
        Route::resource('class_assignments', ClassAssignmentController::class)->except('show');
        Route::post('class_assignments/{id}/restore', [ClassAssignmentController::class, 'restore'])
            ->name('class_assignments.restore');
        Route::delete('class_assignments/{id}/force-delete', [ClassAssignmentController::class, 'forceDelete'])
            ->name('class_assignments.forceDelete');

        // Activity
        Route::resource('activities', ActivityController::class)->names('activities');
        Route::post('activities/{id}/restore', [ActivityController::class, 'restore'])->name('activities.restore');
        Route::delete('activities/{id}/force-delete', [ActivityController::class, 'forceDelete'])->name('activities.forceDelete');

        // Reports (Admin view)
        Route::get('reports', [\App\Http\Controllers\ReportAdminController::class, 'index'])->name('report.admin');
        Route::get('reports/{id}/export', [\App\Http\Controllers\ReportAdminController::class, 'export'])->name('report.export');
        Route::get('reports/{id}/preview', [\App\Http\Controllers\ReportAdminController::class, 'preview'])->name('report.preview');
    });
