<?php

namespace Bluewing\Http\Controllers;

use Bluewing\Auth\Concerns\AuthenticatesUsers;
use Bluewing\Http\Middleware\AppendTokensToResponse;

/**
 * Handles application login routing and any associated tangential functionality.
 */
class LoginController extends Controller
{
    use AuthenticatesUsers;

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
