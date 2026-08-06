<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveTourPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        /*
         * Akses admin tetap dikendalikan oleh middleware
         * route admin yang sudah ada.
         */
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'title' => $this->cleanText(
                $this->input('title')
            ),

            'category' => $this->cleanNullableText(
                $this->input('category')
            ),

            'badge' => $this->cleanNullableText(
                $this->input('badge')
            ),

            'area' => $this->cleanNullableText(
                $this->input('area')
            ),

            'duration' => $this->cleanNullableText(
                $this->input('duration')
            ),

            'trip_type' => $this->cleanNullableText(
                $this->input('trip_type')
            ),
        ]);
    }

    public function rules(): array
    {
        return [
            /*
             * Identitas produk
             */
            'title' => [
                'required',
                'string',
                'max:180',
            ],

            'category' => [
                'nullable',
                'string',
                'max:80',
            ],

            'badge' => [
                'nullable',
                'string',
                'max:80',
            ],

            'area' => [
                'nullable',
                'string',
                'max:120',
            ],

            'duration' => [
                'nullable',
                'string',
                'max:80',
            ],

            'trip_type' => [
                'nullable',
                'string',
                'max:80',
            ],

            'tour_format' => [
                'required',
                'in:full_day,half_day,activity_transfer,custom_trip',
            ],

            /*
             * Konten produk
             */
            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'intro' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'story' => [
                'nullable',
                'string',
                'max:8000',
            ],

            'highlights_text' => [
                'nullable',
                'string',
                'max:5000',
            ],

            /*
             * Itinerary lama tetap diterima sebagai fallback.
             * Roadmap terstruktur dikelola pada Step 13.
             */
            'itinerary_text' => [
                'nullable',
                'string',
                'max:12000',
            ],

            /*
             * Media
             */
            'main_image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:8192',
            ],

            /*
             * Publishing
             */
            'status' => [
                'required',
                'in:draft,active,inactive',
            ],

            'is_popular' => [
                'nullable',
                'boolean',
            ],

            'is_featured' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'tour_format' => 'tour format',
            'main_image' => 'main image',
            'is_popular' => 'popular setting',
            'is_featured' => 'featured setting',
            'highlights_text' => 'highlights',
            'itinerary_text' => 'legacy itinerary',
        ];
    }

    private function cleanText(mixed $value): mixed
    {
        return is_string($value)
            ? trim($value)
            : $value;
    }

    private function cleanNullableText(
        mixed $value
    ): mixed {
        if (!is_string($value)) {
            return $value;
        }

        $value = trim($value);

        return $value === ''
            ? null
            : $value;
    }
}