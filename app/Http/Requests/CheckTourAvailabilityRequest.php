<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CheckTourAvailabilityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        /*
         * Backward compatibility untuk component lama.
         * Setelah component diganti, browser akan memakai
         * travel_date dan participants.
         */
        $travelDate = $this->input(
            'travel_date',
            $this->input('date')
        );

        $participants = $this->input(
            'participants'
        );

        if (!is_array($participants)) {
            $participants = [
                'adult' => $this->input(
                    'adults',
                    1
                ),

                'child' => $this->input(
                    'children',
                    0
                ),

                'infant' => $this->input(
                    'infants',
                    0
                ),
            ];
        }

        $language = $this->input('language');

        if (is_string($language)) {
            $language = trim($language);

            if ($language === '') {
                $language = null;
            }
        }

        $this->merge([
            'travel_date' => $travelDate,
            'participants' => $participants,
            'language' => $language,
        ]);
    }

    public function rules(): array
    {
        return [
            'travel_date' => [
                'required',
                'date_format:Y-m-d',
                'after_or_equal:today',
            ],

            'language' => [
                'nullable',
                'string',
                'max:80',
            ],

            'participants' => [
                'required',
                'array',
            ],

            'participants.adult' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'participants.child' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
            ],

            'participants.infant' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'travel_date' => 'travel date',
            'participants.adult' => 'adult participants',
            'participants.child' => 'child participants',
            'participants.infant' => 'infant participants',
        ];
    }
}