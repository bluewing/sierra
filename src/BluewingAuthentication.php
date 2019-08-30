<?php

namespace Bluewing\SharedServer;

trait BluewingAuthentication {

    /**
     * Override the default remember token field name from `remember_token` to `rememberToken`,
     * to match our preferred camelcase style for database fields.
     */
    protected $rememberTokenName = 'rememberToken';

    public function getUser() {
        return $this->user;
    }

    public function getTenant() {
        return $this->organization;
    }

    /**
     * Retrieves the name of the field associated with the identifier for the `BluewingAuthenticationContract`
     * implementing class.
     *
     * @return string The identifier name of the `BluewingAuthenticationContract`.
     */
    public function getAuthIdentifierName() {
        return $this->getKeyName();
    }

    /**
     * Retrieve the actual identifier associated with the `BluewingAuthenticationContract` implementing object.
     *
     * @return object
     */
    public function getAuthIdentifier() {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Retrieve the password associated with the user. Because the `BluewingAuthenticationContract` refers to the
     * `UserOrganization`, this must be retrieved from the `User` relationship.
     *
     * @return string The `User`'s password.
     */
    public function getAuthPassword() {
        return $this->getUser()->password;
    }

    /**
     * Retrieves the remember token from the `BluewingAuthenticationContract`. Because Bluewing applications utilise
     * stateless JSON Web Tokens for authentication, this method is not needed.
     */
    public function getRememberToken() {
        if (! empty($this->getRememberTokenName())) {
            return (string) $this->{$this->getRememberTokenName()};
        }
    }

    public function setRememberToken($value) {
        if (! empty($this->getRememberTokenName())) {
            $this->{$this->getRememberTokenName()} = $value;
        }
    }

    public function getRememberTokenName() {
        return $this->rememberTokenName;
    }
}
