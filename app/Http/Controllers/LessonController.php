<?php

namespace App\Http\Controllers;

use App\Enums\CourseStatus;
use App\Enums\UserRole;
use App\Http\Requests\StoreLessonRequest;
use App\Http\Requests\UpdateLessonRequest;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    private function courseVisibleToUser(Course $course, Request $request): bool
    {
        $user = $request->user();

        if ($user->role === UserRole::Teacher) {
            return $course->user_id === $user->id;
        }

        return $course->status === CourseStatus::Published;
    }

    public function index(Request $request, Course $course): JsonResponse
    {
        if (! $this->courseVisibleToUser($course, $request)) {
            return response()->json(['message' => 'غير موجود.'], 404);
        }

        return response()->json($course->lessons()->orderBy('order')->get());
    }

    public function show(Request $request, Course $course, Lesson $lesson): JsonResponse
    {
        if (! $this->courseVisibleToUser($course, $request)) {
            return response()->json(['message' => 'غير موجود.'], 404);
        }

        if ($lesson->course_id !== $course->id) {
            return response()->json(['message' => 'غير موجود.'], 404);
        }

        return response()->json($lesson);
    }

    public function store(StoreLessonRequest $request, Course $course): JsonResponse
    {
        $this->authorize('create', [Lesson::class, $course]);

        $lesson = $course->lessons()->create($request->validated());

        return response()->json($lesson, 201);
    }

    public function update(UpdateLessonRequest $request, Course $course, Lesson $lesson): JsonResponse
    {
        if ($lesson->course_id !== $course->id) {
            return response()->json(['message' => 'غير موجود.'], 404);
        }

        $this->authorize('update', $lesson);

        $lesson->update($request->validated());

        return response()->json($lesson);
    }

    public function destroy(Request $request, Course $course, Lesson $lesson): JsonResponse
    {
        if ($lesson->course_id !== $course->id) {
            return response()->json(['message' => 'غير موجود.'], 404);
        }

        $this->authorize('delete', $lesson);

        $lesson->delete();

        return response()->json(['message' => 'تم حذف الدرس بنجاح.'], 200);
    }
}
