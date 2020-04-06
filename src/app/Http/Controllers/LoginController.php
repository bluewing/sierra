<?php

namespace Bluewing\Http\Controllers;

use Bluewing\BluewingAuthenticatesUsers;
use Bluewing\Http\Middleware\AppendTokensToResponse;

/**
 * Handles application login routing and any associated tangential functionality.
 */
class LoginController extends Controller
{
    use BluewingAuthenticatesUsers;

    /**
     * Constructor for `LoginController`.
     *
     * Ensures that JWT and refresh tokens are appropriately appended to the headers
     * of each response.
     */
    public function __construct()
    {
        $this->middleware(AppendTokensToResponse::class);
    }
}
