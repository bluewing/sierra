<?php

namespace Bluewing\Notifications\Channels;

use Illuminate\Notifications\Channels\DatabaseChannel as BaseDatabaseChannel;
use Illuminate\Notifications\Notification;

class DatabaseChannel extends BaseDatabaseChannel
{
    /**
     * Build an array payload for the `DatabaseNotification` Model. This is customized to have the `read_at` property
     * to be camelcased, and also provides a `disposition` property that customizes how the notification should be
     * displayed when received. Finally, this class is bound in the `AppServiceProvider` to replace the
     * `BaseDatabaseChannel` provided by `Illuminate`.
     *
     * @param  mixed  $notifiable
     * @param Notification $notification
     * @return array
     */
    protected function buildPayload($notifiable, Notification $notification)
    {
        return [
            'id'            => $notification->id,
            'disposition'   => property_exists($notification, 'disposition') ? $notification->disposition : null,
            'type'          => get_class($notification),
            'data'          => $this->getData($notifiable, $notification),
            'readAt'        => null,
        ];
    }
}