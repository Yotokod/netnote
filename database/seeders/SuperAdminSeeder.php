<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@netnote.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'global_role' => 'super_admin',
            'is_active' => true,
            'is_2fa_enabled' => false,
        ]);
    }
}
