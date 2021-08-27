<?php

namespace Bluewing\Notifications;

use Illuminate\Notifications\Notification as BaseNotification;

abstract class Notification extends BaseNotification
{
    /**
     * The handler that defines how the notification should be displayed by the receiver, and handled when being
     * marked as read.
     */
    public int $handler;

    /**
     * Sets the `handler` property of the `Notification` model.
     * 
     * @param int $handler - The handler to be set on the `Notification`.
     *
     * @return $this - An instance of the `Notification` class, to allow for fluent chaining.
     */
    public function handler(int $handler): self
    {
        $this->handler =  $handler;
        return $this;
    }
}