<?php

namespace App\Http\Controllers;

use App\Enums\CourseStatus;
use App\Enums\UserRole;
use App\Http\Requests\StoreCommentRequest;
use App\Models\Assignment;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Scopes\PublishedScope;
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

    public function latest(Request $request): JsonResponse
    {
        $user = $request->user();

        // Load 20 most recent comments.
        // user          — eager-loaded: 1 query for all authors.
        // commentable   — morphTo eager-load grouped by type:
        //   courses     — 1 query (with PublishedScope bypassed so teachers see their drafts)
        //   lessons     — 1 query, with their courses in 1 further query
        //   assignments — 1 query, with their courses in 1 further query
        // Total: 1 (comments) + 1 (users) + 1 (courses) + 1 (lessons) + 1 (lesson courses)
        //        + 1 (assignments) + 1 (assignment courses) = up to 7 queries.
        // Absent types reduce the count; with only seeded course comments it is 3 queries.
        $comments = Comment::with([
            'user:id,name',
            'commentable' => function ($morphTo) {
                $morphTo->morphWith([
                    Course::class => [
                        fn ($q) => $q->withoutGlobalScope(PublishedScope::class),
                    ],
                    Lesson::class => [
                        'course' => fn ($q) => $q->withoutGlobalScope(PublishedScope::class),
                    ],
                    Assignment::class => [
                        'course' => fn ($q) => $q->withoutGlobalScope(PublishedScope::class),
                    ],
                ]);
            },
        ])
        ->latest()
        ->take(20)
        ->get();

        $comments = $comments->filter(function (Comment $comment) use ($user) {
            $commentable = $comment->commentable;

            if ($commentable === null) {
                return false;
            }

            $course = match (true) {
                $commentable instanceof Course     => $commentable,
                $commentable instanceof Lesson     => $commentable->course,
                $commentable instanceof Assignment => $commentable->course,
                default                            => null,
            };

            if ($course === null) {
                return false;
            }

            if ($user->role === UserRole::Teacher) {
                return $course->user_id === $user->id;
            }

            return $course->status === CourseStatus::Published;
        })->values();

        return response()->json($comments);
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
