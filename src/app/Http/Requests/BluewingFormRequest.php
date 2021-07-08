<?php


namespace Bluewing\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class BluewingFormRequest extends FormRequest
{

    /**
     * The array of allowed properties that are accepted as part of the `FormRequest` that undergo no validation.
     * Providing additional properties to an API endpoint that utilizes `BluewingFormRequest` will result in validation
     * errors.
     *
     * @var string[]
     */
    protected array $allowlist = [];

    /**
     * Get the validator instance for the request. Provides additional functionality to validate an optional
     * `allowlist` property on the `FormRequest`, ensuring that no unexpected keys exist.
     *
     * @return Validator
     *
     * @throws ValidationException
     */
    protected function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();

        $keysNotInAllowList = array_diff(
            array_keys($this->request->all()),
            $this->allowlist,
            array_keys($this->container->call([$this, 'rules']))
        );

        if (count($keysNotInAllowList) > 0) {
            $errors = array_reduce($keysNotInAllowList, function($carry, $prop) {
                $carry[$prop] = ["\"{$prop}\" property not allowed here."];
                return $carry;
            }, []);

            throw ValidationException::withMessages($errors);
        }

        return $validator;
    }
}
