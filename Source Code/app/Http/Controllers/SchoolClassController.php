<?php

namespace App\Http\Controllers;

use App\Http\Requests\SchoolClassRequest;
use App\Models\SchoolClass;
use Illuminate\Http\Request;

class SchoolClassController extends Controller
{
    public function index(Request  $request)
    {
        $query = SchoolClass::withTrashed();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            });
        }

        $school_classes = $query->paginate(15)->appends(['q' => $request->input('q')]);
        return view('admin.school_classes.index', compact('school_classes'));
    }

    public function create()
    {
    $schools = \App\Models\School::all();
    return view('admin.school_classes.create', compact('schools'));
    }

    public function store(SchoolClassRequest $request)
    {
    SchoolClass::create($request->validated());
    return redirect()->route('admin.school_classes.index')->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function edit(SchoolClass $school_class)
    {
    $schools = \App\Models\School::all();
    return view('admin.school_classes.edit', compact('school_class', 'schools'));
    }

    public function update(SchoolClassRequest $request, SchoolClass $school_class)
    {
    $school_class->update($request->validated());
    return redirect()->route('admin.school_classes.index')->with('success', 'Kelas berhasil diperbarui.');
    }

    public function destroy(SchoolClass $school_class)
    {
        $school_class->delete();
        return redirect()->route('admin.school_classes.index')->with('success', 'Kelas berhasil dihapus.');
    }

    public function restore($id)
    {
        $school_class = SchoolClass::withTrashed()->findOrFail($id);
        $school_class->restore();

        return redirect()->route('admin.school_classes.index')->with('success', 'Kelas berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $school_class = SchoolClass::withTrashed()->findOrFail($id);
        $school_class->forceDelete();

        return redirect()->route('admin.school_classes.index')->with('success', 'Kelas dihapus permanen.');
    }

    //function to populate school class from parameter search (school, academic year)
    public function getSchoolClassData(Request $request)
    {
        //if search by school or academic year, else get all
        $school_id = $request->input('school_id', '');
        $academic_year_id = $request->input('academic_year_id', '');

        $query = SchoolClass::query();

        if ($school_id) {
            $query->where('school_id', $school_id);
        }

        if ($academic_year_id) {
            $query->where('academic_year_id', $academic_year_id);
        }

        $school_classes = $query->get();

        return response()->json($school_classes);
    }

}
