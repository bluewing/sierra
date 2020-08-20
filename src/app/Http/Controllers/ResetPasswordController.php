<?php

namespace Bluewing\Http\Controllers;

use Bluewing\Auth\Concerns\ResetsPasswords;
use Bluewing\Http\Middleware\AppendTokensToResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    use ResetsPasswords;

    /**
     * Constructor for `ResetPasswordController`.
     *
     * Ensures that JWT and refresh tokens are appropriately appended to the headers of each successful reset
     * password response. This is necessary because once the request is completed, the user is considered logged in.
     */
    public function __construct()
    {
        $this->middleware(AppendTokensToResponse::class);
    }

    /**
     * This provides an invokable shortcut to the `reset` method contained in the `ResetsPasswords` trait.
     *
     * @param Request $request -
     *
     * @return JsonResponse -
     */
    public function __invoke(Request $request)
    {
        return $this->reset($request);
    }
}
