<?php


namespace Bluewing\Providers;

use Illuminate\Support\ServiceProvider;
use Stripe\Stripe;

class StripeServiceProvider extends ServiceProvider
{
    /**
     * Sets the Stripe API key for the session.
     *
     * @return void
     */
    public function register()
    {
        Stripe::setApiKey(config('bluewing.payments.secretKey'));
    }

    /**
     * @return void
     */
    public function boot()
    {

    }
}
