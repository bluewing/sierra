<?php

namespace Bluewing\Notifications;

use Bluewing\Notifications\DatabaseNotification as BluewingDatabaseNotification;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Notifications\HasDatabaseNotifications as BaseHasDatabaseNotifications;

trait HasDatabaseNotifications
{
    use BaseHasDatabaseNotifications;

    /**
     * Get the entity's notifications. Customizes the Eloquent relation to refer to the custom `DatabaseNotification`
     * class, and provides custom keys to support morphing on.
     *
     * TODO: Members that are staff can also receive notifications that are on behalf of their `Organization`. In the
     * future, create a customized Eloquent `Relation` to supports this.
     *
     * @return MorphMany
     */
    public function notifications()
    {
        return $this->morphMany(BluewingDatabaseNotification::class, 'notifiable', 'notifiableType', 'notifiableId')
            ->orderBy('createdAt', 'desc');
    }
}