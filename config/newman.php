<?php

return [
    'booking_protection' => [
        'session_per_minute' => (int) env('BOOKING_SESSION_PER_MINUTE', 10),
        'ip_per_minute' => (int) env('BOOKING_IP_PER_MINUTE', 300),
        'availability_session_per_minute' => (int) env('AVAILABILITY_SESSION_PER_MINUTE', 60),
        'availability_ip_per_minute' => (int) env('AVAILABILITY_IP_PER_MINUTE', 1200),
        'success_access_minutes' => (int) env('CUSTOM_TRIP_SUCCESS_ACCESS_MINUTES', 120),
        'notification_emails_per_minute' => (int) env('BOOKING_NOTIFICATION_EMAILS_PER_MINUTE', 20),
    ],

    'booking_notification_email' => env('BOOKING_NOTIFICATION_EMAIL') ?: env('NEWMAN_BOOKING_NOTIFICATION_EMAIL'),


  


    // Konfigurasi lama tetap di sini.

    'whatsapp_number' => env(
        'NEWMAN_WHATSAPP_NUMBER',
        '6281246890251'
    ),

    'whatsapp_display' => env(
        'NEWMAN_WHATSAPP_DISPLAY',
        '+62 812-4689-0251'
    ),

    'contact_email' => env(
        'NEWMAN_CONTACT_EMAIL',
        env('MAIL_FROM_ADDRESS')
    ),

    'instagram_url' => env(
        'NEWMAN_INSTAGRAM_URL'
    ),

    'facebook_url' => env(
        'NEWMAN_FACEBOOK_URL'
    ),

    'location' => env(
        'NEWMAN_LOCATION',
        'Bali, Indonesia'
    ),

];
