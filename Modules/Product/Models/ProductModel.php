<?php

namespace Teguh02\Rijanphp\Modules\Product\Models;

use Teguh02\Rijanphp\Core\Model\Model;

class ProductModel extends Model
{
    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $returnType = 'object';
    protected $allowedFields = ['name', 'description', 'price', 'stock', 'category', 'image'];
    protected $useTimestamps = true;
    protected $useSoftDeletes = true;

    protected $beforeInsert = ['generateSlug'];
    protected $afterFind = ['formatPrice'];

    protected function generateSlug($data)
    {
        if (isset($data['data']['name'])) {
            $data['data']['slug'] = strtolower(str_replace(' ', '-', $data['data']['name']));
        }
        return $data;
    }

    protected function formatPrice($data)
    {
        if ($data && isset($data['price'])) {
            $data['formatted_price'] = 'Rp ' . number_format($data['price'], 0, ',', '.');
        }
        return $data;
    }

    public function findByCategory($category)
    {
        return $this->builder
            ->where('category', $category)
            ->where('deleted_at', null)
            ->get();
    }

    public function search($keyword)
    {
        return $this->builder
            ->where('name', 'LIKE', '%' . $keyword . '%')
            ->orWhere('description', 'LIKE', '%' . $keyword . '%')
            ->get();
    }
}
