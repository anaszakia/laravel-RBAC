<?php
namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\Role;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index()
    {
        $menus = Menu::with('parent', 'roles')
            ->whereNull('parent_id')
            ->withCount('children')
            ->orderBy('order')
            ->get();

        return view('admin.menus.index', compact('menus'));
    }

    public function create()
    {
        $parents = Menu::whereNull('parent_id')->orderBy('order')->get();
        $roles   = Role::all();
        return view('admin.menus.create', compact('parents', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'url'       => 'nullable|string|max:255',
            'icon'      => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:menus,id',
            'order'     => 'nullable|integer',
            'roles'     => 'nullable|array',
            'roles.*'   => 'exists:roles,id',
        ]);

        $menu = Menu::create([
            'name'      => $request->name,
            'url'       => $request->url,
            'icon'      => $request->icon,
            'parent_id' => $request->parent_id,
            'order'     => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->roles) {
            $menu->roles()->sync($request->roles);
        }

        return redirect()->route('menus.index')
            ->with('success', 'Menu berhasil ditambahkan!');
    }

    public function edit(Menu $menu)
    {
        $parents = Menu::whereNull('parent_id')
            ->where('id', '!=', $menu->id)
            ->orderBy('order')
            ->get();
        $roles = Role::all();
        $menu->load('roles');

        return view('admin.menus.edit', compact('menu', 'parents', 'roles'));
    }

    public function update(Request $request, Menu $menu)
    {
        $request->validate([
            'name'      => 'required|string|max:100',
            'url'       => 'nullable|string|max:255',
            'icon'      => 'nullable|string|max:100',
            'parent_id' => 'nullable|exists:menus,id',
            'order'     => 'nullable|integer',
            'roles'     => 'nullable|array',
            'roles.*'   => 'exists:roles,id',
        ]);

        $menu->update([
            'name'      => $request->name,
            'url'       => $request->url,
            'icon'      => $request->icon,
            'parent_id' => $request->parent_id,
            'order'     => $request->order ?? 0,
            'is_active' => $request->has('is_active'),
        ]);

        $menu->roles()->sync($request->roles ?? []);

        return redirect()->route('menus.index')
            ->with('success', 'Menu berhasil diupdate!');
    }

    public function destroy(Menu $menu)
    {
        // Hapus children dulu
        $menu->children()->delete();
        $menu->roles()->detach();
        $menu->delete();

        return redirect()->route('menus.index')
            ->with('success', 'Menu berhasil dihapus!');
    }
}