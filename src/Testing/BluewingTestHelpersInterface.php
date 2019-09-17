<?php

namespace Bluewing\Testing;

use Illuminate\Foundation\Testing\TestResponse;

interface BluewingTestHelpersInterface {
    public function signUp(array $props = []): TestResponse;

    public function organizationVerify(string $token): TestResponse;

    public function userVerify(string $token): TestResponse;

    public function logIn(string $email, string $password): array;
}