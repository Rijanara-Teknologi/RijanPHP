<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Server Error</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link
        href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&family=JetBrains+Mono:wght@400;500&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }

        .mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .glass {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-900 min-h-screen p-6 md:p-12">
    <div class="max-w-5xl mx-auto">
        <!-- Error Header -->
        <div class="mb-12 text-center md:text-left">
            <h1 class="text-5xl font-bold text-red-600 mb-2">500 Server Error</h1>
            <p class="text-gray-600 text-xl font-light">Something went wrong on our end.</p>
        </div>

        <?php if ($debug && isset($exception)): ?>
            <!-- Debug Info -->
            <div class="space-y-6 animate-in fade-in slide-in-from-bottom-4 duration-700">
                <!-- Main Message -->
                <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-red-100">
                    <div class="bg-red-50 px-6 py-4 border-b border-red-100 flex items-center gap-3">
                        <div class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></div>
                        <span class="text-red-700 font-semibold uppercase tracking-wider text-xs">Exception:
                            <?= get_class($exception) ?>
                        </span>
                    </div>
                    <div class="p-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">
                            <?= htmlspecialchars($exception->getMessage()) ?>
                        </h2>
                        <div class="flex flex-wrap gap-4 text-sm">
                            <div class="bg-gray-100 px-3 py-1.5 rounded-lg text-gray-600 flex items-center gap-2">
                                <span class="font-bold">File:</span>
                                <span class="mono">
                                    <?= $exception->getFile() ?>
                                </span>
                            </div>
                            <div class="bg-gray-100 px-3 py-1.5 rounded-lg text-gray-600 flex items-center gap-2">
                                <span class="font-bold">Line:</span>
                                <span class="mono">
                                    <?= $exception->getLine() ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stack Trace -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 overflow-hidden">
                    <div
                        class="bg-gray-50 px-6 py-3 border-b border-gray-200 font-semibold text-gray-700 flex justify-between items-center">
                        <span>Stack Trace</span>
                        <button id="copyError"
                            class="bg-gray-800 text-white px-4 py-1.5 rounded-lg text-xs font-semibold hover:bg-black transition-all duration-200 flex items-center gap-2 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3" />
                            </svg>
                            <span>Copy Error</span>
                        </button>
                    </div>
                    <div class="p-0">
                        <pre id="stackTrace"
                            class="mono text-xs text-gray-600 overflow-x-auto p-6 bg-gray-900 text-white leading-relaxed"><?= htmlspecialchars($exception->getTraceAsString()) ?></pre>
                    </div>
                </div>

                <script>
                    document.getElementById('copyError').addEventListener('click', function () {
                        const icon = this.querySelector('svg');
                        const text = this.querySelector('span');
                        const errorDetails = `Exception: <?= get_class($exception) ?>\nMessage: <?= addslashes($exception->getMessage()) ?>\nFile: <?= addslashes($exception->getFile()) ?>\nLine: <?= $exception->getLine() ?>\n\nStack Trace:\n<?= addslashes($exception->getTraceAsString()) ?>`;

                        navigator.clipboard.writeText(errorDetails).then(() => {
                            const originalText = text.innerText;
                            text.innerText = 'Copied!';
                            this.classList.remove('bg-gray-800');
                            this.classList.add('bg-green-600');

                            setTimeout(() => {
                                text.innerText = originalText;
                                this.classList.remove('bg-green-600');
                                this.classList.add('bg-gray-800');
                            }, 2000);
                        });
                    });
                </script>
            </div>
        <?php else: ?>
            <!-- Production View -->
            <div class="bg-white rounded-3xl shadow-xl p-12 text-center max-w-2xl mx-auto mt-20">
                <div class="w-20 h-20 bg-red-100 rounded-2xl flex items-center justify-center mx-auto mb-8">
                    <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Internal Server Error</h2>
                <p class="text-gray-500 text-lg mb-8 italic">"Not all who wander are lost, but sometimes the server is."</p>
                <div class="text-gray-600 text-sm leading-relaxed mb-10">
                    We've logged this error and are working on fixing it. If you continue to see this, please contact
                    support.
                </div>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="/"
                        class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-xl font-bold hover:shadow-lg hover:-translate-y-1 transition-all duration-200">
                        Take me back Home
                    </a>
                </div>
            </div>
        <?php endif; ?>

        <div class="mt-12 text-center text-gray-400 text-sm">
            Powered by RijanPHP v
            <?= \Teguh02\Rijanphp\Core\Rijan::version() ?>
        </div>
    </div>
</body>

</html>