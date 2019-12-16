<?php

namespace Bluewing\Guards;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

/**
 * JwtGuard provides the implementation for JSON Web Token authentication of users. Laravel definition is:
 * "Guards define how users are authenticated for each request". The default `Guard` used by Angular is `SessionGuard`,
 * this has been swapped out for `JwtGuard` with the configuration in the `default` key in `config/auth.php.
 *
 * The `GuardHelper`'s trait has been used, as many of the methods used by one guard are used by another.
 *
 * TODO: Implement events. What did this mean?
 *
 * @see Illuminate\Auth\SessionGuard
 */
class JwtGuard implements Guard
{
    use GuardHelpers;

    /**
     * The `Request` object associated with the execution lifecycle.
     *
     * @var Request
     */
    protected Request $request;

    /**
     * The ID of the `UserOrganization`. This custom property can be set if the
     * @var string
     */
    protected string $id;

    /**
     * Constructor for `JwtGuard`.
     *
     * @param UserProvider $provider - The `UserProvider` used to handle the user.
     * @param Request $request - The `Request` object being guarded.
     */
    public function __construct(UserProvider $provider, Request $request)
    {
        $this->provider = $provider;
        $this->request = $request;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|null
     */
    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        if (!is_null($this->id)) {
            return $this->provider->retrieveById($this->id);
        }

        return null;
    }

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        $user = $this->provider->retrieveByCredentials($credentials);

        if (!is_null($user) && $this->provider->validateCredentials($user, $credentials)) {
            $this->setUser($user);
            return true;
        }

        return false;
    }

    /**
     * @param $id
     */
    public function setUserId($id) {
        $this->id = $id;
    }
}
