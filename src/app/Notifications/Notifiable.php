<?php

namespace Bluewing\Notifications;

use Illuminate\Notifications\RoutesNotifications;
use Bluewing\Notifications\HasDatabaseNotifications as BluewingHasDatabaseNotifications;

/**
 * Overrides the `HasDatabaseNotifications` trait in favour of a custom implementation that provides a different
 * Eloquent relation definition.
 */
trait Notifiable
{
    use BluewingHasDatabaseNotifications, RoutesNotifications;
}