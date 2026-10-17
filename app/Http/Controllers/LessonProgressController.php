<?php

namespace App\Http\Controllers;

use App\Enums\CourseStatus;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\LessonProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LessonProgressController extends Controller
{
    private function guardStudent(Course $course, Request $request): ?JsonResponse
    {
        $user = $request->user();

        if ($user->role !== UserRole::Student) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        if ($course->status !== CourseStatus::Published) {
            return response()->json(['message' => 'غير موجود.'], 404);
        }

        $enrolled = $course->enrollments()->where('user_id', $user->id)->exists();

        if (! $enrolled) {
            return response()->json(['message' => 'يجب أن تكون مسجلاً في الدورة.'], 403);
        }

        return null;
    }

    public function index(Request $request, Course $course): JsonResponse
    {
        if ($guard = $this->guardStudent($course, $request)) {
            return $guard;
        }

        $userId = $request->user()->id;
        $lessons = $course->lessons()
            ->orderBy('order')
            ->with(['progress' => fn ($q) => $q->where('user_id', $userId)])
            ->get()
            ->map(function (Lesson $lesson) {
                $progress = $lesson->progress->first();
                return [
                    'id'           => $lesson->id,
                    'title'        => $lesson->title,
                    'order'        => $lesson->order,
                    'completed'    => $progress?->completed_at !== null,
                    'completed_at' => $progress?->completed_at,
                ];
            });

        return response()->json([
            'course_id' => $course->id,
            'lessons'   => $lessons,
        ]);
    }

    public function complete(Request $request, Course $course, Lesson $lesson): JsonResponse
    {
        if ($guard = $this->guardStudent($course, $request)) {
            return $guard;
        }

        if ($lesson->course_id !== $course->id) {
            return response()->json(['message' => 'غير موجود.'], 404);
        }

        $progress = LessonProgress::firstOrCreate(
            ['user_id' => $request->user()->id, 'lesson_id' => $lesson->id],
            ['completed_at' => Carbon::now()]
        );

      
        if (! $progress->wasRecentlyCreated && $progress->completed_at === null) {
            $progress->update(['completed_at' => Carbon::now()]);
        }

        return response()->json([
            'lesson_id'    => $lesson->id,
            'completed'    => true,
            'completed_at' => $progress->completed_at,
        ], 200);
    }

    public function incomplete(Request $request, Course $course, Lesson $lesson): JsonResponse
    {
        if ($guard = $this->guardStudent($course, $request)) {
            return $guard;
        }

        if ($lesson->course_id !== $course->id) {
            return response()->json(['message' => 'غير موجود.'], 404);
        }

        LessonProgress::where('user_id', $request->user()->id)
            ->where('lesson_id', $lesson->id)
            ->update(['completed_at' => null]);

        return response()->json([
            'lesson_id' => $lesson->id,
            'completed' => false,
        ], 200);
    }
}
