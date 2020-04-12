<?php

namespace Bluewing\Auth\Concerns;

use Illuminate\Contracts\Auth\Guard;

trait SetsLastLoggedInTenancy
{
    /**
     * Grabs the `User` object off the `Guard`, and sets the `lastLoggedInOrganizationId`
     * on the `User`. This will be called whenever the `User` signs up, or whenever they
     * switch accounts.
     *
     * It needn't be called when the `User` logs in, because they'll be logged into the
     * last `Organization` anyway.
     */
    public function setLastLoggedInTenancy()
    {
        $user = $this->guard()->user()->user;
        $user->lastLoggedInOrganization->save($this->guard()->user()->organization);
    }

    /**
     * Defines a function that returns an instance of a `Guard` contract (in our case, the `JwtGuard`).
     *
     * @returns Guard - An instance of `JwtGuard`.
     */
    abstract public function guard(): Guard;
}
