<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class SubmitTourBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'full_name' => is_string(
                $this->input('full_name')
            )
                ? trim(
                    (string) $this->input(
                        'full_name'
                    )
                )
                : null,

            'whatsapp' => is_string(
                $this->input('whatsapp')
            )
                ? trim(
                    (string) $this->input(
                        'whatsapp'
                    )
                )
                : null,

            'email' => is_string(
                $this->input('email')
            )
                ? trim(
                    (string) $this->input(
                        'email'
                    )
                )
                : null,

            'pickup_address' => is_string(
                $this->input(
                    'pickup_address'
                )
            )
                ? trim(
                    (string) $this->input(
                        'pickup_address'
                    )
                )
                : null,

            'special_requests' => is_string(
                $this->input(
                    'special_requests'
                )
            )
                ? trim(
                    (string) $this->input(
                        'special_requests'
                    )
                )
                : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'full_name' => [
                'required',
                'string',
                'min:2',
                'max:180',
            ],

            'whatsapp' => [
                'required',
                'string',
                'max:50',
                'regex:/^\+?[0-9\s().-]{7,25}$/',
            ],

            'email' => [
                'nullable',
                'email:rfc',
                'max:180',
            ],

            'pickup_address' => [
                'required',
                'string',
                'min:5',
                'max:2000',
            ],

            'special_requests' => [
                'nullable',
                'string',
                'max:3000',
            ],

            'agreement' => [
                'required',
                'accepted',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'whatsapp.regex' =>
                'Enter a valid WhatsApp number.',

            'agreement.accepted' =>
                'Please confirm that the trip details are correct.',
        ];
    }

    public function attributes(): array
    {
        return [
            'full_name' => 'full name',
            'whatsapp' => 'WhatsApp number',
            'pickup_address' =>
                'pickup location',

            'special_requests' =>
                'special requests',

            'agreement' =>
                'trip detail confirmation',
        ];
    }
}