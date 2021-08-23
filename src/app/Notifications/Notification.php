<?php

namespace Bluewing\Notifications;

use Illuminate\Notifications\Notification as BaseNotification;

abstract class Notification extends BaseNotification
{
    /**
     * The disposition that defines how the notification should be displayed by the receiver.
     *
     * @var
     */
    public $disposition;

    /**
     * Sets the `disposition` property of the `Notification` model.
     * 
     * @param $disposition - The disposition to be set on the `Notification`.
     *
     * @return $this - An instance of the `Notification` class, to allow for fluent chaining.
     */
    public function disposition($disposition): self
    {
        $this->disposition = $disposition;
        return $this;
    }
}