<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\DashboardController;
use \App\Http\Controllers\GradingController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/guru/grading', [GradingController::class, 'gradeInput'])
    ->middleware(['auth', 'verified'])
    ->name('grading.gradeInput');

// Store grade (used by modal form)
Route::post('/grades', [GradingController::class, 'store'])
    ->middleware(['auth', 'verified'])
    ->name('grades.store');

// Bulk store pending grades
Route::post('/grades/bulk', [GradingController::class, 'bulkStore'])
    ->middleware(['auth', 'verified'])
    ->name('grades.bulkStore');

// Global AJAX endpoints used by multiple areas (require auth)
Route::middleware(['auth', 'verified'])->group(function () {
    // Use school class controller to populate classes
    Route::get('/ajax/classes', [\App\Http\Controllers\SchoolClassController::class, 'getSchoolClassData'])
        ->name('ajax.classes');
    // Use student controller to populate students
    Route::get('/ajax/students', [\App\Http\Controllers\StudentController::class, 'getStudentData'])
        ->name('ajax.students');

    // Grade parameter lookup for AJAX (compute grade letter)
    Route::get('/ajax/grade-parameters/lookup', [\App\Http\Controllers\GradeParameterController::class, 'lookup'])
        ->name('ajax.grade_parameters.lookup');
});

require __DIR__.'/auth.php';
require __DIR__.'/grading.php';
