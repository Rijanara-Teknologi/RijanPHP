<?php extends_layout('layouts.app') ?>

<?php section('title') ?>Product Detail - <?= $product['name'] ?><?php end_section() ?>

<?php section('content') ?>
<h1>Product Details</h1>
<div>
    <strong>Name:</strong> <?= $product['name'] ?>
</div>
<div>
    <strong>Price:</strong> IDR <?= number_format($product['price'], 0, ',', '.') ?>
</div>
<p>
    <a href="/products">Back to List</a>
</p>
<?php end_section() ?>