<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveTourStopRequest extends FormRequest
{
    public const STOP_TYPES = [
        'pickup' => 'Pickup',
        'destination' => 'Destination',
        'activity' => 'Activity',
        'meal' => 'Meal',
        'free_time' => 'Free time',
        'return' => 'Return',
        'other' => 'Other',
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
                'description',
                'location_name',
                'address',
                'time_label',
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
            'day_number' => [
                'required',
                'integer',
                'min:1',
                'max:30',
            ],

            'stop_type' => [
                'required',
                Rule::in(
                    array_keys(self::STOP_TYPES)
                ),
            ],

            'title' => [
                'required',
                'string',
                'max:180',
            ],

            'description' => [
                'nullable',
                'string',
                'max:3000',
            ],

            'location_name' => [
                'nullable',
                'string',
                'max:180',
            ],

            'address' => [
                'nullable',
                'string',
                'max:500',
            ],

            'scheduled_time' => [
                'nullable',
                'date_format:H:i',
            ],

            'time_label' => [
                'nullable',
                'string',
                'max:80',
            ],

            'duration_minutes' => [
                'nullable',
                'integer',
                'min:0',
                'max:1440',
            ],

            'latitude' => [
                'nullable',
                'numeric',
                'between:-90,90',
                'required_if:show_on_map,1',
            ],

            'longitude' => [
                'nullable',
                'numeric',
                'between:-180,180',
                'required_if:show_on_map,1',
            ],

            'show_on_map' => [
                'nullable',
                'boolean',
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
            'day_number' => 'day number',
            'stop_type' => 'stop type',
            'location_name' => 'location name',
            'scheduled_time' => 'scheduled time',
            'time_label' => 'time label',
            'duration_minutes' => 'duration',
            'show_on_map' => 'map visibility',
            'is_active' => 'active status',
        ];
    }

    public static function stopTypes(): array
    {
        return self::STOP_TYPES;
    }
}