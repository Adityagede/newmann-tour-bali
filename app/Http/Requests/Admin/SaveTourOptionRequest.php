<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveTourOptionRequest extends FormRequest
{
    public const PICKUP_TYPES = [
        'hotel_pickup' => 'Hotel pickup',
        'meeting_point' => 'Meeting point',
        'flexible' => 'Flexible / confirmed later',
    ];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach (
            [
                'title',
                'short_description',
                'languages_text',
                'pickup_label',
                'confirmation_note',
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
            'title' => [
                'required',
                'string',
                'max:180',
            ],

            'short_description' => [
                'nullable',
                'string',
                'max:1500',
            ],

            'duration_minutes' => [
                'nullable',
                'integer',
                'min:1',
                'max:1440',
            ],

            'languages_text' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'pickup_type' => [
                'required',
                Rule::in(
                    array_keys(self::PICKUP_TYPES)
                ),
            ],

            'pickup_label' => [
                'nullable',
                'string',
                'max:180',
            ],

            'confirmation_note' => [
                'nullable',
                'string',
                'max:1500',
            ],

            'min_guests' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],

            'max_guests' => [
                'nullable',
                'integer',
                'min:1',
                'max:100',
                'gte:min_guests',
            ],

            'is_all_inclusive' => [
                'nullable',
                'boolean',
            ],

            'is_default' => [
                'nullable',
                'boolean',
            ],

            'status' => [
                'required',
                Rule::in([
                    'draft',
                    'active',
                    'inactive',
                ]),
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'short_description' => 'short description',
            'duration_minutes' => 'duration',
            'languages_text' => 'languages',
            'pickup_type' => 'pickup type',
            'pickup_label' => 'pickup label',
            'confirmation_note' => 'confirmation note',
            'min_guests' => 'minimum participants',
            'max_guests' => 'maximum participants',
            'is_all_inclusive' => 'all-inclusive setting',
            'is_default' => 'default setting',
        ];
    }

    public static function pickupTypes(): array
    {
        return self::PICKUP_TYPES;
    }
}