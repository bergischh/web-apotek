<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Administrator',
            'email' => 'admin_web@gmail.com',
            'password' => Hash::make('web_admin'),
            'role' => 'admin',
        ]);
        // User::create([
        //     'name' => 'Pembimbing Siswa',
        //     'email' => 'ps_admin@gmail.com',
        //     'password' => Hash::make('adminps'),
        //     'role' => 'ps',
        // ]);
    }
}
