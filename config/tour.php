<?php

return [
    'vehicle_recommendation' => [
        /*
         * Kendaraan tidak menjadi nama Tour Option.
         *
         * Aturan ini hanya menghasilkan rekomendasi awal.
         * Keputusan final tetap dikonfirmasi Newman.
         */
        'rules' => [
            [
                'key' => 'private_car',
                'label' => 'Private car',
                'description' =>
                    'Recommended for smaller private groups.',
                'min_passengers' => 1,
                'max_passengers' => 5,
            ],
            [
                'key' => 'passenger_van',
                'label' =>
                    'Passenger van',
                'description' =>
                    'Toyota Hiace or an equivalent vehicle, '
                    . 'subject to availability.',
                'min_passengers' => 6,
                'max_passengers' => 12,
            ],
            [
                'key' => 'larger_or_multiple',
                'label' =>
                    'Larger vehicle or multiple vehicles',
                'description' =>
                    'Newman will confirm the most suitable '
                    . 'transport arrangement.',
                'min_passengers' => 13,
                'max_passengers' => null,
            ],
        ],

        'confirmation_note' =>
            'Final transport will be confirmed by Newman '
            . 'based on passenger count, luggage, baby seats, '
            . 'accessibility requirements, and vehicle availability.',
    ],
];