<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@siswa.unimas.my'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('admin123'), 
                'role' => 'admin',
            ]
        );
    }
}