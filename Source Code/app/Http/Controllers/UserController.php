<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withTrashed();

        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('role', 'like', '%' . $search . '%');
            });
        }

        $users = $query->paginate(15)->appends(['q' => $request->input('q')]);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(UserRequest $request)
    {
        $role = $request->input('role');
        $entityId = $request->input('entity_id');

        // Ambil entitas sesuai role (termasuk yang di-soft delete)
        $entity = match ($role) {
            'siswa' => Student::withTrashed()->findOrFail($entityId),
            'guru'  => Teacher::withTrashed()->findOrFail($entityId),
            default => abort(400, 'Peran tidak valid.'),
        };

        // Pastikan entitas belum punya user
        if ($entity->user_id) {
            return back()->withErrors(['entity_id' => 'Data ini sudah memiliki akun.'])->withInput();
        }

        // Ambil nama dari entitas
        $name = $entity->name;

        // Buat akun user
        $user = User::create([
            'name'     => $name,
            'email'    => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'role'     => $role,
        ]);

        // Relink user_id ke entitas
        $entity->user_id = $user->id;
        $entity->restore(); // Auto-restore kalau terhapus
        $entity->save();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        $user->delete(); // soft delete
        return back()->with('success', 'User berhasil dihapus.');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('admin.users.index')->with('success', 'User berhasil dipulihkan.');
    }

    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        // Unlink dari entitas sebelum force delete
        match ($user->role) {
            'siswa' => Student::withTrashed()->where('user_id', $user->id)->update(['user_id' => null]),
            'guru'  => Teacher::withTrashed()->where('user_id', $user->id)->update(['user_id' => null]),
        };

        $user->forceDelete();

        return redirect()->route('admin.users.index')->with('success', 'User dihapus permanen.');
    }

    /**
     * Ambil entitas berdasarkan role (untuk dynamic select input)
     */
    public function getEntities(Request $request)
    {
        $role = $request->query('role');

        return match ($role) {
            'siswa' => Student::withTrashed()->whereNull('user_id')->select('id', 'name')->get(),
            'guru'  => Teacher::withTrashed()->whereNull('user_id')->select('id', 'name')->get(),
            default => response()->json([], 400),
        };
    }
}
