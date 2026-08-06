<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateTourGalleryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'gallery_images' => [
                'required',
                'array',
                'min:1',
                'max:12',
            ],

            'gallery_images.*' => [
                'required',
                'file',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'gallery_images.required' =>
                'Please select at least one gallery image.',

            'gallery_images.array' =>
                'The gallery upload format is invalid.',

            'gallery_images.*.image' =>
                'Every gallery file must be a valid image.',

            'gallery_images.*.mimes' =>
                'Gallery images must use JPG, JPEG, PNG, or WEBP format.',

            'gallery_images.*.max' =>
                'Each gallery image may be a maximum of 5 MB.',
        ];
    }
}