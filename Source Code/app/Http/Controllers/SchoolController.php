<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Http\Requests\SchoolRequest;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index(Request $request)
    {
        $query = School::withTrashed();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('principal_name', 'like', '%' . $search . '%');
            });
        }

        $schools = $query->paginate(15)->appends(['q' => $request->input('q')]);
        return view('admin.schools.index', compact('schools'));
    }

    public function create()
    {
        return view('admin.schools.create');
    }

    public function store(SchoolRequest $request)
    {
        School::create($request->validated());
        return redirect()->route('admin.schools.index')->with('success', 'Sekolah berhasil ditambahkan.');
    }

    public function edit(School $school)
    {
        return view('admin.schools.edit', compact('school'));
    }

    public function update(SchoolRequest $request, School $school)
    {
        $school->update($request->validated());
        return redirect()->route('admin.schools.index')->with('success', 'Sekolah berhasil diperbarui.');
    }

    public function destroy(School $school)
    {
        $school->delete();
        return redirect()->route('admin.schools.index')->with('success', 'Sekolah berhasil dihapus.');
    }

    public function restore($id)
    {
        $school = School::withTrashed()->findOrFail($id);
        $school->restore();

        return redirect()->route('admin.schools.index')->with('success', 'Sekolah berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $school = School::withTrashed()->findOrFail($id);
        $school->forceDelete();

        return redirect()->route('admin.schools.index')->with('success', 'Sekolah dihapus permanen.');
    }
}
