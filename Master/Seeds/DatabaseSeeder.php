<?php

namespace Teguh02\Rijanphp\Master\Seeds;

use Teguh02\Rijanphp\Core\Database\Seeder\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(TestSeeder::class);
    }
}
