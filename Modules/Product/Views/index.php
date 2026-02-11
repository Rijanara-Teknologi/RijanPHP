<?php extends_layout('layouts.app') ?>

<?php section('title') ?>Product List<?php end_section() ?>

<?php section('content') ?>
<h1>Our Products</h1>
<ul>
    <?php foreach ($products as $product): ?>
        <li>
            <a href="/product/id/<?= $product['id'] ?>"><?= $product['name'] ?></a>
            ( <a href="/product/slug/<?= $product['slug'] ?>">slug</a> )
            - IDR <?= number_format($product['price'], 0, ',', '.') ?>
        </li>
    <?php endforeach; ?>
</ul>
<?php end_section() ?>