<?php extends_layout('master::layouts/main'); ?>

<?php section('content'); ?>
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold mb-4"><?= esc($title ?? 'Blog') ?></h1>
    <p>Welcome to the Blog module.</p>
</div>
<?php end_section(); ?>