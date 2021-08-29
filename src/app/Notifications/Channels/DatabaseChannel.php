<?php

namespace Bluewing\Notifications\Channels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Channels\DatabaseChannel as BaseDatabaseChannel;
use Illuminate\Notifications\Notification;

class DatabaseChannel extends BaseDatabaseChannel
{

    /**
     * Provides an additional hook to execute functionality prior to the addition of the `Notification` to the
     * database. `Notification`'s can use this by providing a `beforeToDatabase` method. This is useful for deleting
     * other `Notification`'s that have been superseded, for example.
     *
     * @param mixed $notifiable -
     * @param Notification $notification -
     *
     * @return Model - The created `Model` that has been added to the database.
     */
    public function send($notifiable, Notification $notification)
    {
        if (method_exists($notification, 'beforeToDatabase')) {
            $notification->beforeToDatabase($notifiable);
        }
        return parent::send($notifiable, $notification);
    }

    /**
     * Build an array payload for the `DatabaseNotification` Model. This is customized to have the `read_at` property
     * to be camelcased, and also provides a `handler` property that customizes how the notification should be
     * displayed when received. Finally, this class is bound in the `AppServiceProvider` to replace the
     * `BaseDatabaseChannel` provided by `Illuminate`.
     *
     * @param mixed $notifiable -
     * @param Notification $notification -
     *
     * @return array -
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