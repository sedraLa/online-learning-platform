<?php

namespace App\Http\Requests;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'nullable', 'string'],
            'status'      => ['sometimes', new Enum(CourseStatus::class)],
            'level'       => ['sometimes', new Enum(CourseLevel::class)],
            'is_free'     => ['sometimes', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title'       => 'عنوان الدورة',
            'description' => 'وصف الدورة',
            'status'      => 'حالة الدورة',
            'level'       => 'مستوى الدورة',
            'is_free'     => 'نوع الدورة',
        ];
    }

    public function messages(): array
    {
        return [
            'title.max'       => 'يجب ألا يتجاوز :attribute :max حرفاً.',
            'status.*'        => 'قيمة :attribute غير صالحة.',
            'level.*'         => 'قيمة :attribute غير صالحة.',
            'is_free.boolean' => 'يجب أن تكون قيمة :attribute صحيحة أو خاطئة.',
        ];
    }
}
