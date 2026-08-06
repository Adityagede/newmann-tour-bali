<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveTourOptionDiscountRequest extends FormRequest
{
    public const DISCOUNT_TYPES = [
        'percentage' => 'Percentage',
        'fixed' => 'Fixed amount per participant',
    ];

    public const PARTICIPANT_TYPES = [
        'adult' => 'Adult',
        'child' => 'Child',
        'infant' => 'Infant',
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
                'starts_at',
                'ends_at',
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
            'label' => [
                'required',
                'string',
                'max:180',
            ],

            'discount_type' => [
                'required',
                Rule::in(
                    array_keys(self::DISCOUNT_TYPES)
                ),
            ],

            'discount_value' => [
                'required',
                'integer',
                'min:1',
                'max:999999999999',
            ],

            /*
             * true berarti participant_types akan
             * disimpan null dan berlaku untuk semua
             * peserta berbayar yang diperbolehkan.
             */
            'applies_to_all' => [
                'required',
                'boolean',
            ],

            'participant_types' => [
                'nullable',
                'array',
            ],

            'participant_types.*' => [
                'string',
                'distinct',
                Rule::in(
                    array_keys(self::PARTICIPANT_TYPES)
                ),
            ],

            'starts_at' => [
                'nullable',
                'date_format:Y-m-d\TH:i',
            ],

            'ends_at' => [
                'nullable',
                'date_format:Y-m-d\TH:i',
            ],

            'priority' => [
                'required',
                'integer',
                'min:0',
                'max:10000',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }

    public function withValidator(
        Validator $validator
    ): void {
        $validator->after(function (
            Validator $validator
        ): void {
            $discountType = (string) $this->input(
                'discount_type',
                ''
            );

            $discountValue = (int) $this->input(
                'discount_value',
                0
            );

            if (
                $discountType === 'percentage'
                && $discountValue > 100
            ) {
                $validator->errors()->add(
                    'discount_value',
                    'Percentage discount cannot exceed 100%.'
                );
            }

            $appliesToAll = $this->boolean(
                'applies_to_all'
            );

            $participantTypes = $this->input(
                'participant_types',
                []
            );

            if (!is_array($participantTypes)) {
                $participantTypes = [];
            }

            if (
                !$appliesToAll
                && $participantTypes === []
            ) {
                $validator->errors()->add(
                    'participant_types',
                    'Select at least one participant type '
                    . 'or enable all paid participants.'
                );
            }

            $startsAt = $this->input('starts_at');
            $endsAt = $this->input('ends_at');

            if (
                is_string($startsAt)
                && is_string($endsAt)
                && strtotime($endsAt)
                    < strtotime($startsAt)
            ) {
                $validator->errors()->add(
                    'ends_at',
                    'The ending date and time must be '
                    . 'after or equal to the starting date and time.'
                );
            }
        });
    }

    public function attributes(): array
    {
        return [
            'discount_type' => 'discount type',
            'discount_value' => 'discount value',
            'applies_to_all' => 'all-participant setting',
            'participant_types' => 'participant targets',
            'starts_at' => 'starting date and time',
            'ends_at' => 'ending date and time',
            'is_active' => 'active status',
        ];
    }

    public static function discountTypes(): array
    {
        return self::DISCOUNT_TYPES;
    }

    public static function participantTypes(): array
    {
        return self::PARTICIPANT_TYPES;
    }
}