<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{
    SchoolClass,
    Teacher,
    Student,
    AcademicYear,
    ClassStudentAssignment,
    ClassTeacherAssignment
};
use App\Http\Requests\ClassAssignmentRequest;
use Illuminate\Support\Facades\DB;

class ClassAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $academicYears = AcademicYear::orderByDesc('year')->get();
        $classes = SchoolClass::orderBy('name')->get();

        $assignments = ClassTeacherAssignment::groupedWithStudentCounts(
            $request->input('q'),
            $request->input('year'),
            $request->input('class'),
            $request->input('student')
        );

        return view('admin.class_assignments.index', compact('assignments', 'academicYears', 'classes'));
    }

    public function create()
    {
        $classes = SchoolClass::all();
        $teachers = Teacher::all();
        $students = Student::all();
        $academicYears = AcademicYear::all();

        return view('admin.class_assignments.create', compact('classes', 'teachers', 'students', 'academicYears'));
    }

    public function store(ClassAssignmentRequest $request)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data) {
            $assignment = ClassTeacherAssignment::create([
                'class_id' => $data['class_id'],
                'teacher_id' => $data['teacher_id'],
                'academic_year_id' => $data['academic_year_id'],
                'is_wali' => $data['is_wali'] ?? false,
            ]);

            foreach ($data['students'] as $studentId) {
                ClassStudentAssignment::firstOrCreate([
                    'student_id' => $studentId,
                    'class_id' => $data['class_id'],
                    'academic_year_id' => $data['academic_year_id'],
                ]);
            }
        });

        return redirect()->route('admin.class_assignments.index')
            ->with('success', 'Mapping berhasil disimpan.');
    }

    public function edit($id)
    {
        $assignment = ClassTeacherAssignment::withTrashed()
            ->with(['academicYear'])
            ->findOrFail($id);

        $classes = SchoolClass::all();
        $teachers = Teacher::all();
        $students = Student::all();

        $assignedStudentIds = ClassStudentAssignment::where('class_id', $assignment->class_id)
            ->where('academic_year_id', $assignment->academic_year_id)
            ->pluck('student_id')
            ->toArray();

        $selected = Student::whereIn('id', $assignedStudentIds)->get();

        return view('admin.class_assignments.edit', compact(
            'assignment',
            'classes',
            'teachers',
            'students',
            'selected',
            'assignedStudentIds'
        ));
    }

    public function update(ClassAssignmentRequest $request, $id)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $id) {
            $assignment = ClassTeacherAssignment::withTrashed()->findOrFail($id);

            $assignment->update([
                'class_id' => $data['class_id'],
                'teacher_id' => $data['teacher_id'],
                'academic_year_id' => $data['academic_year_id'],
                'is_wali' => $data['is_wali'] ?? false,
            ]);

            ClassStudentAssignment::where('class_id', $data['class_id'])
                ->where('academic_year_id', $data['academic_year_id'])
                ->delete();

            foreach ($data['students'] as $studentId) {
                ClassStudentAssignment::create([
                    'student_id' => $studentId,
                    'class_id' => $data['class_id'],
                    'academic_year_id' => $data['academic_year_id'],
                ]);
            }
        });

        return redirect()->route('admin.class_assignments.index')
            ->with('success', 'Mapping berhasil diperbarui.');
    }

    /**
     * Soft delete assignment
     */
    public function destroy($id)
    {
        $assignment = ClassTeacherAssignment::findOrFail($id);

        $assignment->delete();

        return redirect()->route('admin.class_assignments.index')
            ->with('success', 'Mapping berhasil dihapus sementara.');
    }

    /**
     * Restore soft deleted assignment
     */
    public function restore($id)
    {
        $assignment = ClassTeacherAssignment::onlyTrashed()->findOrFail($id);

        $assignment->restore();

        return redirect()->route('admin.class_assignments.index')
            ->with('success', 'Mapping berhasil dipulihkan.');
    }

    /**
     * Permanently delete assignment
     */
    public function forceDelete($id)
    {
        $assignment = ClassTeacherAssignment::onlyTrashed()->findOrFail($id);

        $assignment->forceDelete();

        return redirect()->route('admin.class_assignments.index')
            ->with('success', 'Mapping berhasil dihapus permanen.');
    }
}
