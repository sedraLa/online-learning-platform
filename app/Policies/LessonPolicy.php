<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;

class LessonPolicy
{
    public function create(User $user, Course $course): bool
    {
        return $user->role === UserRole::Teacher && $user->id === $course->user_id;
    }

    public function update(User $user, Lesson $lesson): bool
    {
        return $user->role === UserRole::Teacher && $user->id === $lesson->course->user_id;
    }

    public function delete(User $user, Lesson $lesson): bool
    {
        return $user->role === UserRole::Teacher && $user->id === $lesson->course->user_id;
    }
}
