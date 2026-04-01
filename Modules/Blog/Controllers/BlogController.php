<?php
namespace Teguh\Rijanphp\Modules\Blog\Controllers;

use Teguh02\Rijanphp\Core\Controller\Controller;

class BlogController extends Controller
{
    public function index()
    {
        return view('index', [
            'title' => 'Blog Module'
        ]);
    }
}