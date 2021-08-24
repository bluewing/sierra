<?php

namespace Bluewing\Auth\Contracts;

interface Claimable
{
    public function getAuthIdentifier();
    public function getTenancyIdentifier();
    public function getClaimsForJwt(): ?array;
}