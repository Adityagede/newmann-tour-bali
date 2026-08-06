<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SaveTourOptionBlackoutDateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        foreach (
            [
                'blackout_date',
                'start_time',
                'reason',
                'internal_note',
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
            'blackout_date' => [
                'required',
                'date_format:Y-m-d',
            ],

            /*
             * Checkbox mengirim nilai 1 untuk full-day
             * dan hidden input mengirim nilai 0.
             */
            'blocks_entire_day' => [
                'required',
                'boolean',
            ],

            /*
             * Waktu wajib ketika bukan full-day.
             */
            'start_time' => [
                'nullable',
                'date_format:H:i',
                'required_if:blocks_entire_day,0',
            ],

            'reason' => [
                'nullable',
                'string',
                'max:500',
            ],

            /*
             * Hanya untuk admin dan tidak ditampilkan
             * kepada pengunjung.
             */
            'internal_note' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'is_active' => [
                'required',
                'boolean',
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            'blackout_date' => 'blackout date',
            'blocks_entire_day' => 'full-day setting',
            'start_time' => 'starting time',
            'internal_note' => 'internal note',
            'is_active' => 'active status',
        ];
    }
}