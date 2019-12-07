<?php

namespace Bluewing\Services;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router;

class ApiEndpointGenerator {

    /**
     * @var Router
     */
    protected $router;

    /**
     * ApiEndpointGenerator constructor.
     *
     * @param Router $router - The dependency-injected instance of Illuminate\Routing\Router
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Retrieves the API endpoints for the application.
     *
     * @return array - The ApiEndpoints ready for database insertion.
     */
    public function getApiEndpoints(): array
    {
        // Returns a route collection
        $routeCollection = $this->router->getRoutes();

        $results = [];

        foreach ($routeCollection as $route) {
            $results[] = $this->getRouteInformation($route);
        }

        return $results;
    }

    /**
     * Gathers the route information for a `Route`, and maps it into an array of properties
     * to be inserted into an `ApiEndpoint` entity.
     *
     * @param Route $route - The `Route` to retrieve information for.
     *
     * @return array - An `array` containing the information for a specified `Route`.
     */
    protected function getRouteInformation(Route $route): array
    {
        return [
            'key'           => $this->buildKeyForRoute($route),
            'method'        => $route->methods()[0],
            'value'         => $this->buildUriForRoute($route->uri()),
            'description'   => null
        ];
    }

    /**
     * Each `Route` needs to be uniquely identified. We do this by mutating the uri of the `Route`.
     *
     * First, we explode the route into segments, then remove any empty or non-suffix route segment placeholders.
     * Then, we iterate over each route segment, concatenate them together, and then use the method and
     * contents of the final route segment to determine the name of the API endpoint key.
     *
     * @param Route $route - The `Route` that should be keyed.
     *
     * @return string - The key for which the `Route` should be stored.
     */
    protected function buildKeyForRoute(Route $route): string
    {
        // Return the route name, if it's defined.
        if (!is_null($route->getName())) {
            return $route->getName();
        }

        // Explode into segments
        $routeSegments = explode('/', $route->uri());

        // Filter out blank & non-suffix route parameters
        $filteredRouteSegments = array_values(array_filter($routeSegments, function($segment, $index) use($routeSegments) {
            if ($index === count($routeSegments) - 1) return true;
            return $this->isSegmentARouteParameter($segment);
        }, ARRAY_FILTER_USE_BOTH));

        // The final string
        $apiEndpointKey = '';

        // Iterate over every filtered route segment
        foreach($filteredRouteSegments as $index => $routeSegment) {
            // If we have the final route segment
            if ($index === count($filteredRouteSegments) - 1) {
                $apiEndpointKey .= $this->mapMethodToKeySuffix($route->methods()[0], $routeSegment);
            } else {
                $apiEndpointKey .= '.' . $routeSegment;
            }
        }

        return trim($apiEndpointKey, '.');
    }

    /**
     * Laravel handles route parameters by encapsulating the parameter in a set of `{}` braces, while
     * Angular chooses to prefix the route parameter with `:`. We map these across here.
     *
     * @param string $routeUrl - The uri of the `Route`.
     *
     * @return string - The uri for the client to handle the `Route`.
     */
    protected function buildUriForRoute(string $routeUrl): string
    {
        return preg_replace('/{(.*?)}/', ':$1', $routeUrl);
    }

    /**
     * Given the last route segment and method, determine the suffix of the API endpoint key.
     *
     * @param string $method - The HTTP verb that the endpoint is associated with.
     * @param string $routeSegment - The suffixed route segment which should be evaluated to determine
     * the API endpoint key.
     *
     * @return string - The suffix for the API endpoint key.
     */
    protected function mapMethodToKeySuffix(string $method, string $routeSegment): string
    {
        $appendedSuffix = '';
        if (preg_match('/^{.*}$/', $routeSegment) === 1) {
            switch ($method) {
                case 'GET':
                    $appendedSuffix = 'show'; break;
                case 'PATCH':
                case 'PUT':
                    $appendedSuffix = 'update'; break;
                case 'DELETE':
                    $appendedSuffix = 'destroy'; break;
            }
        } else {
            switch ($method) {
                case 'GET':
                    $appendedSuffix .= $routeSegment . '.index'; break;
                case 'POST':
                    $appendedSuffix .= $routeSegment . '.store'; break;
            }
        }

        return '.' . $appendedSuffix;
    }

    /**
     * Helper function to determine if the provided segment is a route parameter.
     *
     * @param string $segment - The segment to check.
     *
     * @return `true` if the segment is a route parameter, `false` otherwise.
     */
    protected function isSegmentARouteParameter(string $segment): bool
    {
        return !is_null($segment) && preg_match('/{.*}/', $segment) === 0;
    }
}
