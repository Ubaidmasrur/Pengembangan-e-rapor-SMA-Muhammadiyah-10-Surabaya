<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Http\Requests\ActivityRequest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    public function index(Request $request)
    {
        $query = Activity::withTrashed();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('activity_date', 'like', '%' . $search . '%');
            });
        }

        $activities = $query->paginate(15)->appends(['q' => $request->input('q')]);
        return view('admin.activities.index', compact('activities'));
    }

    public function create()
    {
        return view('admin.activities.create');
    }

    public function store(ActivityRequest $request)
    {
        $data = $request->validated();

        // Upload thumbnail jika ada
        if ($request->hasFile('thumbnail')) {
            $data['thumbnail'] = $request->file('thumbnail')->store('activities', 'public');
        }

        Activity::create($data);

        return redirect()->route('admin.activities.index')->with('success', 'Kegiatan berhasil ditambahkan.');
    }

    public function show(Activity $activity)
    {
        return view('admin.activities.show', compact('activity'));
    }

    public function edit(Activity $activity)
    {
        return view('admin.activities.edit', compact('activity'));
    }

    public function update(ActivityRequest $request, Activity $activity)
    {
        $data = $request->validated();

        // Hapus gambar jika diminta
        if ($request->has('delete_thumbnail')) {
            if ($activity->thumbnail && Storage::disk('public')->exists($activity->thumbnail)) {
                Storage::disk('public')->delete($activity->thumbnail);
            }
            $data['thumbnail'] = null;
        }

        // Upload gambar baru jika ada
        if ($request->hasFile('thumbnail')) {
            if ($activity->thumbnail && Storage::disk('public')->exists($activity->thumbnail)) {
                Storage::disk('public')->delete($activity->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('activities', 'public');
        }

        $activity->update($data);

        return redirect()->route('admin.activities.index')->with('success', 'Kegiatan berhasil diperbarui.');
    }

    public function destroy(Activity $activity)
    {
        $activity->delete();
        return redirect()->route('admin.activities.index')->with('success', 'Kegiatan berhasil dihapus sementara.');
    }

    public function restore($id)
    {
        $activity = Activity::withTrashed()->findOrFail($id);
        $activity->restore();

        return redirect()->route('admin.activities.index')->with('success', 'Kegiatan berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $activity = Activity::withTrashed()->findOrFail($id);

        if ($activity->thumbnail) {
            Storage::disk('public')->delete($activity->thumbnail);
        }

        $activity->forceDelete();

        return redirect()->route('admin.activities.index')->with('success', 'Kegiatan berhasil dihapus permanen.');
    }
}
