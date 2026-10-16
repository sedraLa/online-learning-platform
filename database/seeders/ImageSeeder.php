<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Image;
use Illuminate\Database\Seeder;

class ImageSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            'Laravel from Scratch'       => 'courses/laravel-from-scratch.jpg',
            'Python for Data Science'    => 'courses/python-data-science.jpg',
            'Flutter Mobile Development' => 'courses/flutter-mobile.jpg',
        ];

        foreach ($courses as $title => $path) {
            $course = Course::where('title', $title)->first();
            if (! $course) {
                continue;
            }

            // morphOne unique constraint means one image per course; skip if already exists
            if (! $course->image()->exists()) {
                Image::create([
                    'imageable_type' => 'course',
                    'imageable_id'   => $course->id,
                    'path'           => $path,
                    'disk'           => 'public',
                ]);
            }
        }
    }
}
