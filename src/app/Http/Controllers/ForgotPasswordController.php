<?php

namespace Bluewing\Http\Controllers;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;

class ForgotPasswordController extends Controller
{
    use SendsPasswordResetEmails;

    /**
     * This provides an invokable shortcut to the `sendResetLinkEmail` method contained in the
     * `SendsPasswordResetEmails` trait.
     *
     * @param Request $request -
     *
     * @return JsonResponse -
     */
    public function __invoke(Request $request)
    {
        return $this->sendResetLinkEmail($request);
    }

    /**
     * Override the default reset link response to return `204 No Content` always, if the reset link was successfully
     * sent.
     *
     * @param Request $request
     * @param $response
     *
     * @return JsonResponse
     */
    protected function sendResetLinkResponse(Request $request, $response)
    {
        return response()->json(null, 204);
    }
}

