<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mode Pembayaran (Demo vs Live)
    |--------------------------------------------------------------------------
    | Menentukan apakah rekening pembayaran berstatus rekening simulasi/demo
    | portofolio atau rekening operasional live terverifikasi.
    */
    'payment_mode' => env('SPORTBOOKING_PAYMENT_MODE', 'demo'),

    /*
    |--------------------------------------------------------------------------
    | Rekening Tujuan Pembayaran
    |--------------------------------------------------------------------------
    | Nomor rekening dan nama penerima untuk instruksi transfer pelanggan.
    */
    'bank_accounts' => [
        'bca' => [
            'bank_name' => 'Bank BCA',
            'account_number' => env('SPORTBOOKING_BCA_NUMBER', '8271 9028 41'),
            'account_holder' => env('SPORTBOOKING_BCA_HOLDER', 'SportBooking Demo Receiver'),
            'is_demo' => env('SPORTBOOKING_PAYMENT_MODE', 'demo') === 'demo',
        ],
        'mandiri' => [
            'bank_name' => 'Bank Mandiri',
            'account_number' => env('SPORTBOOKING_MANDIRI_NUMBER', '1370 0829 1102'),
            'account_holder' => env('SPORTBOOKING_MANDIRI_HOLDER', 'SportBooking Demo Receiver'),
            'is_demo' => env('SPORTBOOKING_PAYMENT_MODE', 'demo') === 'demo',
        ],
    ],
];
