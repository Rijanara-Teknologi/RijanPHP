<!DOCTYPE html>
<html>

<head>
    <title>
        <?= yield_section('title', 'RijanPHP App') ?>
    </title>
</head>

<body>
    <header>
        <?= include_view('partials.nav') ?>
    </header>

    <main>
        <?= yield_section('content') ?>
    </main>

    <footer>
        <p>&copy;
            <?= date('Y') ?> RijanPHP Framework
        </p>
    </footer>
</body>

</html>