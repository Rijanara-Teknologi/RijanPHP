<?php

namespace Teguh02\Rijanphp\Master\Models;

use Teguh02\Rijanphp\Core\Model\Model;

class User extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $returnType = 'object'; // Return objects by default for easier usage

    protected $allowedFields = [
        'name',
        'email',
        'password'
    ];

    protected $useTimestamps = true;
}
