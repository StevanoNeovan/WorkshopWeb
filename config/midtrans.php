<?php
// config/midtrans.php

return [
    /*
     * Ambil dari dashboard Midtrans:
     * https://dashboard.midtrans.com (production)
     * https://dashboard.sandbox.midtrans.com (sandbox/testing)
     *
     * Settings > Access Keys
     */
    'server_key'    => env('MIDTRANS_SERVER_KEY', ''),
    'client_key'    => env('MIDTRANS_CLIENT_KEY', ''),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'snap_url'      => env('MIDTRANS_IS_PRODUCTION', false)
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js',
];