<?php

return [

    /*
    |--------------------------------------------------------------------------
    | B2B Sync Secret
    |--------------------------------------------------------------------------
    |
    | This secret key is used to authenticate B2B export endpoints.
    |
    */
    'sync_secret' => env('B2B_SYNC_SECRET', 'EasyTax_Super_Secret_Key_2026!'),

    /*
    |--------------------------------------------------------------------------
    | Cross Server Secret
    |--------------------------------------------------------------------------
    |
    | This secret key is used to authenticate KPI API requests.
    |
    */
    'cross_server_secret' => env('CROSS_SERVER_SECRET'),

];
