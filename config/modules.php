<?php defined('RIJANPHP') or die('Access denied.');

use Teguh02\Rijanphp\Modules\Homepage\HomePage;

return [
    'modules' => \Teguh02\Rijanphp\Core\Modules\Module::load(
        new HomePage(),
    ),
];
