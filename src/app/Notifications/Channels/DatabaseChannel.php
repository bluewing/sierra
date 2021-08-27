<?php

namespace Bluewing\Notifications\Channels;

use Illuminate\Notifications\Channels\DatabaseChannel as BaseDatabaseChannel;
use Illuminate\Notifications\Notification;

class DatabaseChannel extends BaseDatabaseChannel
{
    /**
     * Build an array payload for the `DatabaseNotification` Model. This is customized to have the `read_at` property
     * to be camelcased, and also provides a `handler` property that customizes how the notification should be
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
            'handler'       => property_exists($notification, 'handler') ? $notification->handler : null,
            'type'          => get_class($notification),
            'data'          => $this->getData($notifiable, $notification),
            'readAt'        => null,
        ];
    }
}