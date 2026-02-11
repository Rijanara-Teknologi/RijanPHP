<?php

namespace Teguh02\Rijanphp\Core\Session;

class SessionManager
{
    protected $driver = 'file';

    public function __construct()
    {
        // In the future, we can load driver from config
    }

    public function driver()
    {
        return $this->driver;
    }
}
