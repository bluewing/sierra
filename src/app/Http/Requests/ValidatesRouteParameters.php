<?php


namespace Bluewing\Http\Requests;


use Illuminate\Routing\Route;

trait ValidatesRouteParameters
{
    /**
     * @return array
     */
    public abstract function all();

    /**
     * @return Route
     */
    public abstract function route();

    /**
     * Include any route parameters in the array of data to be validated.
     *
     * @return array - The combined array of input data, and route parameters.
     */
    public function validationData()
    {
        return array_merge($this->all(), $this->route()->parameters());
    }
}
