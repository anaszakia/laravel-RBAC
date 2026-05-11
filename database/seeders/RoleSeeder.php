<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Role::create(['name' => 'Administrator', 'slug' => 'admin']);
        $user  = Role::create(['name' => 'User', 'slug' => 'user']);

        // Buat user admin
        User::create([
            'name'     => 'Admin',
            'email'    => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'role_id'  => $admin->id,
        ]);
    }
}