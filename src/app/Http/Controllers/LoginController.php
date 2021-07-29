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
     * `LoginController` uses the `AuthenticatesUsers` trait to handle login functionality, while also appending
     * JSON Web Tokens and Refresh Tokens to the headers of successful login responses.
     */
    public function __construct()
    {
        $this->middleware([
            AppendTokensToResponse::class
        ]);
    }
}
