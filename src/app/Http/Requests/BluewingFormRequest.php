<?php


namespace Bluewing\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class BluewingFormRequest extends FormRequest
{

    /**
     * Get the validator instance for the request. Provides additional functionality to validate an optional
     * `whitelist` property on the `FormRequest`, ensuring that no unexpected keys exist.
     *
     * @return Validator
     *
     * @throws ValidationException
     */
    protected function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();

        if (property_exists($this, 'whitelist')) {

            $keysNotInWhitelist = array_diff(
                array_keys($this->request->all()),
                $this->whitelist,
                array_keys($this->container->call([$this, 'rules']))
            );

            if (count($keysNotInWhitelist) > 0) {
                $errors = [];

                foreach ($keysNotInWhitelist as $property) {
                    $errors[$property] = ['Property not allowed here.'];
                }

                throw ValidationException::withMessages($errors);
            }
        }

        return $validator;
    }
}
