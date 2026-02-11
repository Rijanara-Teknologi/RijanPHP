<?php defined('RIJANPHP') or die('Access denied.');

use Rijanphp\Modules\Homepage\HomePage;

return [
    'modules' => \Teguh02\Rijanphp\Core\Modules\Module::load(
        new HomePage(),

        // You can add more modules here        
    ),
];