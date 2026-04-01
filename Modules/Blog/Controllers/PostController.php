<?php
namespace Teguh\Rijanphp\Modules\Blog\Controllers;

use Teguh02\Rijanphp\Core\Controller\Controller;

class PostController extends Controller
{
    public function index()
    {
        return view('Post.index');
    }

    public function show($id)
    {
        return view('Post.show', ['id' => $id]);
    }

    public function store()
    {
        $data = $this->request->all();
        return response()->json(['status' => 'created', 'data' => $data]);
    }

    public function update($id)
    {
        $data = $this->request->all();
        return response()->json(['status' => 'updated', 'id' => $id, 'data' => $data]);
    }

    public function destroy($id)
    {
        return response()->json(['status' => 'deleted', 'id' => $id]);
    }
}