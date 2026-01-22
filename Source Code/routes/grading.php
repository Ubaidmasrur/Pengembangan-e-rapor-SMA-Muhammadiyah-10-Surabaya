<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GradingController;

Route::middleware(['auth', 'role:guru', 'verified'])
    ->prefix('guru')
    ->name('guru.')
    ->group(function () {
        // Grading page and actions
        Route::get('/grading', [GradingController::class, 'gradeInput'])->name('grading.gradeInput');
        Route::post('/grades', [GradingController::class, 'store'])->name('grades.store');
        Route::post('/grades/bulk', [GradingController::class, 'bulkStore'])->name('grades.bulkStore');
    // Update and delete individual grade detail rows
    Route::put('/grades/{id}', [GradingController::class, 'updateDetail'])->name('grades.update');
    Route::delete('/grades/{id}', [GradingController::class, 'destroyDetail'])->name('grades.destroy');

        // Student grades listing (delegated to GradingController)
        Route::get('/studentgrades', [GradingController::class, 'showStudentGrades'])->name('studentgrades.index');
    // detail route renamed to avoid duplicate route name with teacher routes
    Route::get('/studentgrades/{id}', [GradingController::class, 'showStudentGrades'])->name('grade.studentgrades.show');
    // AJAX grade lookup
    Route::get('/grading/grade-letter', [GradingController::class, 'getGradeLetter'])->name('grading.getGradeLetter');
    Route::get('/grading/master', [GradingController::class, 'getMasterRecord'])->name('grading.getMasterRecord');
    Route::get('/grading/grades', [GradingController::class, 'getGrades'])->name('grading.getGrades');
    });

// Read-only student grades page (master-detail) for any authenticated user
Route::middleware(['auth', 'verified'])
    ->get('/my/grades', [GradingController::class, 'myGrades'])->name('student.grades');
