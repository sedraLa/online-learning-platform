<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name'     => 'Alice Mentor',
                'email'    => 'alice@example.com',
                'password' => Hash::make('password'),
                'role'     => UserRole::Teacher->value,
            ],
            [
                'name'     => 'Bob Instructor',
                'email'    => 'bob@example.com',
                'password' => Hash::make('password'),
                'role'     => UserRole::Teacher->value,
            ],
            [
                'name'     => 'Carol Learner',
                'email'    => 'carol@example.com',
                'password' => Hash::make('password'),
                'role'     => UserRole::Student->value,
            ],
            [
                'name'     => 'Dan Student',
                'email'    => 'dan@example.com',
                'password' => Hash::make('password'),
                'role'     => UserRole::Student->value,
            ],
            [
                'name'     => 'Eve Trainee',
                'email'    => 'eve@example.com',
                'password' => Hash::make('password'),
                'role'     => UserRole::Student->value,
            ],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(['email' => $data['email']], $data);
        }
    }
}
