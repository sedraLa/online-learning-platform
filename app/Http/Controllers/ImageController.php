<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Http\Requests\StoreImageRequest;
use App\Models\Assignment;
use App\Models\Course;
use App\Models\Lesson;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    public function __construct(private ImageService $imageService) {}

    public function forCourse(StoreImageRequest $request, Course $course): JsonResponse
    {
        if ($request->user()->role !== UserRole::Teacher || $request->user()->id !== $course->user_id) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $image = $this->imageService->replace($course, $request->file('image'));

        return response()->json($this->imageResponse($image, 'course'), 200);
    }

    public function forLesson(StoreImageRequest $request, Course $course, Lesson $lesson): JsonResponse
    {
        if ($lesson->course_id !== $course->id) {
            return response()->json(['message' => 'غير موجود.'], 404);
        }

        if ($request->user()->role !== UserRole::Teacher || $request->user()->id !== $course->user_id) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $image = $this->imageService->replace($lesson, $request->file('image'));

        return response()->json($this->imageResponse($image, 'lesson'), 200);
    }

    public function forAssignment(StoreImageRequest $request, Assignment $assignment): JsonResponse
    {
        $course = $assignment->course;

        if ($request->user()->role !== UserRole::Teacher || $request->user()->id !== $course->user_id) {
            return response()->json(['message' => 'غير مصرح.'], 403);
        }

        $image = $this->imageService->replace($assignment, $request->file('image'));

        return response()->json($this->imageResponse($image, 'assignment'), 200);
    }

    public function forMe(StoreImageRequest $request): JsonResponse
    {
        $image = $this->imageService->replace($request->user(), $request->file('image'));

        return response()->json($this->imageResponse($image, 'user'), 200);
    }

    private function imageResponse($image, string $type): array
    {
        return [
            'id'             => $image->id,
            'path'           => $image->path,
            'disk'           => $image->disk,
            'imageable_type' => $type,
        ];
    }
}
