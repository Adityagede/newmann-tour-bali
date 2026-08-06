<?php

return [
    'booking_notification_email' => env(
        'BOOKING_NOTIFICATION_EMAIL'
    ),


  


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