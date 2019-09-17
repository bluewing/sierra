<?php

namespace Bluewing\Providers;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * The `BluewingUserProvider` provides an extension of the default Laravel `EloquentUserProvider` implementation
 * to override the `retrieveByCredentials` and `retrieveByToken` methods. Although we still use Eloquent and a
 * traditional relational database, our user schema is spread out over a few tables, `UserOrganization`, `User`,
 * and `Organization`. This means retrieving by credentials or via a token is slightly more convoluted than it
 * otherwise would be.
 *
 * This class is registered as the UserProvider in the `AuthServiceProvider::boot` method via the
 * `Auth::provider` call.
 *
 * @see Illuminate\Auth\EloquentUserProvider
 * @see Illuminate\Contracts\Auth\UserProvider
 */
class BluewingUserProvider extends EloquentUserProvider implements UserProvider
{
    /**
     * Retrieve a Bluewing `UserOrganization` by the credential needed to identify the `User` instance: the
     * `User`'s email. From here, the last logged in `Organization` id is used to provide the exact `UserOrganization`.
     *
     * @param array $credentials
     * @return Builder|Model|object
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (!array_key_exists('email', $credentials)) {
            return null;
        }

        // Retrieve all possible `UserOrganization`'s.
        $userOrganizations = $this->newModelQuery()
            ->with(['user', 'organization'])
            ->whereHas('user', function($q) use($credentials) {
                return $q->where('email', $credentials['email']);
            })->get();

        // The email address is invalid, or the User no longer has any UserOrganization`'s they can log into.
        if ($userOrganizations->count() === 0) {
            return null;
        }

        // Retrieve the only possible `UserOrganization`.
        if ($userOrganizations->count() === 1) {
            return $userOrganizations->first();
        }

        // Retrieve the `UserOrganization` the `User` was last logged in as.
        $userOrganizations->first(function($userOrganization) {
            return $userOrganization->organization->id === $userOrganization->user->lastLoggedInOrganizationId;
        });
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $identifier - The identifier used to identify an `Authenticatable`.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        $model = $this->createModel();

        return $this->newModelQuery($model)
            ->withoutGlobalScopes()
            ->where($model->getAuthIdentifierName(), $identifier)
            ->first();
    }

    /**
     * TODO: implement.
     *
     * @param mixed $identifier
     * @param string $token
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|void|null
     */
    public function retrieveByToken($identifier, $token) {

    }
}
