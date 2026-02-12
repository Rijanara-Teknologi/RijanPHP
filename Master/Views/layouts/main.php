<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= yield_section('title', 'RijanPHP') ?></title>
    <!-- Master CSS -->
    <style>
        body {
            font-family: sans-serif;
            background: #f0f0f0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        header {
            background: #333;
            color: white;
            padding: 1rem;
        }

        footer {
            background: #333;
            color: white;
            padding: 1rem;
            margin-top: auto;
        }

        .container {
            display: flex;
            flex: 1;
        }

        aside {
            width: 200px;
            background: #e0e0e0;
            padding: 1rem;
        }

        main {
            flex: 1;
            padding: 1rem;
        }
    </style>
</head>

<body>
    <?= include_view('master::partials.header') ?>

    <div class="container">
        <?= include_view('master::partials.sidebar') ?>

        <main>
            <?= yield_section('content') ?>
        </main>
    </div>

    <?= include_view('master::partials.footer') ?>
</body>

</html>