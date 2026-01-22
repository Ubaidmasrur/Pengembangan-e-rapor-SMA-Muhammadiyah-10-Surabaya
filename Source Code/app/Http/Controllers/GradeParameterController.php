<?php

namespace App\Http\Controllers;

use App\Models\GradeParameter;
use App\Http\Requests\GradeParameterRequest;
use App\Models\Subject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class GradeParameterController extends Controller
{
    public function index(Request $request)
    {
    $query = GradeParameter::withTrashed();

        if ($request->filled('q')) {
            $search = $request->input('q');

            $query->where(function ($q) use ($search) {
                $q->where('grade_letter', 'like', '%' . $search . '%');
            });
        }

        $gradeParameters = $query->paginate(15)->appends(['q' => $request->input('q')]);

        return view('admin.grade_parameters.index', compact('gradeParameters'));
    }

    public function create()
    {
        $subjects = Subject::all();
        return view('admin.grade_parameters.create', compact('subjects'));
    }

    public function store(GradeParameterRequest $request)
    {
        GradeParameter::create($request->validated());
        return redirect()->route('admin.grade_parameters.index')->with('success', 'Parameter nilai berhasil ditambahkan.');
    }

    public function edit(GradeParameter $gradeParameter)
    {
        $subjects = Subject::all();
        return view('admin.grade_parameters.edit', compact('gradeParameter', 'subjects'));
    }

    public function update(GradeParameterRequest $request, GradeParameter $gradeParameter)
    {
        $gradeParameter->update($request->validated());
        return redirect()->route('admin.grade_parameters.index')->with('success', 'Parameter nilai berhasil diperbarui.');
    }

    public function destroy(GradeParameter $gradeParameter)
    {
        $gradeParameter->delete();
        return redirect()->route('admin.grade_parameters.index')->with('success', 'Parameter nilai berhasil dihapus.');
    }

    public function restore($id)
    {
        $gradeParameter = GradeParameter::withTrashed()->findOrFail($id);
        $gradeParameter->restore();

        return redirect()->route('admin.grade_parameters.index')->with('success', 'Parameter nilai berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $gradeParameter = GradeParameter::withTrashed()->findOrFail($id);
        $gradeParameter->forceDelete();

        return redirect()->route('admin.grade_parameters.index')->with('success', 'Parameter nilai dihapus permanen.');
    }

    /**
     * AJAX lookup: given optional subject_id and score, return matching grade letter or parameters.
     * GET /ajax/grade-parameters/lookup?subject_id=1&score=87
     */
    public function lookup(Request $request)
    {
        $subjectId = $request->query('subject_id');
        $score = $request->query('score');

        $query = GradeParameter::query();
        // Only apply subject filter when the column exists in the DB
        if (Schema::hasColumn('grade_parameters', 'subject_id') && $subjectId) {
            $query->where('subject_id', $subjectId);
        }

        $params = $query->orderBy('min_score', 'desc')->get();

        // if score provided, try to find a matching parameter
        if ($score !== null && $score !== '') {
            $s = floatval($score);
            $match = $params->first(function ($p) use ($s) {
                // inclusive range: min_score <= s <= max_score
                return $p->min_score <= $s && $s <= $p->max_score;
            });
            if ($match) {
                return response()->json(['grade_letter' => $match->grade_letter, 'found' => true]);
            }
            return response()->json(['grade_letter' => null, 'found' => false]);
        }

        // otherwise return list of params (useful for admin UIs)
        return response()->json(['grade_parameters' => $params]);
    }
}
