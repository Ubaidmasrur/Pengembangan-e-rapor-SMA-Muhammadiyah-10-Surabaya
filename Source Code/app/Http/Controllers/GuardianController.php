<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Http\Requests\GuardianRequest;

class GuardianController extends Controller
{
    public function index(Request $request)
    {
        $query = Guardian::withTrashed();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('telepon', 'like', '%' . $search . '%')
                    ->orWhereHas('students', function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', '%' . $search . '%');
                    });
            });
        }

        $guardians = $query->paginate(15)->appends(['q' => $request->input('q')]);
        return view('admin.guardians.index', compact('guardians'));
    }

    public function create()
    {
        $students = Student::all();
        return view('admin.guardians.create', compact('students'));
    }

    public function store(GuardianRequest $request)
    {
        Guardian::create($request->validated());
        return redirect()->route('admin.guardians.index')->with('success', 'Data wali berhasil ditambahkan.');
    }

    public function edit(Guardian $guardian)
    {
        $students = Student::all();
        return view('admin.guardians.edit', compact('guardian', 'students'));
    }

    public function update(GuardianRequest $request, Guardian $guardian)
    {
        $guardian->update($request->validated());
        return redirect()->route('admin.guardians.index')->with('success', 'Data wali berhasil diperbarui.');
    }

    public function destroy(Guardian $guardian)
    {
        $guardian->delete();
        return redirect()->route('admin.guardians.index')->with('success', 'Data wali berhasil dihapus.');
    }

    public function restore($id)
    {
        Guardian::withTrashed()->findOrFail($id)->restore();
        return redirect()->route('admin.guardians.index')->with('success', 'Data wali berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        Guardian::withTrashed()->findOrFail($id)->forceDelete();
        return redirect()->route('admin.guardians.index')->with('success', 'Data wali berhasil dihapus permanen.');
    }
}
