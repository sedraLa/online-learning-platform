<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Category;
use App\Models\Course;
use Illuminate\Database\Seeder;

class AssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $webDev  = Category::where('slug', 'web-development')->first();
        $dataSci = Category::where('slug', 'data-science')->first();
        $algo    = Category::where('slug', 'algorithms')->first();
        $mobile  = Category::where('slug', 'mobile-dev')->first();

        $assignmentData = [
            'Laravel from Scratch' => [
                ['title' => 'Build a Basic CRUD API',    'description' => 'Create a REST API for a to-do list using Laravel routes and Eloquent.', 'due_date' => '2026-11-01 23:59:00', 'categories' => [$webDev->id]],
            ],
            'Advanced Eloquent ORM' => [
                ['title' => 'Optimise a Slow Query',     'description' => 'Identify and fix an N+1 query in a given codebase.', 'due_date' => '2026-11-15 23:59:00', 'categories' => [$webDev->id]],
            ],
            'Python for Data Science' => [
                ['title' => 'Analyse a CSV Dataset',     'description' => 'Load a CSV with pandas and answer five analysis questions.', 'due_date' => '2026-11-10 23:59:00', 'categories' => [$dataSci->id, $algo->id]],
            ],
            'Flutter Mobile Development' => [
                ['title' => 'Build a Counter App',       'description' => 'Create a stateful counter app in Flutter.', 'due_date' => '2026-11-20 23:59:00', 'categories' => [$mobile->id]],
            ],
        ];

        foreach ($assignmentData as $courseTitle => $assignments) {
            $course = Course::where('title', $courseTitle)->first();
            if (! $course) {
                continue;
            }

            foreach ($assignments as $data) {
                $categoryIds = $data['categories'];
                unset($data['categories']);

                $assignment = Assignment::firstOrCreate(
                    ['course_id' => $course->id, 'title' => $data['title']],
                    array_merge($data, ['course_id' => $course->id])
                );

                $assignment->categories()->syncWithoutDetaching($categoryIds);
            }
        }
    }
}
