<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class ReviewTourSelectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'tour_option_id' => (int) $this->input(
                'tour_option_id'
            ),

            'adults' => (int) $this->input(
                'adults',
                1
            ),

            'children' => (int) $this->input(
                'children',
                0
            ),

            'infants' => (int) $this->input(
                'infants',
                0
            ),

            'language' => is_string(
                $this->input('language')
            )
                ? trim(
                    (string) $this->input(
                        'language'
                    )
                )
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'tour_option_id' => [
                'required',
                'integer',
                'exists:tour_options,id',
            ],

            'travel_date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],

            'starting_time' => [
                'required',
                'date_format:H:i',
            ],

            'language' => [
                'required',
                'string',
                'max:80',
            ],

            'adults' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'children' => [
                'required',
                'integer',
                'min:0',
                'max:100',
            ],

            'infants' => [
                'required',
                'integer',
                'min:0',
                'max:100',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'tour_option_id' => 'tour option',
            'travel_date' => 'travel date',
            'starting_time' => 'starting time',
            'language' => 'language',
            'adults' => 'adult participants',
            'children' => 'child participants',
            'infants' => 'infant participants',
        ];
    }
}