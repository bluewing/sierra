<?php

namespace Bluewing\SharedServer\Guards;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

/**
 * JwtGuard provides the implementation for JSON Web Token authentication of users. Laravel definition is:
 * "Guards define how users are authenticated for each request". The default `Guard` used by Angular is `SessionGuard`,
 * this has been swapped out for `JwtGuard` with the configuration in the `default` key in `config/auth.php
 *
 * TODO: Implement events
 *
 * @see Illuminate\Auth\SessionGuard
 */
class JwtGuard implements Guard
{
    protected $request;
    protected $provider;
    protected $user;
    protected $id;

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
     * Determine if the current user is authenticated.
     *
     * @return bool Whether the current user is authenticated.
     */
    public function check()
    {
        return !is_null($this->id);
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool Whether the current user is a guest.
     */
    public function guest()
    {
        return !$this->check();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (!is_null($this->user)) {
            return $this->user;
        }

        if (!is_null($this->id)) {
            return $this->provider->retrieveById($this->id);
        }
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|string|null
     */
    public function id()
    {
        return $this->user()->getAuthIdentifier();
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
     * Set the current user.
     *
     * @param \Illuminate\Contracts\Auth\Authenticatable $user
     * @return void
     */
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
    }

    /**
     * @param $id
     */
    public function setUserId($id) {
        $this->id = $id;
    }
}
