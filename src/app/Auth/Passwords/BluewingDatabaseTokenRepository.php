<?php


namespace Bluewing\Auth\Passwords;


use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Carbon;

/**
 * We utilize a custom `BluewingDatabaseTokenRepository` to override the field name for the 'created_at` column in
 * the password resets table, setting it to `createdAt`.
 *
 * @package Bluewing\Auth\Passwords
 */
class BluewingDatabaseTokenRepository extends DatabaseTokenRepository
{
    private const CREATED_AT = 'createdAt';

    /**
     * @return string
     */
    private function getCreatedAtColumnName(): string
    {
        return self::CREATED_AT;
    }

    /**
     * Build the record payload for the table.
     *
     * @param  string  $email
     * @param  string  $token
     *
     * @return array
     */
    protected function getPayload($email, $token)
    {
        return ['email' => $email, 'token' => $this->hasher->make($token), $this->getCreatedAtColumnName() => new Carbon];
    }

    /**
     * Determine if a token record exists and is valid.
     *
     * @param CanResetPasswordContract $user
     * @param  string  $token
     * @return bool
     */
    public function exists(CanResetPasswordContract $user, $token)
    {
        $record = (array) $this->getTable()->where(
            'email', $user->getEmailForPasswordReset()
        )->first();

        return $record &&
            ! $this->tokenExpired($record[$this->getCreatedAtColumnName()]) &&
            $this->hasher->check($token, $record['token']);
    }

    /**
     * Determine if the given user recently created a password reset token.
     *
     * @param CanResetPasswordContract $user
     * @return bool
     */
    public function recentlyCreatedToken(CanResetPasswordContract $user)
    {
        $record = (array) $this->getTable()->where(
            'email', $user->getEmailForPasswordReset()
        )->first();

        return $record && $this->tokenRecentlyCreated($record[$this->getCreatedAtColumnName()]);
    }

    /**
     * Delete expired tokens.
     *
     * @return void
     */
    public function deleteExpired()
    {
        $expiredAt = Carbon::now()->subSeconds($this->expires);

        $this->getTable()->where($this->getCreatedAtColumnName(), '<', $expiredAt)->delete();
    }
}
