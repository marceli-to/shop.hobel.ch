<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Payrexx Instance
    |--------------------------------------------------------------------------
    |
    | Your Payrexx instance name (subdomain), e.g., 'yourshop' for yourshop.payrexx.com
    |
    */

    'instance' => env('PAYREXX_INSTANCE', ''),

    /*
    |--------------------------------------------------------------------------
    | Payrexx API Secret
    |--------------------------------------------------------------------------
    |
    | Your Payrexx API secret key from the dashboard
    |
    */

    'secret' => env('PAYREXX_SECRET', ''),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | The currency for payments (CHF for Swiss market)
    |
    */

    'currency' => env('PAYREXX_CURRENCY', 'CHF'),

    /*
    |--------------------------------------------------------------------------
    | Payment Service Providers (PSP)
    |--------------------------------------------------------------------------
    |
    | Available payment methods. Leave empty to show all available methods.
    | Options: mastercard, visa, twint, postfinance_card, postfinance_efinance, etc.
    |
    */

    'psp' => [],

    /*
    |--------------------------------------------------------------------------
    | Design ID
    |--------------------------------------------------------------------------
    |
    | Custom design ID for the payment page. Create a design via the API
    | or Payrexx dashboard and set the ID here.
    |
    */

    'design_id' => env('PAYREXX_DESIGN_ID', null),

];
