<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'   => ['sometimes', 'string', 'max:255'],
            'content' => ['sometimes', 'nullable', 'string'],
            'order'   => ['sometimes', 'integer', 'min:0'],
        ];
    }

    public function attributes(): array
    {
        return [
            'title'   => 'عنوان الدرس',
            'content' => 'محتوى الدرس',
            'order'   => 'ترتيب الدرس',
        ];
    }

    public function messages(): array
    {
        return [
            'title.max'     => 'يجب ألا يتجاوز :attribute :max حرفاً.',
            'order.integer' => 'يجب أن يكون :attribute رقماً صحيحاً.',
            'order.min'     => 'يجب أن يكون :attribute أكبر من أو يساوي :min.',
        ];
    }
}
