<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLessonRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title'   => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
            'order'   => ['required', 'integer', 'min:0'],
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
            'title.required' => 'حقل :attribute مطلوب.',
            'title.max'      => 'يجب ألا يتجاوز :attribute :max حرفاً.',
            'order.required' => 'حقل :attribute مطلوب.',
            'order.integer'  => 'يجب أن يكون :attribute رقماً صحيحاً.',
            'order.min'      => 'يجب أن يكون :attribute أكبر من أو يساوي :min.',
        ];
    }
}
