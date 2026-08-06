<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdateTourOptionPricesRequest extends FormRequest
{
    /**
     * Tiga kategori resmi Newman Tour V2.
     *
     * Rentang umur tidak diedit bebas melalui admin
     * agar konsisten di seluruh Tour Options.
     */
    public const PARTICIPANT_DEFINITIONS = [
        'adult' => [
            'label' => 'Adult',
            'age_min' => 12,
            'age_max' => null,
            'age_label' => '12+',
            'sort_order' => 10,
        ],

        'child' => [
            'label' => 'Child',
            'age_min' => 3,
            'age_max' => 11,
            'age_label' => '3–11',
            'sort_order' => 20,
        ],

        'infant' => [
            'label' => 'Infant',
            'age_min' => 0,
            'age_max' => 2,
            'age_label' => '0–2',
            'sort_order' => 30,
        ],
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'prices' => [
                'required',
                'array',
            ],
        ];

        foreach (
            array_keys(self::PARTICIPANT_DEFINITIONS)
            as $participantType
        ) {
            $rules["prices.{$participantType}"] = [
                'required',
                'array',
            ];

            $rules[
                "prices.{$participantType}.is_allowed"
            ] = [
                'required',
                'boolean',
            ];

            $rules[
                "prices.{$participantType}.is_free"
            ] = [
                'required',
                'boolean',
            ];

            $rules[
                "prices.{$participantType}.base_price"
            ] = [
                'nullable',
                'integer',
                'min:0',
                'max:999999999999',
            ];
        }

        return $rules;
    }

    public function withValidator(
        Validator $validator
    ): void {
        $validator->after(function (
            Validator $validator
        ): void {
            $prices = $this->input(
                'prices',
                []
            );

            $hasAllowedParticipant = false;

            foreach (
                self::PARTICIPANT_DEFINITIONS
                as $participantType => $definition
            ) {
                $row = is_array(
                    $prices[$participantType] ?? null
                )
                    ? $prices[$participantType]
                    : [];

                $isAllowed = filter_var(
                    $row['is_allowed'] ?? false,
                    FILTER_VALIDATE_BOOLEAN
                );

                $isFree = filter_var(
                    $row['is_free'] ?? false,
                    FILTER_VALIDATE_BOOLEAN
                );

                if ($isAllowed) {
                    $hasAllowedParticipant = true;
                }

                $basePrice = $row[
                    'base_price'
                ] ?? null;

                /*
                 * Kategori berbayar dan diperbolehkan
                 * harus mempunyai harga lebih dari nol.
                 */
                if (
                    $isAllowed
                    && !$isFree
                    && (
                        $basePrice === null
                        || $basePrice === ''
                        || (int) $basePrice <= 0
                    )
                ) {
                    $validator->errors()->add(
                        "prices.{$participantType}.base_price",
                        $definition['label']
                        . ' must have a price greater than zero '
                        . 'when it is allowed and not free.'
                    );
                }
            }

            if (!$hasAllowedParticipant) {
                $validator->errors()->add(
                    'prices',
                    'At least one participant category must be allowed.'
                );
            }
        });
    }

    public function attributes(): array
    {
        return [
            'prices.adult.is_allowed' =>
                'Adult availability',

            'prices.adult.is_free' =>
                'Adult free setting',

            'prices.adult.base_price' =>
                'Adult price',

            'prices.child.is_allowed' =>
                'Child availability',

            'prices.child.is_free' =>
                'Child free setting',

            'prices.child.base_price' =>
                'Child price',

            'prices.infant.is_allowed' =>
                'Infant availability',

            'prices.infant.is_free' =>
                'Infant free setting',

            'prices.infant.base_price' =>
                'Infant price',
        ];
    }

    public static function participantDefinitions(): array
    {
        return self::PARTICIPANT_DEFINITIONS;
    }
}