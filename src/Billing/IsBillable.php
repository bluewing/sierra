<?php

namespace Bluewing\Billing;

use Stripe\Customer;
use Stripe\Exception\ApiErrorException;

trait IsBillable
{

    /**
     * Creates a customer record via Stripe's API for the billable model, then saves the
     * returned customer ID onto the billable model's `paymentProviderCustomerId` property.
     *
     * @see https://stripe.com/docs/api/customers/create
     *
     * @return Customer - The created customer object.
     *
     * @throws ApiErrorException
     */
    public function createCustomer(): Customer
    {
        $customer = Customer::create([
            'name'  => $this->name,
            'email' => $this->email
        ]);

        $this->paymentProviderCustomerReference = $customer['id'];
        $this->save();

        return $customer;
    }

    /**
     * Updates the customer record for this billable model via Stripe's API to contain the
     * latest information about the customer.
     *
     * @see https://stripe.com/docs/api/customers/update
     *
     * @return Customer - The updated customer object.
     *
     * @throws ApiErrorException
     */
    public function syncCustomer(): Customer
    {
        return Customer::update($this->paymentProviderCustomerReference, [
            'name'  => $this->name,
            'email' => $this->email
        ]);
    }

    /**
     * Adds a payment method for the `Organization` from the provided token parameter.
     *
     * @param string $token
     *
     * @return IsBillable
     *
     * @throws ApiErrorException
     */
    public function addPaymentMethod(string $token)
    {
        $card = Customer::createSource($this->paymentProviderCustomerId, [
            'source' => $token
        ]);

        // Create Payment Method entry for our records.

        return $this;
    }

    /**
     * @see
     *
     * @return
     */
    public function updatePaymentMethod(string $token)
    {

    }

    /**
     * @see
     *
     * @return
     */
    public function removePaymentMethod(string $token)
    {

    }

    /**
     * @see
     *
     * @return
     */
    public function setDefaultPaymentMethod(string $token)
    {

    }

    /**
     * Retrieves the default payment method,
     *
     * @see
     *
     * @return
     */
    public function getPaymentMethod(string $token = null)
    {

    }

    /**
     * Charge the provided `Organization` for the given amount using their preferred
     * payment method.
     *
     * @see
     *
     * @return
     */
    public function charge(int $amount)
    {

    }

    /**
     *
     * @see
     *
     * @return
     */
    public function beginSubscription()
    {

    }
}
