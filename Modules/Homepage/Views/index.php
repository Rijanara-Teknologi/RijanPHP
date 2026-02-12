<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RijanPHP | Modern, Modular PHP Framework</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2596be',
                        dark: '#0a0f1a',
                        surface: '#111827',
                        border: 'rgba(255, 255, 255, 0.1)',
                    },
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background-color: #0a0f1a;
            color: #f8fafc;
            overflow-x: hidden;
        }

        .hero-glow {
            position: absolute;
            top: -10%;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            height: 600px;
            background: radial-gradient(circle at 50% 0%, rgba(37, 150, 190, 0.15) 0%, transparent 70%);
            z-index: -1;
        }

        .glass-card {
            background: rgba(17, 24, 39, 0.7);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            border-color: rgba(37, 150, 190, 0.4);
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.5);
        }

        .feature-icon-box {
            background: linear-gradient(135deg, rgba(37, 150, 190, 0.1) 0%, rgba(52, 211, 153, 0.05) 100%);
            border: 1px solid rgba(37, 150, 190, 0.2);
        }
    </style>
</head>

<body class="antialiased">
    <div class="hero-glow"></div>

    <!-- Navigation -->
    <nav class="max-w-7xl mx-auto px-6 py-8 flex items-center justify-between">
        <div class="flex items-center space-x-3 group cursor-pointer">
            <div
                class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-[0_0_20px_rgba(37,150,190,0.3)] transition-transform group-hover:scale-110">
                <i class="fas fa-bolt text-white text-xl"></i>
            </div>
            <span class="text-2xl font-bold tracking-tight">Rijan<span class="text-primary">PHP</span></span>
        </div>
        <div class="hidden md:flex items-center space-x-8 text-sm font-medium text-gray-400">
            <a href="#" class="hover:text-white transition-colors">Documentation</a>
            <a href="#" class="hover:text-white transition-colors">Modules</a>
            <a href="https://github.com/Rijanara-Teknologi/RijanPHP" target="_blank"
                class="hover:text-white transition-colors">GitHub</a>
        </div>
        <div>
            <a href="https://github.com/Rijanara-Teknologi/RijanPHP" target="_blank"
                class="bg-white/5 hover:bg-white/10 border border-white/10 px-5 py-2.5 rounded-full text-sm font-semibold transition-all">
                v<?= \Teguh02\Rijanphp\Core\Rijan::version() ?>
            </a>
        </div>
    </nav>

    <!-- Hero Section -->
    <main class="max-w-7xl mx-auto px-6 py-12 md:py-20 text-center relative">
        <div
            class="inline-flex items-center space-x-2 bg-primary/10 border border-primary/20 px-4 py-1.5 rounded-full text-primary text-xs font-bold mb-8 animate-bounce">
            <span class="relative flex h-2 w-2">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
            </span>
            <span>RijanPHP v<?= \Teguh02\Rijanphp\Core\Rijan::VERSION ?> is now stable!</span>
        </div>

        <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight mb-8 leading-[1.1]">
            Build <span
                class="text-transparent bg-clip-text bg-gradient-to-r from-primary to-emerald-400">Modular</span>
            Applications<br>
            With Elegant Simplicity
        </h1>

        <p class="max-w-2xl mx-auto text-lg md:text-xl text-gray-400 leading-relaxed mb-10">
            A modern PHP framework designed for developers who value performance,
            zero-dependency philosophy, and clean architectural patterns.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-20">
            <a href="#"
                class="w-full sm:w-auto bg-primary hover:bg-primary/90 text-white font-bold py-4 px-10 rounded-2xl shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2">
                Get Started
                <i class="fas fa-arrow-right text-sm"></i>
            </a>
            <a href="https://github.com/Rijanara-Teknologi/RijanPHP" target="_blank"
                class="w-full sm:w-auto bg-white/5 hover:bg-white/10 border border-white/10 text-white font-bold py-4 px-10 rounded-2xl transition-all flex items-center justify-center gap-2">
                <i class="fab fa-github"></i>
                Star on GitHub
            </a>
        </div>

        <!-- Dashboard Preview / Welcome Card -->
        <div class="max-w-5xl mx-auto glass-card rounded-3xl p-8 md:p-12 relative overflow-hidden text-left mb-20">
            <div
                class="absolute top-0 right-0 w-64 h-64 bg-primary/10 blur-[80px] -z-10 translate-x-1/2 -translate-y-1/2">
            </div>

            <div class="grid md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-bold mb-6">Lightweight & Fast</h2>
                    <p class="text-gray-400 mb-6 leading-relaxed">
                        RijanPHP is built from the ground up to be incredibly fast. By avoiding heavy dependencies and
                        bloated libraries, your application stays lean and responds in milliseconds.
                    </p>
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-sm text-gray-300">
                            <div
                                class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-emerald-400 text-[10px]"></i>
                            </div>
                            Zero external core dependencies
                        </li>
                        <li class="flex items-center gap-3 text-sm text-gray-300">
                            <div
                                class="w-6 h-6 rounded-full bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-check text-emerald-400 text-[10px]"></i>
                            </div>
                            Native PHP View Engine
                        </li>
                    </ul>
                </div>
                <div class="relative">
                    <div
                        class="bg-surface/50 border border-white/5 rounded-2xl p-4 font-mono text-xs text-left shadow-2xl">
                        <div class="flex space-x-1.5 mb-4">
                            <div class="w-3 h-3 rounded-full bg-red-500/20"></div>
                            <div class="w-3 h-3 rounded-full bg-yellow-500/20"></div>
                            <div class="w-3 h-3 rounded-full bg-emerald-500/20"></div>
                        </div>
                        <div class="text-primary-400">Router<span class="text-gray-500">::</span>get<span
                                class="text-gray-300">(</span><span class="text-emerald-400">'/'</span><span
                                class="text-gray-300">, function() {</span></div>
                        <div class="pl-4 text-emerald-400">return <span class="text-gray-300">view(</span>'welcome'<span
                                class="text-gray-300">);</span></div>
                        <div class="text-gray-300">}<span class="text-gray-300">);</span></div>
                    </div>
                    <div class="absolute -bottom-6 -right-6 bg-primary/20 w-32 h-32 blur-[40px] -z-10"></div>
                </div>
            </div>
        </div>

        <!-- Features Grid -->
        <section class="max-w-6xl mx-auto py-12">
            <h3 class="text-sm font-bold text-primary tracking-[0.2em] uppercase mb-12">Core Foundations</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-left">
                <!-- Feature 1 -->
                <div class="glass-card rounded-2xl p-8">
                    <div class="w-12 h-12 rounded-xl feature-icon-box flex items-center justify-center mb-6">
                        <i class="fas fa-boxes text-primary"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3">Modular by Design</h4>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Encapsulate your logic into self-contained modules. Each module has its own routes, controllers,
                        and views.
                    </p>
                </div>
                <!-- Feature 2 -->
                <div class="glass-card rounded-2xl p-8">
                    <div class="w-12 h-12 rounded-xl feature-icon-box flex items-center justify-center mb-6">
                        <i class="fas fa-shield-halved text-primary"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3">Security Built-in</h4>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Native CSRF protection, encrypted cookies, and secure session handling keep your data safe by
                        default.
                    </p>
                </div>
                <!-- Feature 3 -->
                <div class="glass-card rounded-2xl p-8">
                    <div class="w-12 h-12 rounded-xl feature-icon-box flex items-center justify-center mb-6">
                        <i class="fas fa-database text-primary"></i>
                    </div>
                    <h4 class="text-xl font-bold mb-3">Fluent ORM</h4>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        A powerful yet simple query builder that supports MySQL, PostgreSQL, and SQLite out of the box.
                    </p>
                </div>
            </div>
        </section>

        <!-- CTA Footer -->
        <footer class="mt-32 pt-16 border-t border-white/5 pb-16">
            <div class="grid md:grid-cols-4 gap-12 text-left text-sm">
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center space-x-2 mb-6">
                        <div class="w-6 h-6 bg-primary rounded-md flex items-center justify-center">
                            <i class="fas fa-bolt text-white text-[10px]"></i>
                        </div>
                        <span class="text-lg font-bold">RijanPHP</span>
                    </div>
                    <p class="text-gray-500 max-w-sm mb-6">
                        RijanPHP is a modern PHP framework for crafting high-quality applications without the complexity
                        of modern legacy frameworks.
                    </p>
                </div>
                <div>
                    <h5 class="font-bold mb-6 text-white uppercase tracking-wider text-xs">Resources</h5>
                    <ul class="space-y-4 text-gray-500">
                        <li><a href="#" class="hover:text-primary transition-colors">Documentation</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Starter Guide</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">API Reference</a></li>
                    </ul>
                </div>
                <div>
                    <h5 class="font-bold mb-6 text-white uppercase tracking-wider text-xs">Community</h5>
                    <ul class="space-y-4 text-gray-500">
                        <li><a href="https://github.com/Rijanara-Teknologi/RijanPHP"
                                class="hover:text-primary transition-colors">GitHub Repository</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Contributing</a></li>
                        <li><a href="#" class="hover:text-primary transition-colors">Discord Server</a></li>
                    </ul>
                </div>
            </div>
            <div
                class="mt-16 pt-8 border-t border-white/5 flex flex-col md:flex-row items-center justify-between text-gray-500 text-xs gap-4">
                <p>© 2026 PT Rijanara Inovasi Teknologi. Built for simplicity.</p>
                <div class="flex items-center space-x-6">
                    <a href="https://github.com/teguh02" class="hover:text-white"><i class="fab fa-github"></i></a>
                    <a href="#" class="hover:text-white"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="hover:text-white"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
        </footer>
    </main>
</body>

</html>