<?php

namespace Bluewing\Providers;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * The `BluewingUserProvider` provides an extension of the default Laravel `EloquentUserProvider` implementation
 * to override the `retrieveByCredentials` and `retrieveByToken` methods. Although we still use Eloquent and a
 * traditional relational database, our user schema is spread out over a few tables, `Member`, `User`, and
 * `Organization`. This means retrieving by credentials or via a token is slightly more convoluted than it
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
     * Retrieve a Bluewing `Member` by the credential needed to identify the `User` instance: the
     * `User`'s email. From here, the last logged in `Organization` id is used to provide the exact `Member`.
     *
     * @param array $credentials
     *
     * @return Builder|Model|object|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        if (!array_key_exists('email', $credentials)) {
            return null;
        }

        // Retrieve all possible `Member`'s.
        $members = $this->newModelQuery()
            ->with(['user', 'organization', 'roles'])
            ->whereHas('user', fn($q) => $q->where('email', $credentials['email']))
            ->get();

        // The email address is invalid, or the `User` no longer has any `Member`'s they can log into.
        if ($members->count() === 0) {
            return null;
        }

        // Retrieve the only possible `Member`.
        if ($members->count() === 1) {
            return $members->first();
        }

        // Retrieve the `Member` the `User` was last logged in as.
        $members->first(fn($member) => $member->organization->id === $member->user->lastLoggedInOrganizationId);
    }

    /**
     * Retrieve a user by their unique identifier.
     *
     * Note that unlike the `UserProvider` class, this method will never return `null`. If a `Member` ID is
     * provided that does correspond with any `Member`, null would normally be returned. This would allow
     * the JwtGuard class to not apply a tenancy scope to the appropriate queries, allowing tenancy-wide access.
     *
     * Fortunately, it would only be possible to provide a `Member` ID that does not exist by altering a
     * client JSON Web Token with knowledge of the application key. As JWT's are immutable, this is therefore not a
     * security concern. However, during development where such an event is possible, it is helpful to return 401
     * Unauthorized as a reminder that the `Member` does not exist.
     *
     * @param mixed $identifier - The identifier used to identify an `Authenticatable`.
     *
     * @return Authenticatable|null
     *
     * @throws AuthenticationException - An `AuthenticationException` will be thrown if no model is found that
     * matches the provided identifier.
     */
    public function retrieveById($identifier)
    {
        $model = $this->createModel();

        try {
            return $this->newModelQuery($model)
                ->withoutGlobalScopes()
                ->where($model->getAuthIdentifierName(), $identifier)
                ->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new AuthenticationException;
        }
    }

    /**
     * TODO: implement.
     *
     * @param mixed $identifier
     * @param string $token
     *
     * @return Authenticatable|void|null
     */
    public function retrieveByToken($identifier, $token) {

    }
}
