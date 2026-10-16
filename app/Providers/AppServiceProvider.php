<?php

namespace App\Providers;

use App\Models\Course;
use App\Policies\CoursePolicy;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Course::class, CoursePolicy::class);

        Relation::morphMap([
            'course'     => \App\Models\Course::class,
            'lesson'     => \App\Models\Lesson::class,
            'assignment' => \App\Models\Assignment::class,
            'user'       => \App\Models\User::class,
        ]);
    }
}
