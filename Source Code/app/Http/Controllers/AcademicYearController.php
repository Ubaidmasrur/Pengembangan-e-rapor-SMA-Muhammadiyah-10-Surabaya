<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Http\Requests\AcademicYearRequest;
use Illuminate\Http\Request;

class AcademicYearController extends Controller
{
    public function index(Request $request)
    {
        $query = AcademicYear::withTrashed();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $q->where('year', 'like', '%' . $search . '%')
                ->orWhere('semester', 'like', '%' . $search . '%')
                ->orWhere('start_month', 'like', '%' . $search . '%')
                ->orWhere('end_month', 'like', '%' . $search . '%');
        }

        $academicYears = $query->paginate(15)->appends(['q' => $request->input('q')]);
        return view('admin.academic_years.index', compact('academicYears'));
    }

    public function create()
    {
        return view('admin.academic_years.create');
    }

    public function store(AcademicYearRequest $request)
    {
        if ($request->is_active) {
            AcademicYear::where('is_active', true)->update(['is_active' => false]);
        }

        AcademicYear::create($request->validated());

        return redirect()->route('admin.academic_years.index')->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    public function edit(AcademicYear $academicYear)
    {
        return view('admin.academic_years.edit', compact('academicYear'));
    }

    public function update(AcademicYearRequest $request, AcademicYear $academicYear)
    {
        if ($request->is_active) {
            AcademicYear::where('is_active', true)->where('id', '!=', $academicYear->id)->update(['is_active' => false]);
        }

        $academicYear->update($request->validated());

        return redirect()->route('admin.academic_years.index')->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    public function destroy(AcademicYear $academicYear)
    {
        $academicYear->delete();
        return redirect()->route('admin.academic_years.index')->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    public function restore($id)
    {
        $academicYear = AcademicYear::withTrashed()->findOrFail($id);
        $academicYear->restore();

        return redirect()->route('admin.academic_years.index')->with('success', 'Tahun ajaran berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $academicYear = AcademicYear::withTrashed()->findOrFail($id);
        $academicYear->forceDelete();

        return redirect()->route('admin.academic_years.index')->with('success', 'Tahun ajaran dihapus permanen.');
    }

    // function to populate academic year from parameter
    public function getAcademicYearData(Request $request)
    {
        $academicYears = AcademicYear::all();
        return response()->json($academicYears);
    }
}
