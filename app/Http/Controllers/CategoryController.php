<?php

namespace App\Http\Controllers;

use App\Enums\CourseStatus;
use App\Enums\UserRole;
use App\Models\Category;
use App\Models\Scopes\PublishedScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Category::all());
    }

    public function show(Request $request, Category $category): JsonResponse
    {
        $user = $request->user();

        // --- Courses ---
        $coursesQuery = $category->courses()
            ->withoutGlobalScope(PublishedScope::class);

        if ($user->role === UserRole::Teacher) {
            $coursesQuery->where('user_id', $user->id);
        } else {
            $coursesQuery->where('status', CourseStatus::Published->value);
        }

        $courses = $coursesQuery->get();

        // Collect the IDs of courses visible to this user, used to filter
        // lessons and assignments without additional per-record queries.
        $visibleCourseIds = $courses->pluck('id');

        // --- Lessons ---
        // Eager-load the course relationship on all lessons in this category,
        // then keep only those whose course is in the visible set.
        // This is two queries (lessons + their courses), not N.
        $lessons = $category->lessons()
            ->with(['course' => fn ($q) => $q->withoutGlobalScope(PublishedScope::class)])
            ->get()
            ->filter(fn ($lesson) => $visibleCourseIds->contains($lesson->course_id))
            ->values();

        // --- Assignments ---
        // Same approach as lessons.
        $assignments = $category->assignments()
            ->with(['course' => fn ($q) => $q->withoutGlobalScope(PublishedScope::class)])
            ->get()
            ->filter(fn ($assignment) => $visibleCourseIds->contains($assignment->course_id))
            ->values();

        return response()->json([
            'id'          => $category->id,
            'name'        => $category->name,
            'slug'        => $category->slug,
            'courses'     => $courses,
            'lessons'     => $lessons,
            'assignments' => $assignments,
        ]);
    }
}
