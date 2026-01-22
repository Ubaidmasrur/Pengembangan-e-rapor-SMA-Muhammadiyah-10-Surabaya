<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use App\Http\Requests\SubjectRequest;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Subject::withTrashed();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('type', 'like', '%' . $search . '%');
            });
        }

        $subjects = $query->paginate(15)->appends(['q' => $request->input('q')]);
        return view('admin.subjects.index', compact('subjects'));
    }

    public function create()
    {
        return view('admin.subjects.create');
    }

    public function store(SubjectRequest $request)
    {
        Subject::create($request->validated());

        return redirect()->route('admin.subjects.index')->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    public function edit(Subject $subject)
    {
        return view('admin.subjects.edit', compact('subject'));
    }

    public function update(SubjectRequest $request, Subject $subject)
    {
        $subject->update($request->validated());

        return redirect()->route('admin.subjects.index')->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->route('admin.subjects.index')->with('success', 'Mata Pelajaran berhasil dihapus.');
    }

    public function restore($id)
    {
        $subject = Subject::withTrashed()->findOrFail($id);
        $subject->restore();

        return redirect()->route('admin.subjects.index')->with('success', 'Mata Pelajaran berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $subject = Subject::withTrashed()->findOrFail($id);
        $subject->forceDelete();

        return redirect()->route('admin.subjects.index')->with('success', 'Mata Pelajaran dihapus permanen.');
    }
}
