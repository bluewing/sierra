<?php

return [

    /*
     |--------------------------------------------------------------------------
     | Tenancy Management
     |--------------------------------------------------------------------------
     |
     | Handle tenancy models and related functionality here.
     |
     */

    'tenancies' => [
        'organization' => [
            'model'         => null,
            'identifier'    => null,
        ],
    ],

    /*
     |--------------------------------------------------------------------------
     | Refresh Tokens
     |--------------------------------------------------------------------------
     |
     | This parameter determines what application model should be used for the
     | `RefreshToken` entity for the `RefreshTokenManager`.
     |
     */

    'refreshtokens' => [
        'model'                 => null
    ],

    /*
     |--------------------------------------------------------------------------
     | Refresh Tokens
     |--------------------------------------------------------------------------
     |
     | These parameters determine the name of the
     |
     */

    'preferences' => [
        'preferenceModel'           => null,
        'preferenceTemplateModel'   => null
    ],


    /*
     |--------------------------------------------------------------------------
     | Payments
     |--------------------------------------------------------------------------
     |
     | This parameter determines what application model should be used for the
     | `RefreshToken` entity for the `RefreshTokenManager`.
     |
     */

    'payments' => [
        'billingMethodModel'    => null,
        'invoiceModel'          => null,
        'secretKey'             => null,
        'publishableKey'        => null,
    ]
];
