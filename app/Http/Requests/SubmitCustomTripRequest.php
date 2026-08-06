<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class SubmitCustomTripRequest extends FormRequest
{
    private const VEHICLE_OPTIONS = [
        'Not sure, please recommend',
        'Toyota Avanza',
        'Toyota Hiace',
        'Another Car',
    ];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => $this->cleanString('name'),
            'whatsapp' => $this->cleanString('whatsapp'),
            'email' => $this->cleanNullableString('email'),
            'selected_vehicle' => $this->cleanString('selected_vehicle'),
            'custom_vehicle' => $this->cleanNullableString('custom_vehicle'),
            'pickup_area' => $this->cleanNullableString('pickup_area'),
            'message' => $this->cleanString('message'),
        ]);
    }

    public function rules(): array
    {
        return [
            'trip_date' => [
                'nullable',
                'date',
                'after_or_equal:today',
            ],
            'people_count' => [
                'required',
                'integer',
                'min:1',
                'max:100',
            ],
            'pickup_area' => [
                'nullable',
                'string',
                'max:180',
            ],
            'message' => [
                'required',
                'string',
                'min:20',
                'max:1200',
            ],
            'selected_vehicle' => [
                'required',
                'string',
                Rule::in(self::VEHICLE_OPTIONS),
            ],
            'custom_vehicle' => [
                'exclude_unless:selected_vehicle,Another Car',
                'required_if:selected_vehicle,Another Car',
                'nullable',
                'string',
                'max:180',
            ],
            'name' => [
                'required',
                'string',
                'max:120',
            ],
            'whatsapp' => [
                'required',
                'string',
                'min:7',
                'max:50',
            ],
            'email' => [
                'nullable',
                'email',
                'max:120',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'people_count.required' => 'Please enter the number of guests.',
            'people_count.min' => 'The group must contain at least one guest.',
            'message.required' => 'Please describe the destinations or trip plan you want.',
            'message.min' => 'Please add a little more detail to the trip plan.',
            'selected_vehicle.required' => 'Please choose a vehicle preference or ask Newman to recommend one.',
            'custom_vehicle.required_if' => 'Please describe the vehicle or transport arrangement you need.',
            'name.required' => 'Please enter your name.',
            'whatsapp.required' => 'Please enter an active WhatsApp number.',
        ];
    }

    private function cleanString(string $key): string
    {
        return trim((string) $this->input($key, ''));
    }

    private function cleanNullableString(string $key): ?string
    {
        $value = trim((string) $this->input($key, ''));

        return $value === '' ? null : $value;
    }
}
