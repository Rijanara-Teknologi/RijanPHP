<?php
namespace Teguh02\Rijanphp\Modules\Homepage\Controllers;

use Teguh02\Rijanphp\Core\Controller\Controller;

class IndexController extends Controller
{
    public function index()
    {
        return view('index', [
            'name' => 'RijanPHP',
            'version' => \Teguh02\Rijanphp\Core\Rijan::version()
        ]);
    }

    public function postExample()
    {
        $input = $this->request->input('message');
        return $this->json([
            'received' => $input,
            'status' => 'success'
        ]);
    }
}