<?php

namespace Bluewing\SharedServer;

use Bluewing\SharedServer\Requests\LoginRequest;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Foundation\Auth\RedirectsUsers;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

/**
 * This trait provides a `login` method and associated functionality for validating a user and logging them into the
 * application. Based off the Laravel-default `AuthenticatesUsers` trait, it is included in `LoginController`, making
 * use of `RedirectsUsers` and `ThrottlesLogins`—similarly to `AuthenticatesUsers`.
 *
 * @see Illuminate\Foundation\Auth\AuthenticatesUsers
 *
 * @package App\Base
 */
trait BluewingAuthenticatesUsers {

    use RedirectsUsers, ThrottlesLogins;

    /**
     * Logs a user into the application.
     *
     * Receives a `LoginRequest` object, which contains the email address, password, and id of the `Organization` thw
     * `User` is trying to log in to. If the user has too many login requests, they will be throttled and locked out
     * of the application.
     *
     * Otherwise, a login attempt will be made, and if successful, a `204 No Content` will be returned to the user
     * along with a JSON Web Token residing in the `Authorization` header representing their authenticity. It is the
     * client's responsibility to return this token on future requests to confirm their claim.
     *
     * If the login attempt was not successful, a `TODO: determine correct status code` response will be returned,
     * and the login attempt counter will be incremented.
     *
     * @bluewing-http-method POST
     * @bluewing-url /user/login
     * @bluewing-auth Guest
     *
     * @param LoginRequest $request - The `Request` associated with the login attempt.
     *
     * @return ResponseFactory|Response|void
     *
     * @throws ValidationException
     *
     * @see Illuminate\Foundation\Auth\AuthenticatesUsers::login()
     */
    public function login(LoginRequest $request) {
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     *
     * @param LoginRequest $request
     *
     * @return
     *
     * @see AuthenticatesUsers::attemptLogin()
     */
    protected function attemptLogin(LoginRequest $request) {
        return $this->guard()->validate($this->credentials($request));
    }

    /**
     * TODO: Fill out completely
     * @param LoginRequest $request
     *
     * @return ResponseFactory|Response A `Response` of `204 No Content` with the associated JWT attached as to the
     * `Authorization` header on the `Response` object.
     */
    protected function sendLoginResponse(LoginRequest $request) {
        $this->clearLoginAttempts($request);
        $token = $this->jwtManager->buildTokenFor($this->guard()->user());
        return response($this->guard()->user(), 200)->header('Authorization', $token);
    }

    /**
     * TODO: Fill out completely.
     */
    protected function sendFailedLoginResponse(LoginRequest $request) {
        return response(null, 401);
    }

    /**
     * Retrieves the credentials from the `Request` object needed to validate the `User`.
     *
     * @param LoginRequest $request - The `Request` associated with the login attempt.
     *
     * @return array An array representing the three properties needed to validate the `User`.
     */
    protected function credentials(LoginRequest $request) {
        return $request->only($this->username(), 'password');
    }

    /**
     * Get the login username to be used by the controller. The term "username" refers to the identifier
     * that the user provides for themselves to be identified.
     *
     * @return string The associated key that returns the unique username/identifier for the `User`.
     */
    protected function username()
    {
        return 'email';
    }

    /**
     * Retrieves the current `Guard` instance from the `Auth` facade. Because the default guard is set to
     * `JwtGuard` in the `config/auth.php` file, `Auth::guard()` returns an instance of it without needing
     * to specify a parameter ('jwt').
     *
     * @return mixed The `JwtGuard`
     *
     * @see App\Base\Guards\JwtGuard
     */
    protected function guard() {
        return Auth::guard();
    }
}
