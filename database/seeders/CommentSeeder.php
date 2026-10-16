<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $alice = User::where('email', 'alice@example.com')->first();
        $bob   = User::where('email', 'bob@example.com')->first();
        $carol = User::where('email', 'carol@example.com')->first();
        $dan   = User::where('email', 'dan@example.com')->first();
        $eve   = User::where('email', 'eve@example.com')->first();

        $laravel  = Course::where('title', 'Laravel from Scratch')->first();
        $eloquent = Course::where('title', 'Advanced Eloquent ORM')->first();
        $python   = Course::where('title', 'Python for Data Science')->first();
        $flutter  = Course::where('title', 'Flutter Mobile Development')->first();

        // Comments on courses
        // carol and dan are enrolled in Laravel from Scratch
        $this->comment($laravel, $alice->id, 'Welcome to the course! Feel free to ask questions.');
        $this->comment($laravel, $carol->id, 'Really enjoying this so far, thank you!');
        $this->comment($laravel, $dan->id,   'Could you add more examples for middleware?');

        // dan is enrolled in Advanced Eloquent ORM
        $this->comment($eloquent, $alice->id, 'This course covers real-world query patterns.');
        $this->comment($eloquent, $dan->id,   'The N+1 section was eye-opening.');

        // carol and eve are enrolled in Python for Data Science
        $this->comment($python, $bob->id,   'Updated the pandas section for version 2.0.');
        $this->comment($python, $carol->id, 'The matplotlib examples are very clear.');
        $this->comment($python, $eve->id,   'Would love a section on NumPy as well.');

        // Comments on lessons
        $settingUp = Lesson::where('title', 'Setting Up Laravel')->first();
        $this->comment($settingUp, $carol->id, 'Had a path issue on Windows, fixed it by adding PHP to PATH.');
        $this->comment($settingUp, $dan->id,   'Worked perfectly on Ubuntu.');

        $eagerLoading = Lesson::where('title', 'Eager Loading')->first();
        $this->comment($eagerLoading, $dan->id, 'This finally made with() click for me.');

        $pandasLesson = Lesson::where('title', 'Pandas DataFrames')->first();
        $this->comment($pandasLesson, $carol->id, 'Great examples, the groupby part was really helpful.');
        $this->comment($pandasLesson, $eve->id,   'Can we get an exercise for this lesson?');

        // Comments on assignments
        $crudApi = Assignment::where('title', 'Build a Basic CRUD API')->first();
        $this->comment($crudApi, $carol->id, 'Submitted! Used resource controllers as suggested.');
        $this->comment($crudApi, $dan->id,   'Is it okay to use query builder instead of Eloquent?');

        $csvAssignment = Assignment::where('title', 'Analyse a CSV Dataset')->first();
        $this->comment($csvAssignment, $eve->id, 'Which CSV dataset should we use?');
        $this->comment($csvAssignment, $bob->id, 'Any public dataset from Kaggle works fine.');
    }

    private function comment(object $model, int $userId, string $body): void
    {
        $existing = $model->comments()
            ->where('user_id', $userId)
            ->where('body', $body)
            ->exists();

        if (! $existing) {
            $model->comments()->create([
                'user_id' => $userId,
                'body'    => $body,
            ]);
        }
    }
}
