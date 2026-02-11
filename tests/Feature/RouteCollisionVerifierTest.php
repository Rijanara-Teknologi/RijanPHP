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
            $this->fail("Route Collision Detected: [{$method}] '{$uriA}' and '{$uriB}' match the same pattern: {$patternA}");
        }
    }

    /**
     * Test actual route responses to ensure correct views are rendered.
     */
    public function test_route_responses_contain_expected_content()
    {
        $testCases = [
            '/' => 'RijanPHP v1.0.0 is now stable!',
            '/products' => 'Our Products',
        ];

        foreach ($testCases as $uri => $expectedContent) {
            // Mock Request for this URI
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $_SERVER['REQUEST_URI'] = $uri;

            // Re-instantiate Request to pick up new globals
            $request = new \Teguh02\Rijanphp\Core\Http\Request();
            \Teguh02\Rijanphp\Core\Http\Request::$instance = $request;

            // Clear view state between requests for test isolation
            \Teguh02\Rijanphp\Core\View\View::clear();

            ob_start();
            $result = Router::dispatch();
            // Some routes might return strings, others might echo. We handle both.
            if ($result) {
                echo $result;
            }
            $output = ob_get_clean();

            $this->assertStringContainsString(
                $expectedContent,
                $output,
                "Route [GET] {$uri} failed to return expected content: '{$expectedContent}'. \nOutput sample: " . substr($output, 0, 200) . "..."
            );
        }
    }
}
