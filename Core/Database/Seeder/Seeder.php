<?php

namespace Teguh02\Rijanphp\Core\Database\Seeder;

abstract class Seeder
{
    abstract public function run();

    public function call($class)
    {
        $seeder = new $class;
        $seeder->run();
    }
}
