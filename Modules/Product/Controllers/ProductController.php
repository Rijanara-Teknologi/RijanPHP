<?php
namespace Teguh02\Rijanphp\Modules\Product\Controllers;

use Teguh02\Rijanphp\Core\Controller\Controller;

class ProductController extends Controller
{
    public function index()
    {
        return view('index', [
            'products' => $this->getFakeProducts()
        ]);
    }

    public function show($id)
    {
        $product = $this->findProduct('id', $id);

        if (!$product) {
            return "Product not found";
        }

        return view('detail', [
            'product' => $product
        ]);
    }

    public function showBySlug($slug)
    {
        $product = $this->findProduct('slug', $slug);

        if (!$product) {
            return "Product not found";
        }

        return view('detail', [
            'product' => $product
        ]);
    }

    public function store()
    {
        return $this->json([
            'message' => 'Product created successfully',
            'data' => $this->request->all()
        ], 201);
    }

    public function update($id)
    {
        return $this->json([
            'message' => "Product $id updated successfully",
            'data' => $this->request->all()
        ]);
    }

    public function destroy($id)
    {
        return $this->json([
            'message' => "Product $id deleted successfully"
        ]);
    }

    private function findProduct($key, $value)
    {
        foreach ($this->getFakeProducts() as $product) {
            if ($product[$key] == $value) {
                return $product;
            }
        }
        return null;
    }

    private function getFakeProducts()
    {
        return [
            ['id' => 1, 'slug' => 'laptop-gaming', 'name' => 'Laptop Gaming', 'price' => 15000000],
            ['id' => 2, 'slug' => 'mouse-wireless', 'name' => 'Mouse Wireless', 'price' => 250000],
            ['id' => 3, 'slug' => 'keyboard-mechanical', 'name' => 'Keyboard Mechanical', 'price' => 850000],
        ];
    }
}
