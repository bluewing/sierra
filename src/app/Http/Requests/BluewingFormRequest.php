<?php


namespace Bluewing\Http\Requests;

use Exception;
use Illuminate\Contracts\Support\Arrayable;
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
     * @return Validator -
     *
     * @throws ValidationException -
     * @throws Exception -
     */
    protected function getValidatorInstance()
    {
        $validator = parent::getValidatorInstance();

        $keysNotInAllowList = array_diff(
            array_keys($this->request->all()),
            $this->allowlist,
            array_keys($this->getValidatorRules())
        );

        if (count($keysNotInAllowList) > 0) {
            $errors = array_reduce($keysNotInAllowList, function($carry, $prop) {
                $carry[$prop] = ["\"$prop\" property not allowed here."];
                return $carry;
            }, []);

            throw ValidationException::withMessages($errors);
        }
        return $validator;
    }

    /**
     * Fetches the validator rules from the `FormRequest`, ensuring they are returned as an `array` of rules.
     *
     * @throws Exception - An `Exception` will be thrown if the return value of the `rules` method of the `FormRequest`
     * is neither an `array` or an instance of `Arrayable`.
     */
    private function getValidatorRules(): array
    {
        $rules = $this->container->call([$this, 'rules']);

        if (is_array($rules)) {
            return $rules;
        } else if ($rules instanceof Arrayable) {
            return $rules->toArray();
        }

        throw new Exception('`rules` method of `FormRequest` is not callable.');
    }
}
