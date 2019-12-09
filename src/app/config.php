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
        'organization'      => [
            'model'         => null,
            'identifier'    => null,
        ],
        'userOrganization'  => [
            'model'         => null
        ],
        'user'              => [
            'model'         => null
        ]
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

    'refreshTokens' => [
        'model' => null
    ],

    /*
     |--------------------------------------------------------------------------
     | Preferences
     |--------------------------------------------------------------------------
     |
     | These parameters determine the name of the model that should be used for
     | preferences and their corresponding default values across the system.
     | This informs the configuration used by `PreferencesBuilder`.
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
     | Payments related configuration.
     |
     */

    'payments' => [
        'billingMethodModel'    => null,
        'invoiceModel'          => null,
        'secretKey'             => null,
        'publishableKey'        => null,
    ]
];
