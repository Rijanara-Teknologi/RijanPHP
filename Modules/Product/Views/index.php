<?php extends_layout('master::layouts/main'); ?>

<?php section('content'); ?>
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-800"><?= esc($title ?? 'Products') ?></h1>
        <a href="<?= route('products.create') ?>" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
            Add New Product
        </a>
    </div>

    <?php if (empty($products)): ?>
        <div class="text-center py-12">
            <p class="text-gray-500 text-lg">No products found.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">
                            <?= esc($product->name ?? 'Unknown') ?>
                        </h3>
                        <p class="text-gray-600 text-sm mb-4">
                            <?= esc(substr($product->description ?? '', 0, 100)) ?>...
                        </p>
                        <div class="flex justify-between items-center">
                            <span class="text-green-600 font-bold">
                                <?= esc($product->formatted_price ?? 'Rp 0') ?>
                            </span>
                            <div class="space-x-2">
                                <a href="<?= route('products.show', ['id' => $product->id ?? 0]) ?>" 
                                   class="text-blue-500 hover:text-blue-700">View</a>
                                <a href="<?= route('products.edit', ['id' => $product->id ?? 0]) ?>" 
                                   class="text-yellow-500 hover:text-yellow-700">Edit</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
<?php end_section(); ?>
