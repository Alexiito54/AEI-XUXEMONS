<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'user_id' => 'root',
            'name' => 'Root',
            'surname' => 'Administrator',
            'email' => 'root@admin.com',
            'password' => Hash::make('123456'),
            'role' => 'ADMIN',
        ]);
    }
}
