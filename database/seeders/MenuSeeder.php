<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('menu_role')->truncate();
        DB::table('menus')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Parent: Menu Management
        $menuManagement = DB::table('menus')->insertGetId([
            'name'       => 'Menu Management',
            'url'        => null,
            'icon'       => 'ti ti-menu-deep',
            'parent_id'  => null,
            'order'      => 1,
            'is_active'  => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Children
        DB::table('menus')->insert([
            [
                'name'       => 'Menus',
                'url'        => '/menus',
                'icon'       => 'ti ti-menu-2',
                'parent_id'  => $menuManagement,
                'order'      => 1,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'       => 'Roles',
                'url'        => '/roles',
                'icon'       => 'ti ti-shield',
                'parent_id'  => $menuManagement,
                'order'      => 2,
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}