<?php

namespace App\Http\Requests;

use App\Enums\CourseLevel;
use App\Enums\CourseStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StoreCourseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status'      => ['required', new Enum(CourseStatus::class)],
            'level'       => ['required', new Enum(CourseLevel::class)],
            'is_free'     => ['required', 'boolean'],
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
            'title.required'   => 'حقل :attribute مطلوب.',
            'title.max'        => 'يجب ألا يتجاوز :attribute :max حرفاً.',
            'status.required'  => 'حقل :attribute مطلوب.',
            'status.*'         => 'قيمة :attribute غير صالحة.',
            'level.required'   => 'حقل :attribute مطلوب.',
            'level.*'          => 'قيمة :attribute غير صالحة.',
            'is_free.required' => 'حقل :attribute مطلوب.',
            'is_free.boolean'  => 'يجب أن تكون قيمة :attribute صحيحة أو خاطئة.',
        ];
    }
}
