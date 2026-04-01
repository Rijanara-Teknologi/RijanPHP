<?php
namespace Teguh02\Rijanphp\Modules\Product\Controllers;

use Teguh02\Rijanphp\Core\Controller\Controller;
use Teguh02\Rijanphp\Modules\Product\Models\ProductModel;

class ProductController extends Controller
{
    public function index()
    {
        $products = model(ProductModel::class)->findAll();
        return view('index', [
            'products' => $products,
            'title' => 'All Products'
        ]);
    }

    public function show($id)
    {
        $product = model(ProductModel::class)->find($id);

        if (!$product) {
            return response()->status(404)->setContent('Product not found');
        }

        return view('show', [
            'product' => $product
        ]);
    }

    public function create()
    {
        return view('create');
    }

    public function store()
    {
        $data = $this->request->only(['name', 'description', 'price', 'stock', 'category']);

        $id = model(ProductModel::class)->insert($data);

        return response()->json([
            'message' => 'Product created successfully',
            'id' => $id
        ]);
    }

    public function edit($id)
    {
        $product = model(ProductModel::class)->find($id);

        if (!$product) {
            return response()->status(404)->setContent('Product not found');
        }

        return view('edit', [
            'product' => $product
        ]);
    }

    public function update($id)
    {
        $data = $this->request->only(['name', 'description', 'price', 'stock', 'category']);

        model(ProductModel::class)->update($id, $data);

        return response()->json([
            'message' => 'Product updated successfully'
        ]);
    }

    public function destroy($id)
    {
        model(ProductModel::class)->delete($id);

        return response()->json([
            'message' => 'Product deleted successfully'
        ]);
    }

    public function search()
    {
        $keyword = $this->request->query('q', '');
        $products = model(ProductModel::class)->search($keyword);

        return response()->json([
            'products' => $products,
            'total' => count($products)
        ]);
    }

    public function byCategory($category)
    {
        $products = model(ProductModel::class)->findByCategory($category);

        return view('category', [
            'products' => $products,
            'category' => $category
        ]);
    }
}
