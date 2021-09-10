<?php

namespace Bluewing\Rules\Support;

trait HasCustomizableMessage
{
    /**
     * The customized message.
     *
     * @var string
     */
    protected string $customMessage;

    /**
     * Allows a custom validation rule to provide a custom `message` instead of the default message specified by the
     * rule.
     *
     * @param string $message - The customized message to be displayed if the validation process fails. This provides
     * additional customization over the built-in overrides for e
     *
     * @return self - The instance of the `Rule` that this trait is being applied to.
     */
    public function withCustomMessage(string $message): self
    {
        $this->customMessage = $message;
        return $this;
    }

    /**
     * Get the validation error message, either by retrieving a set custom message, or by calling the
     * `defaultMessage` method, if it exists on the `Rule`.
     *
     * @return string - The validation error message.
     *
     * @throws RuleMissingDefaultMessageMethod - This exception will be thrown if the `defaultMessage` method is not
     * provided.
     */
    public function message()
    {
        if (isset($this->customMessage)) {
            return $this->customMessage;
        }

        if (method_exists($this, 'defaultMessage')) {
            return $this->defaultMessage();
        }

        throw RuleMissingDefaultMessageMethod::forRule($this);
    }
}