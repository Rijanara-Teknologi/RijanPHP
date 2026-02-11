<?php

namespace Teguh02\Rijanphp\Master\Middleware;

use Teguh02\Rijanphp\Core\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        // 'api/*',
        // 'webhook/*',
    ];
}
