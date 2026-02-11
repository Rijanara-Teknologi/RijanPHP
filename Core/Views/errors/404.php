<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-50 text-gray-900 h-screen flex items-center justify-center p-6">
    <div class="max-w-md w-full text-center space-y-8">
        <div class="space-y-4">
            <h1
                class="text-9xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-primary to-blue-600 inline-block">
                404</h1>
            <h2 class="text-3xl font-semibold text-gray-800">Oops! Page not found</h2>
            <p class="text-gray-500 text-lg">The page you are looking for might have been removed, had its name changed,
                or is temporarily unavailable.</p>
        </div>

        <div class="pt-6">
            <a href="/"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-xl hover:bg-indigo-700 transition-all duration-200">
                Go back home
            </a>
        </div>

        <div class="pt-12 text-gray-400 text-sm">
            &copy;
            <?= date('Y') ?> RijanPHP Framework. All rights reserved.
        </div>
    </div>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#4f46e5',
                    }
                }
            }
        }
    </script>
</body>

</html>