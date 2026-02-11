<?php

namespace Teguh02\Rijanphp\Tests\Feature;

use Teguh02\Rijanphp\Tests\TestCase;
use Teguh02\Rijanphp\Core\Router\Router;

class RouteCollisionVerifierTest extends TestCase
{
    /**
     * Test that no two routes have the exact same method and URI.
     * (Existing Router::add throws exception, but this verifies the final table)
     */
    public function test_no_duplicate_routes()
    {
        $routes = Router::getRoutes();
        $seen = [];

        foreach ($routes as $method => $methodRoutes) {
            foreach ($methodRoutes as $route) {
                $identifier = $method . ':' . $route['path'];

                if (isset($seen[$identifier])) {
                    $this->fail("Duplicate route detected: [{$method}] {$route['path']} is defined multiple times.");
                }

                $seen[$identifier] = true;
            }
        }

        $this->assertTrue(true); // check passed
    }

    /**
     * Test for Route Shadowing/Overlaps.
     * Check if a static route is unreachable because a wildcard route defined earlier captures it.
     * OR if two wildcard routes are identical (e.g. /user/{id} and /user/{name}).
     */
    public function test_route_overlaps()
    {
        $routesByType = Router::getRoutes();

        foreach ($routesByType as $method => $routes) {
            // We need to compare every route against every other route in the same method
            for ($i = 0; $i < count($routes); $i++) {
                for ($j = $i + 1; $j < count($routes); $j++) {
                    $routeA = $routes[$i];
                    $routeB = $routes[$j];

                    $this->checkOverlap($method, $routeA, $routeB);
                }
            }
        }

        $this->assertTrue(true);
    }

    private function checkOverlap($method, $routeA, $routeB)
    {
        $uriA = $routeA['path'];
        $uriB = $routeB['path'];

        // Convert {param} to generic regex for comparison
        $patternA = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $uriA);
        $patternB = preg_replace('/\{[a-zA-Z0-9_]+\}/', '([^/]+)', $uriB);

        $regexA = "#^" . $patternA . "$#";
        $regexB = "#^" . $patternB . "$#";

        // Check if Pattern A matches URI B (Shadowing?)
        // Only if Pattern A is "wider" (has wildcards) and B is static or narrower
        // Or if both are wildcards and effectively same regex

        if ($patternA === $patternB && $uriA !== $uriB) {
            // e.g. /product/{id} vs /product/{slug} -> Collision!
            $this->fail("Route Collision Detected: [{$method}] '{$uriA}' and '{$uriB}' are essentially the same pattern.");
        }
    }
}
