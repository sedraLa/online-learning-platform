<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $carol = User::where('email', 'carol@example.com')->first();
        $dan   = User::where('email', 'dan@example.com')->first();
        $eve   = User::where('email', 'eve@example.com')->first();

        $laravel  = Course::where('title', 'Laravel from Scratch')->first();
        $eloquent = Course::where('title', 'Advanced Eloquent ORM')->first();
        $python   = Course::where('title', 'Python for Data Science')->first();
        $flutter  = Course::where('title', 'Flutter Mobile Development')->first();

        $enrollments = [
            ['user_id' => $carol->id, 'course_id' => $laravel->id,  'enrolled_at' => '2026-09-01 10:00:00'],
            ['user_id' => $carol->id, 'course_id' => $python->id,   'enrolled_at' => '2026-09-03 11:00:00'],
            ['user_id' => $dan->id,   'course_id' => $laravel->id,  'enrolled_at' => '2026-09-05 09:00:00'],
            ['user_id' => $dan->id,   'course_id' => $eloquent->id, 'enrolled_at' => '2026-09-10 14:00:00'],
            ['user_id' => $dan->id,   'course_id' => $flutter->id,  'enrolled_at' => '2026-09-12 16:00:00'],
            ['user_id' => $eve->id,   'course_id' => $python->id,   'enrolled_at' => '2026-09-07 08:00:00'],
            ['user_id' => $eve->id,   'course_id' => $flutter->id,  'enrolled_at' => '2026-09-14 13:00:00'],
        ];

        foreach ($enrollments as $data) {
            Enrollment::firstOrCreate(
                ['user_id' => $data['user_id'], 'course_id' => $data['course_id']],
                $data
            );
        }
    }
}
