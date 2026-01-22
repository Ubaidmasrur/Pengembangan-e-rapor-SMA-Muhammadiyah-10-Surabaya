<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentRequest;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::withTrashed();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('nisn', 'like', '%' . $search . '%')
                    ->orWhere('birth_date', 'like', '%' . $search . '%')
                    ->orWhere('disability_type', 'like', '%' . $search . '%');
            });
        }

        $students = $query->paginate(15)->appends(['q' => $request->input('q')]);

        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(StudentRequest $request)
    {
        $validated = $request->validated();

        // Simpan data siswa (tanpa membuat user)
        Student::create($validated);

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(Student $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    public function update(StudentRequest $request, Student $student)
    {
        $validated = $request->validated();

        // Update data siswa (tidak menyentuh tabel users)
        $student->update($validated);

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    public function restore($id)
    {
        $student = Student::withTrashed()->findOrFail($id);
        $student->restore();

        return redirect()->route('admin.students.index')->with('success', 'Data siswa berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $student = Student::withTrashed()->findOrFail($id);

        $student->forceDelete();

        return redirect()->route('admin.students.index')->with('success', 'Data siswa dihapus permanen.');
    }

    /**
     * Get filtered students by school, academic year, and class.
     */
    public function getStudentData(Request $request)
    {
        $school_id = $request->input('school_id', '');
        $academic_year_id = $request->input('academic_year_id', '');
        $class_id = $request->input('class_id', '');

        $query = Student::query();

        if ($school_id) {
            $query->where('school_id', $school_id);
        }

        if ($academic_year_id) {
            $query->where('academic_year_id', $academic_year_id);
        }

        if ($class_id) {
            $query->where('class_id', $class_id);
        }

        $students = $query->get();

        return response()->json($students);
    }
}
