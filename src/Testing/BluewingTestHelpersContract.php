<?php

namespace Bluewing\Testing;

use Illuminate\Foundation\Testing\TestResponse;

interface BluewingTestHelpersContract {

    /**
     * @param array $props
     *
     * @return TestResponse
     */
    public function signUp(array $props = []): TestResponse;

    /**
     * @param string $token
     *
     * @return TestResponse
     */
    public function organizationVerify(string $token): TestResponse;

    /**
     * @param string $token
     *
     * @return TestResponse
     */
    public function userVerify(string $token): TestResponse;

    /**
     * @param string $email
     * @param string $password
     *
     * @return array
     */
    public function logIn(string $email, string $password): array;
}
