<?php

namespace Database\Seeders;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Models\Category;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        $alice = User::where('email', 'alice@example.com')->first();
        $bob   = User::where('email', 'bob@example.com')->first();

        $webDev   = Category::where('slug', 'web-development')->first();
        $dataSci  = Category::where('slug', 'data-science')->first();
        $mobile   = Category::where('slug', 'mobile-dev')->first();
        $algo     = Category::where('slug', 'algorithms')->first();
        $databases = Category::where('slug', 'databases')->first();

        $courses = [
            [
                'user_id'     => $alice->id,
                'title'       => 'Laravel from Scratch',
                'description' => 'A complete guide to building APIs with Laravel.',
                'status'      => CourseStatus::Published->value,
                'level'       => CourseLevel::Beginner->value,
                'is_free'     => true,
                'categories'  => [$webDev->id, $databases->id],
            ],
            [
                'user_id'     => $alice->id,
                'title'       => 'Advanced Eloquent ORM',
                'description' => 'Deep dive into Eloquent relationships and performance.',
                'status'      => CourseStatus::Published->value,
                'level'       => CourseLevel::Advanced->value,
                'is_free'     => false,
                'categories'  => [$webDev->id, $databases->id],
            ],
            [
                'user_id'     => $alice->id,
                'title'       => 'REST API Design (Draft)',
                'description' => 'Work in progress: designing clean REST APIs.',
                'status'      => CourseStatus::Draft->value,
                'level'       => CourseLevel::Intermediate->value,
                'is_free'     => false,
                'categories'  => [$webDev->id],
            ],
            [
                'user_id'     => $bob->id,
                'title'       => 'Python for Data Science',
                'description' => 'Introduction to data analysis with Python and pandas.',
                'status'      => CourseStatus::Published->value,
                'level'       => CourseLevel::Beginner->value,
                'is_free'     => true,
                'categories'  => [$dataSci->id, $algo->id],
            ],
            [
                'user_id'     => $bob->id,
                'title'       => 'Flutter Mobile Development',
                'description' => 'Build cross-platform mobile apps with Flutter.',
                'status'      => CourseStatus::Published->value,
                'level'       => CourseLevel::Intermediate->value,
                'is_free'     => false,
                'categories'  => [$mobile->id],
            ],
        ];

        foreach ($courses as $data) {
            $categoryIds = $data['categories'];
            unset($data['categories']);

            $course = Course::firstOrCreate(
                ['title' => $data['title'], 'user_id' => $data['user_id']],
                $data
            );

            $course->categories()->syncWithoutDetaching($categoryIds);
        }
    }
}
