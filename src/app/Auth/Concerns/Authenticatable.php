<?php

namespace Bluewing\Auth\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Trait BluewingAuthentication
 * @package Bluewing
 */
trait Authenticatable {

    /**
     * Override the default remember token field name from `remember_token` to `rememberToken`,
     * to match our preferred camelCase style for database fields.
     */
    protected string $rememberTokenName = 'rememberToken';

    /**
     * Retrieves the name of the field associated with the identifier for the `MemberContract`
     * implementing class.
     *
     * @return string - The identifier name of the `MemberContract`.
     */
    public function getAuthIdentifierName()
    {
        return $this->getKeyName();
    }

    /**
     * Retrieve the actual identifier associated with the `MemberContract` implementing object.
     *
     * @return object
     */
    public function getAuthIdentifier()
    {
        return $this->{$this->getAuthIdentifierName()};
    }

    /**
     * Retrieve the password associated with the user. Because the `MemberContract` refers to the `Member`, this must
     * be retrieved from the `User` relationship.
     *
     * @return string - The `User`'s password.
     */
    public function getAuthPassword()
    {
        return $this->user->password;
    }

    /**
     * Retrieves the remember token from the `MemberContract`. Because Bluewing applications utilise
     * stateless JSON Web Tokens for authentication, this method is not needed.
     *
     * @return string|void - The remember token, if it exists.
     */
    public function getRememberToken()
    {
        if (! empty($this->getRememberTokenName())) {
            return (string) $this->{$this->getRememberTokenName()};
        }
    }

    /**
     * Sets the remember token. This is unused by the Bluewing because we use JWTs and Refresh Tokens.
     *
     * @param $value - The remember token to set.
     *
     * @return void
     */
    public function setRememberToken($value)
    {
        if (! empty($this->getRememberTokenName())) {
            $this->{$this->getRememberTokenName()} = $value;
        }
    }

    /**
     * @return string
     */
    public function getRememberTokenName()
    {
        return $this->rememberTokenName;
    }
}
