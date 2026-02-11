<?php

namespace Teguh02\Rijanphp\Core\Middleware;

use Teguh02\Rijanphp\Core\Security\Csrf;

class VerifyCsrfToken
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [];

    public function handle()
    {
        if (
            $this->isReading($this->request()) ||
            $this->inExceptArray($this->request()) ||
            $this->tokensMatch($this->request())
        ) {
            return; // Continue
        }

        throw new \Exception('CSRF token mismatch.');
    }

    /**
     * Determine if the HTTP request uses a "read" verb.
     *
     * @param  array  $request
     * @return bool
     */
    protected function isReading($request)
    {
        return in_array($request['server']['REQUEST_METHOD'], ['HEAD', 'GET', 'OPTIONS']);
    }

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param  array  $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        $uri = $request['server']['REQUEST_URI'] ?? '/';
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($uri == $except) {
                return true;
            }

            // Basic wildcard support could be added here
        }

        return false;
    }

    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  array  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        $token = $this->getTokenFromRequest($request);

        return is_string($token) &&
            is_string(Csrf::token()) &&
            hash_equals(Csrf::token(), $token);
    }

    /**
     * Get the CSRF token from the request.
     *
     * @param  array  $request
     * @return string|null
     */
    protected function getTokenFromRequest($request)
    {
        $token = $request['input']['_token'] ?? null;

        if (!$token) {
            $token = $request['server']['HTTP_X_CSRF_TOKEN'] ?? null;
        }

        return $token;
    }

    protected function request()
    {
        // Ideally use Request class, but for now generic array access via globals or Rijan instance
        return [
            'server' => \Teguh02\Rijanphp\Core\Rijan::$instance->server ?? $_SERVER,
            'input' => \Teguh02\Rijanphp\Core\Rijan::$instance->request ?? $_REQUEST,
        ];
    }
}
