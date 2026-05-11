<?php
namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Services\MinioService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct(protected MinioService $minio) {}

    public function index()
    {
        abort_unless(can('users.view'), 403);

        $users = User::with('role', 'roles')->orderBy('name')->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        abort_unless(can('users.create'), 403);

        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        abort_unless(can('users.create'), 403);

        $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|min:8|confirmed',
            'role_id'   => 'nullable|exists:roles,id',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string|max:500',
            'avatar'    => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $avatarPath = null;
        if ($request->hasFile('avatar')) {
            $avatarPath = minio_upload($request->file('avatar'), 'avatars');
        }

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role_id'  => $request->role_id,
            'phone'    => $request->phone,
            'address'  => $request->address,
            'avatar'   => $avatarPath,
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil ditambahkan!');
    }

    public function show(User $user)
    {
        abort_unless(can('users.view'), 403);

        $user->load('role', 'roles');

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        abort_unless(can('users.edit'), 403);

        $roles = Role::orderBy('name')->get();
        $user->load('role', 'roles');

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        abort_unless(can('users.edit'), 403);

        $request->validate([
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'role_id'  => 'nullable|exists:roles,id',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:500',
            'avatar'   => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data = [
            'name'    => $request->name,
            'email'   => $request->email,
            'role_id' => $request->role_id,
            'phone'   => $request->phone,
            'address' => $request->address,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Upload avatar baru & hapus yang lama
        if ($request->hasFile('avatar')) {
            $data['avatar'] = minio_replace($user->avatar, $request->file('avatar'), 'avatars');
        }

        // Hapus avatar jika checkbox remove dicentang
        if ($request->has('remove_avatar') && $user->avatar) {
            minio_delete($user->avatar);
            $data['avatar'] = null;
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User berhasil diupdate!');
    }

    public function destroy(User $user)
    {
        abort_unless(can('users.delete'), 403);

        if ($user->id === session('user_id')) {
            return redirect()->route('users.index')
                ->with('error', 'Tidak bisa menghapus akun yang sedang login!');
        }

        // Hapus avatar dari MinIO
        if ($user->avatar) {
            minio_delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User berhasil dihapus!');
    }
}