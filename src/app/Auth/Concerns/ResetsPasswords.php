<?php


namespace Bluewing\Auth\Concerns;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Foundation\Auth\ResetsPasswords as IlluminateResetsPasswords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Trait ResetsPasswords
 *
 * Overrides the default `ResetsPasswords` trait to provide custom functionality for sending out responses.
 *
 * @package Bluewing\Auth\Concerns
 */
trait ResetsPasswords
{
    use IlluminateResetsPasswords;

    /**
     * Override the default reset password response to return `200 OK` always, if the password was
     * successfully reset for the user, with user details in the response body.
     *
     * @param Request $request - The `Request` that is being processed.
     * @param $response - The response message that would otherwise be sent.
     *
     * @return JsonResponse -
     */
    protected function sendResetResponse(Request $request, $response)
    {
        return response()->json($this->guard()->user());
    }

    /**
     * Set the user's password. This overrides the `setUserPassword` method in the `ResetsPasswords` trait to remove
     * the hashing mechanism, as this is performed automatically in the `User` class mutator for this property.
     *
     * @param  CanResetPassword  $member
     * @param  string  $password
     * @return void
     */
    protected function setUserPassword($member, $password)
    {
        $member->user->password = $password;
    }

    /**
     * Override the credentials needed to perform a password reset. We do not require a password confirmation to
     * process the request.
     *
     * @param Request $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        return $request->only(
            'password', 'email', 'token'
        );
    }

    /**
     * Override the password reset rules to remove the need for a password confirmation.
     *
     * @return array
     */
    protected function rules()
    {
        return [
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:6',
        ];
    }

    /**
     * Reset the given user's password. This removes the creation of a `rememberToken` property that is not used, and
     * replaces the `guard()->login()` call with a `guard()->setUser()` call. Additionally, ensure we are saving the
     * `User`, and not the `Member`, as the password is not stored on the `Member` instance.
     *
     * @param CanResetPassword $member
     * @param  string  $password
     *
     * @return void
     */
    protected function resetPassword($member, $password)
    {
        $this->setUserPassword($member, $password);
        $member->user->save();

        event(new PasswordReset($member));

        $this->guard()->setUser($member);
    }
}
