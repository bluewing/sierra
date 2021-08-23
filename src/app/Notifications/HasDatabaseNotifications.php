<?php

namespace Bluewing\Notifications;

use Bluewing\Notifications\DatabaseNotification as BluewingDatabaseNotification;
use Illuminate\Notifications\HasDatabaseNotifications as BaseHasDatabaseNotifications;

trait HasDatabaseNotifications
{
    use BaseHasDatabaseNotifications;

    /**
     * Get the entity's notifications. Customizes the Eloquent relation to refer to the custom `DatabaseNotification`
     * class, and provides custom keys to support morphing on.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function notifications()
    {
        return $this->morphMany(BluewingDatabaseNotification::class, 'notifiable', 'notifiableType', 'notifiableId')
            ->orderBy('createdAt', 'desc');
    }
}