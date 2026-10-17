<?php

namespace App\Providers;

use App\Models\Course;
use App\Models\Lesson;
use App\Policies\CoursePolicy;
use App\Policies\LessonPolicy;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ImageService::class);
    }

    public function boot(): void
    {
        Gate::policy(Course::class, CoursePolicy::class);
        Gate::policy(Lesson::class, LessonPolicy::class);

        Relation::morphMap([
            'course'     => \App\Models\Course::class,
            'lesson'     => \App\Models\Lesson::class,
            'assignment' => \App\Models\Assignment::class,
            'user'       => \App\Models\User::class,
        ]);
    }
}
