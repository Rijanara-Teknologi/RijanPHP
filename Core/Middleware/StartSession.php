<?php

namespace Teguh02\Rijanphp\Core\Middleware;

use Teguh02\Rijanphp\Core\Session\Session;

class StartSession
{
    public function handle()
    {
        Session::start();
    }
}
