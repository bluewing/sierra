<?php

namespace Bluewing;

use Bluewing\Models\User;
use Bluewing\Requests\LoginRequest;

trait SetsLastLoggedInTenancy
{
    public function setLastLoggedInTenancy(LoginRequest $request) {
        $user = $this->guard()->user()->user;
        $user->lastLoggedInOrganizationId = $this->guard()->user()->organization->id;
        $user->save();
    }
}
