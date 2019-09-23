<?php

namespace Bluewing;

trait SetsLastLoggedInTenancy
{
    public function setLastLoggedInTenancy() {
        $user = $this->guard()->user()->user;
        $user->lastLoggedInOrganizationId = $this->guard()->user()->organization->id;
        $user->save();
    }
}
