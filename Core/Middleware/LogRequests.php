<?php

namespace Teguh02\Rijanphp\Core\Middleware;

class LogRequests
{
    public function handle()
    {
        $server = \Teguh02\Rijanphp\Core\Rijan::$instance->server ?? $_SERVER;

        $method = $server['REQUEST_METHOD'] ?? 'UNKNOWN';
        $uri = $server['REQUEST_URI'] ?? '/';
        $ip = $server['REMOTE_ADDR'] ?? 'UNKNOWN';

        log_info("Request: {$method} {$uri} from {$ip}");
    }
}
