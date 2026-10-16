<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Seeder;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        $webDev    = Category::where('slug', 'web-development')->first();
        $databases = Category::where('slug', 'databases')->first();
        $dataSci   = Category::where('slug', 'data-science')->first();
        $mobile    = Category::where('slug', 'mobile-dev')->first();

        $lessonData = [
            'Laravel from Scratch' => [
                ['title' => 'Setting Up Laravel',        'content' => 'Install Laravel via Composer and configure your environment.', 'order' => 1, 'categories' => [$webDev->id]],
                ['title' => 'Routing Basics',            'content' => 'Define routes in web.php and api.php.', 'order' => 2, 'categories' => [$webDev->id]],
                ['title' => 'Eloquent Introduction',     'content' => 'Create your first model and migration.', 'order' => 3, 'categories' => [$databases->id]],
            ],
            'Advanced Eloquent ORM' => [
                ['title' => 'Eager Loading',             'content' => 'Use with() to avoid N+1 queries.', 'order' => 1, 'categories' => [$databases->id]],
                ['title' => 'Polymorphic Relationships', 'content' => 'Implement morphMany and morphToMany.', 'order' => 2, 'categories' => [$databases->id]],
            ],
            'Python for Data Science' => [
                ['title' => 'Python Basics',             'content' => 'Variables, loops, and functions in Python.', 'order' => 1, 'categories' => [$dataSci->id]],
                ['title' => 'Pandas DataFrames',         'content' => 'Load and manipulate data with pandas.', 'order' => 2, 'categories' => [$dataSci->id]],
                ['title' => 'Data Visualisation',        'content' => 'Plot data with matplotlib.', 'order' => 3, 'categories' => [$dataSci->id]],
            ],
            'Flutter Mobile Development' => [
                ['title' => 'Dart Language Basics',      'content' => 'Learn Dart syntax before Flutter.', 'order' => 1, 'categories' => [$mobile->id]],
                ['title' => 'Widgets and Layouts',       'content' => 'Build UIs with Flutter widgets.', 'order' => 2, 'categories' => [$mobile->id]],
            ],
        ];

        foreach ($lessonData as $courseTitle => $lessons) {
            $course = Course::where('title', $courseTitle)->first();
            if (! $course) {
                continue;
            }

            foreach ($lessons as $data) {
                $categoryIds = $data['categories'];
                unset($data['categories']);

                $lesson = Lesson::firstOrCreate(
                    ['course_id' => $course->id, 'title' => $data['title']],
                    array_merge($data, ['course_id' => $course->id])
                );

                $lesson->categories()->syncWithoutDetaching($categoryIds);
            }
        }
    }
}
