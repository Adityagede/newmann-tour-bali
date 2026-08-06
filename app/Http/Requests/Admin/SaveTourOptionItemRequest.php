<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveTourOptionItemRequest extends FormRequest
{
    public const ITEM_TYPES = [
        'included' => 'Included',
        'excluded' => 'Excluded',
    ];

    public const CATEGORIES = [
        'transport' => 'Transport',
        'pickup' => 'Pickup and drop-off',
        'guide' => 'Driver or guide',
        'ticket' => 'Entrance ticket',
        'meal' => 'Meal',
        'drink' => 'Drink',
        'parking_fee' => 'Parking or local fee',
        'equipment' => 'Equipment',
        'personal_expense' => 'Personal expense',
        'insurance' => 'Insurance',
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
                'label',
                'details',
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
            'item_type' => [
                'required',
                Rule::in(array_keys(self::ITEM_TYPES)),
            ],

            'category' => [
                'required',
                Rule::in(array_keys(self::CATEGORIES)),
            ],

            'label' => [
                'required',
                'string',
                'max:180',
            ],

            'details' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'is_highlighted' => [
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
            'item_type' => 'item type',
            'is_highlighted' => 'highlight setting',
            'is_active' => 'active status',
        ];
    }

    public static function itemTypes(): array
    {
        return self::ITEM_TYPES;
    }

    public static function categories(): array
    {
        return self::CATEGORIES;
    }
}