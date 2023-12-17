<?php

namespace Database\Seeders;


use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CashierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Kasir 1',
            'email' => 'apotek_kasir@gmail.com',
            'password' => Hash::make('kasirapotek'),
            'role' => 'cashier',
        ]);
    }
}
