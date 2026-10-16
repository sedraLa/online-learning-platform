<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            CourseSeeder::class,
            LessonSeeder::class,
            AssignmentSeeder::class,
            EnrollmentSeeder::class,
            CommentSeeder::class,
            ImageSeeder::class,
        ]);
    }
}
