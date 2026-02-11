<?php

namespace Teguh02\Rijanphp\Core\Cookie;

class CookieManager
{
    public function encrypt($value)
    {
        return encrypt($value);
    }

    public function decrypt($payload)
    {
        return decrypt($payload);
    }
}
