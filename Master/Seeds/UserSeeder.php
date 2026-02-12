<?php

namespace Teguh02\Rijanphp\Master\Seeds;

use Teguh02\Rijanphp\Core\Database\Seeder\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        db()->table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => password_hash('password', PASSWORD_BCRYPT),
        ]);

        db()->table('users')->insert([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => password_hash('password', PASSWORD_BCRYPT),
        ]);
    }
}
