<?php

namespace Teguh02\Rijanphp\Modules\Product\Controllers;

use Teguh02\Rijanphp\Core\Controller\Controller;
use Teguh02\Rijanphp\Master\Models\User;

class UserController extends Controller
{
    public function index()
    {
        $userModel = new User();
        $users = $userModel->findAll();

        return view('product::users', ['users' => $users]);
    }
}
