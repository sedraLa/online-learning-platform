<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => ['required', 'image', 'mimes:jpeg,png,webp', 'max:2048'],
        ];
    }

    public function attributes(): array
    {
        return [
            'image' => 'الصورة',
        ];
    }

    public function messages(): array
    {
        return [
            'image.required' => 'حقل :attribute مطلوب.',
            'image.image'    => 'يجب أن يكون الملف المرفق صورة.',
            'image.mimes'    => 'يجب أن تكون :attribute من نوع: jpeg, png, webp.',
            'image.max'      => 'يجب ألا يتجاوز حجم :attribute 2 ميغابايت.',
        ];
    }
}
