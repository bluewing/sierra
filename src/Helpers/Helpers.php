<?php


namespace Bluewing\Helpers;

if (!function_exists('getFullModelNamespace')) {
    /**
     * Helper function to retrieve the correct namespace of the specified model file.
     *
     * @param string $modelName
     *
     * @return string
     */
    function getFullModelNamespace(string $modelName): string {
        return config('bluewing.namespace') . '\\' . $modelName;
    }
}
