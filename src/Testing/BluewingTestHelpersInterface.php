<?php

namespace Bluewing\SharedServer\Testing;

interface BluewingTestHelpersInterface {
    public function signUp(array $props = []);

    public function organizationVerify(string $token);

    public function userVerify(string $token);

    public function logIn(string $email, string $password);
}