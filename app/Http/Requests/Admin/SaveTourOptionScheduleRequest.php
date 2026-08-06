<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveTourOptionScheduleRequest extends FormRequest
{
    public const DAYS = [
        0 => 'Sunday',
        1 => 'Monday',
        2 => 'Tuesday',
        3 => 'Wednesday',
        4 => 'Thursday',
        5 => 'Friday',
        6 => 'Saturday',
    ];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach (
            [
                'start_time',
                'end_time',
                'available_from',
                'available_until',
                'capacity',
                'booking_cutoff_hours',
            ] as $field
        ) {
            $value = $this->input($field);

            if (!is_string($value)) {
                continue;
            }

            $value = trim($value);

            $this->merge([
                $field => $value === ''
                    ? null
                    : $value,
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'day_of_week' => [
                'required',
                'integer',
                Rule::in(array_keys(self::DAYS)),
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'nullable',
                'date_format:H:i',
                'after:start_time',
            ],

            'available_from' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'available_until' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:available_from',
            ],

            'capacity' => [
                'nullable',
                'integer',
                'min:1',
                'max:1000',
            ],

            'booking_cutoff_hours' => [
                'required',
                'integer',
                'min:0',
                'max:720',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'day_of_week' => 'operating day',
            'start_time' => 'starting time',
            'end_time' => 'ending time',
            'available_from' => 'available from date',
            'available_until' => 'available until date',
            'booking_cutoff_hours' => 'booking cutoff',
            'is_active' => 'active status',
        ];
    }

    public static function days(): array
    {
        return self::DAYS;
    }
}