<?php

namespace Bluewing\Controllers;

use Bluewing\BluewingAuthenticatesUsers;

/**
 * Handles application login routing and any associated tangential functionality.
 */
class LoginController extends Controller {

    use BluewingAuthenticatesUsers;

    /**
     *
     */
    public function __construct()
    {
        $this->middleware('AppendTokensToResponse');
    }
}
