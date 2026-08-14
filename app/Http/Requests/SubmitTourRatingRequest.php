<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SubmitTourRatingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if (is_string($this->input('feedback'))) {
            $feedback = trim((string) $this->input('feedback'));

            $this->merge([
                'feedback' => $feedback !== '' ? $feedback : null,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'rating' => [
                'required',
                'integer',
                'between:1,5',
            ],
            'feedback' => [
                'nullable',
                'string',
                'max:1000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'rating.required' => 'Choose a star rating before sending.',
            'rating.between' => 'Choose a rating from 1 to 5 stars.',
            'feedback.max' => 'Keep your private note within 1,000 characters.',
        ];
    }
}
