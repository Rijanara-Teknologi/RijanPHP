<?php

namespace Teguh02\Rijanphp\Master\Seeds;

use Teguh02\Rijanphp\Core\Database\Seeder\Seeder;

class TestSeeder extends Seeder
{
    public function run()
    {
        db()->table('users')->insert([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => password_hash('password', PASSWORD_BCRYPT),
        ]);
    }
}
