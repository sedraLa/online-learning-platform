<?php

namespace App\Http\Controllers;

use App\Enums\CourseStatus;
use App\Enums\UserRole;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    private function guardAccess(Course $course, Request $request): ?JsonResponse
    {
        if ($course->status !== CourseStatus::Published) {
            return response()->json(['message' => 'غير موجود.'], 404);
        }

        $user = $request->user();

        if ($user->role !== UserRole::Student) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $enrolled = $course->enrollments()
            ->where('user_id', $user->id)
            ->exists();

        if (! $enrolled) {
            return response()->json(['message' => 'يجب أن تكون مسجلاً في الدورة للتعليق.'], 403);
        }

        return null;
    }

    public function indexForCourse(Request $request, Course $course): JsonResponse
    {
        if ($course->status !== CourseStatus::Published) {
            return response()->json(['message' => 'غير موجود.'], 404);
        }

        return response()->json($course->comments()->with('user:id,name')->get());
    }

    public function indexForLesson(Request $request, Course $course, Lesson $lesson): JsonResponse
    {
        if ($course->status !== CourseStatus::Published) {
            return response()->json(['message' => 'غير موجود.'], 404);
        }

        if ($lesson->course_id !== $course->id) {
            return response()->json(['message' => 'غير موجود.'], 404);
        }

        return response()->json($lesson->comments()->with('user:id,name')->get());
    }

    public function indexForAssignment(Request $request, Course $course, Assignment $assignment): JsonResponse
    {
        if ($course->status !== CourseStatus::Published) {
            return response()->json(['message' => 'غير موجود.'], 404);
        }

        if ($assignment->course_id !== $course->id) {
            return response()->json(['message' => 'غير موجود.'], 404);
        }

        return response()->json($assignment->comments()->with('user:id,name')->get());
    }

    public function storeForCourse(StoreCommentRequest $request, Course $course): JsonResponse
    {
        if ($guard = $this->guardAccess($course, $request)) {
            return $guard;
        }

        $comment = $course->comments()->create([
            'user_id' => $request->user()->id,
            'body'    => $request->validated()['body'],
        ]);

        return response()->json($comment, 201);
    }

    public function storeForLesson(StoreCommentRequest $request, Course $course, Lesson $lesson): JsonResponse
    {
        if ($guard = $this->guardAccess($course, $request)) {
            return $guard;
        }

        if ($lesson->course_id !== $course->id) {
            return response()->json(['message' => 'غير موجود.'], 404);
        }

        $comment = $lesson->comments()->create([
            'user_id' => $request->user()->id,
            'body'    => $request->validated()['body'],
        ]);

        return response()->json($comment, 201);
    }

    public function storeForAssignment(StoreCommentRequest $request, Course $course, Assignment $assignment): JsonResponse
    {
        if ($guard = $this->guardAccess($course, $request)) {
            return $guard;
        }

        if ($assignment->course_id !== $course->id) {
            return response()->json(['message' => 'غير موجود.'], 404);
        }

        $comment = $assignment->comments()->create([
            'user_id' => $request->user()->id,
            'body'    => $request->validated()['body'],
        ]);

        return response()->json($comment, 201);
    }
}
