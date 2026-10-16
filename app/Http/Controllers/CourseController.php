<?php

namespace App\Http\Controllers;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use App\Enums\UserRole;
use App\Http\Requests\StoreCourseRequest;
use App\Http\Requests\UpdateCourseRequest;
use App\Models\Course;
use App\Models\Scopes\PublishedScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Course::query();

        if ($request->user()->role === UserRole::Teacher) {
            $query->withoutGlobalScope(PublishedScope::class)
                  ->forTeacher($request->user());
        }

        if ($request->filled('level')) {
            $level = CourseLevel::tryFrom($request->input('level'));
            if ($level) {
                $query->byLevel($level);
            }
        }

        if ($request->boolean('free')) {
            $query->free();
        }

        return response()->json($query->get());
    }

    public function show(Request $request, Course $course): JsonResponse
    {
        $user = $request->user();

        if ($user->role === UserRole::Teacher) {
            if ($course->user_id !== $user->id) {
                return response()->json(['message' => 'غير موجود.'], 404);
            }
            return response()->json($course);
        }

        // Students see only published courses
        if ($course->status !== CourseStatus::Published) {
            return response()->json(['message' => 'غير موجود.'], 404);
        }

        return response()->json($course);
    }

    public function store(StoreCourseRequest $request): JsonResponse
    {
        $this->authorize('create', Course::class);

        $course = $request->user()->courses()->create($request->validated());

        return response()->json($course, 201);
    }

    public function update(UpdateCourseRequest $request, Course $course): JsonResponse
    {
        $this->authorize('update', $course);

        $course->update($request->validated());

        return response()->json($course);
    }

    public function destroy(Request $request, Course $course): JsonResponse
    {
        $this->authorize('delete', $course);

        $course->delete();

        return response()->json(['message' => 'تم حذف الدورة بنجاح.'], 200);
    }
}
