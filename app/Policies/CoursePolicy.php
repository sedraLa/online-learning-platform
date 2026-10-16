<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;

class CoursePolicy
{
    public function create(User $user): bool
    {
        return $user->role === UserRole::Teacher;
    }

    public function update(User $user, Course $course): bool
    {
        return $user->role === UserRole::Teacher && $user->id === $course->user_id;
    }

    public function delete(User $user, Course $course): bool
    {
        return $user->role === UserRole::Teacher && $user->id === $course->user_id;
    }
}
