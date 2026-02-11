<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You | RijanPHP Framework</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2596be',
                        secondary: '#1a6a8a',
                        accent: '#34d399',
                        dark: '#0f172a',
                        light: '#f8fafc'
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'fade-in': 'fadeIn 0.8s ease-out forwards'
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0px)' },
                            '50%': { transform: 'translateY(-20px)' }
                        },
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(20px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            min-height: 100vh;
            overflow-x: hidden;
        }
        
        .gradient-bg {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle at 20% 30%, rgba(37, 150, 190, 0.15) 0%, transparent 50%),
                        radial-gradient(circle at 80% 70%, rgba(52, 211, 153, 0.1) 0%, transparent 50%),
                        radial-gradient(circle at 50% 50%, rgba(255, 255, 255, 0.05) 0%, transparent 70%);
            z-index: -1;
            pointer-events: none;
        }
        
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
        }
        
        .particle {
            position: absolute;
            background: rgba(37, 150, 190, 0.3);
            border-radius: 50%;
            animation: float 15s infinite linear;
        }
        
        .glow-effect {
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            z-index: -1;
        }
        
        .glow-1 {
            background: linear-gradient(135deg, #2596be, #34d399);
            top: -100px;
            right: -100px;
        }
        
        .glow-2 {
            background: linear-gradient(135deg, #34d399, #2596be);
            bottom: -100px;
            left: -100px;
        }
        
        .card-hover {
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-hover:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        
        .feature-icon {
            transition: all 0.3s ease;
        }
        
        .feature-card:hover .feature-icon {
            transform: scale(1.2) rotate(10deg);
            filter: drop-shadow(0 0 15px rgba(37, 150, 190, 0.5));
        }
        
        .stagger-item {
            opacity: 0;
            transform: translateY(30px);
        }
        
        .stagger-item.visible {
            animation: fadeIn 0.8s ease-out forwards;
        }
    </style>
</head>
<body class="font-sans text-gray-100">
    <!-- Background Effects -->
    <div class="gradient-bg"></div>
    <div class="glow-effect glow-1"></div>
    <div class="glow-effect glow-2"></div>
    
    <!-- Particles -->
    <div class="particles" id="particles"></div>
    
    <div class="container mx-auto px-4 py-4 sm:py-6">
        <!-- Header Section -->
        <div class="text-center mb-4 stagger-item" style="animation-delay: 0.1s">
            <div class="inline-block mb-2">
                <div class="relative">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-primary to-accent flex items-center justify-center mx-auto">
                        <i class="fas fa-code text-white text-3xl"></i>
                    </div>
                    <div class="absolute inset-0 rounded-full bg-primary animate-ping opacity-20"></div>
                </div>
            </div>
            <h1 class="text-3xl md:text-5xl font-bold mb-2 bg-clip-text text-transparent bg-gradient-to-r from-primary to-accent">
                Thank You!
            </h1>
            <p class="text-lg md:text-xl text-gray-300 mb-1">
                For Choosing
            </p>
            <h2 class="text-2xl md:text-4xl font-bold mb-2 bg-clip-text text-transparent bg-gradient-to-r from-accent to-primary">
                RijanPHP Framework
            </h2>
            <p class="text-base md:text-lg text-gray-400 max-w-xl mx-auto">
                A simple yet powerful modern PHP framework for building elegant web applications
            </p>
        </div>
        
        <!-- Main Card - Reduced top/bottom margins -->
        <div class="max-w-4xl mx-auto mb-4">
            <div class="bg-white/10 backdrop-blur-lg rounded-xl border border-white/20 overflow-hidden card-hover">
                <div class="bg-gradient-to-r from-primary to-accent px-6 py-3">
                    <div class="flex items-center justify-center">
                        <div class="flex items-center space-x-2">
                            <i class="fas fa-rocket text-white text-xl"></i>
                            <span class="text-white font-semibold">Your Development Journey Starts Here</span>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <div class="mb-5">
                        <h3 class="text-xl font-bold text-white mb-2">Welcome to the RijanPHP Ecosystem!</h3>
                        <p class="text-gray-200 text-base leading-relaxed mb-2">
                            You've made an excellent choice. RijanPHP is a modern PHP framework designed for developers who value simplicity without sacrificing power, flexibility, and performance.
                        </p>
                        <p class="text-gray-300 text-base leading-relaxed">
                            Built with a modular architecture and PSR-4 compliance, RijanPHP gives you the freedom to structure your applications exactly how you need while maintaining clean, maintainable code.
                        </p>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div class="bg-white/5 backdrop-blur-sm rounded-lg p-4 border border-white/10">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-accent flex items-center justify-center">
                                        <i class="fas fa-bolt text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-white font-semibold mb-1">High Performance</h4>
                                    <p class="text-gray-300 text-sm">Optimized for speed and resource efficiency</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white/5 backdrop-blur-sm rounded-lg p-4 border border-white/10">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-accent to-primary flex items-center justify-center">
                                        <i class="fas fa-layer-group text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-white font-semibold mb-1">Modular Architecture</h4>
                                    <p class="text-gray-300 text-sm">Flexible structure with isolated modules for each feature</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white/5 backdrop-blur-sm rounded-lg p-4 border border-white/10">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-primary to-accent flex items-center justify-center">
                                        <i class="fas fa-shield-alt text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-white font-semibold mb-1">Security First</h4>
                                    <p class="text-gray-300 text-sm">Built-in security best practices and protection</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-white/5 backdrop-blur-sm rounded-lg p-4 border border-white/10">
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-accent to-primary flex items-center justify-center">
                                        <i class="fas fa-plug text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <h4 class="text-white font-semibold mb-1">Seamless Integration</h4>
                                    <p class="text-gray-300 text-sm">Full Composer compatibility with smooth autoloader transitions</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center py-4 bg-gradient-to-r from-primary/10 to-accent/10 rounded-lg border border-white/10">
                        <div class="inline-flex items-center space-x-2">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-accent flex items-center justify-center">
                                <i class="fas fa-star text-white"></i>
                            </div>
                            <p class="text-gray-200 text-sm italic">
                                "Simplicity is the ultimate sophistication in development"
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Features Grid - Reduced bottom margin -->
        <div class="max-w-6xl mx-auto mb-4">
            <div class="text-center mb-4 stagger-item" style="animation-delay: 0.2s">
                <h3 class="text-2xl font-bold mb-2">Core Features</h3>
                <p class="text-gray-400 text-sm">A framework built for modern developers</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="feature-card bg-white/5 backdrop-blur-sm rounded-lg p-4 border border-white/10 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-xl bg-gradient-to-br from-primary to-accent flex items-center justify-center mb-3 mx-auto">
                        <i class="fas fa-magic text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-center mb-2">Smart Autoloader</h4>
                    <p class="text-gray-300 text-center text-sm">
                        PSR-4 compliant autoloader that reads composer.json for seamless Composer integration
                    </p>
                </div>
                
                <div class="feature-card bg-white/5 backdrop-blur-sm rounded-lg p-4 border border-white/10 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-xl bg-gradient-to-br from-accent to-primary flex items-center justify-center mb-3 mx-auto">
                        <i class="fas fa-cubes text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-center mb-2">True Modularity</h4>
                    <p class="text-gray-300 text-center text-sm">
                        Organize your application with self-contained modules featuring their own MVC structure
                    </p>
                </div>
                
                <div class="feature-card bg-white/5 backdrop-blur-sm rounded-lg p-4 border border-white/10 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-xl bg-gradient-to-br from-primary to-accent flex items-center justify-center mb-3 mx-auto">
                        <i class="fas fa-exchange-alt text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-center mb-2">Composer Compatible</h4>
                    <p class="text-gray-300 text-center text-sm">
                        Switch between custom and Composer autoloader without any code changes
                    </p>
                </div>
                
                <div class="feature-card bg-white/5 backdrop-blur-sm rounded-lg p-4 border border-white/10 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-xl bg-gradient-to-br from-accent to-primary flex items-center justify-center mb-3 mx-auto">
                        <i class="fas fa-sliders-h text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-center mb-2">Flexible Configuration</h4>
                    <p class="text-gray-300 text-center text-sm">
                        Intuitive configuration system that adapts to your project requirements
                    </p>
                </div>
                
                <div class="feature-card bg-white/5 backdrop-blur-sm rounded-lg p-4 border border-white/10 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-xl bg-gradient-to-br from-primary to-accent flex items-center justify-center mb-3 mx-auto">
                        <i class="fas fa-rocket text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-center mb-2">Production Ready</h4>
                    <p class="text-gray-300 text-center text-sm">
                        Built-in error handling, logging, and optimization for deployment-ready applications
                    </p>
                </div>
                
                <div class="feature-card bg-white/5 backdrop-blur-sm rounded-lg p-4 border border-white/10 card-hover">
                    <div class="feature-icon w-12 h-12 rounded-xl bg-gradient-to-br from-accent to-primary flex items-center justify-center mb-3 mx-auto">
                        <i class="fas fa-book-open text-white text-xl"></i>
                    </div>
                    <h4 class="text-lg font-bold text-center mb-2">Comprehensive Docs</h4>
                    <p class="text-gray-300 text-center text-sm">
                        Clear documentation with practical examples to accelerate your development workflow
                    </p>
                </div>
            </div>
        </div>
        
        <!-- CTA Section -->
        <div class="max-w-4xl mx-auto mb-4 stagger-item" style="animation-delay: 0.3s">
            <div class="bg-gradient-to-br from-primary to-accent rounded-xl p-6 text-center">
                <h3 class="text-xl font-bold text-white mb-2">
                    Ready to Build Your Next Application?
                </h3>
                <p class="text-white/90 text-base mb-4 max-w-xl mx-auto">
                    Start your new project with RijanPHP and experience the joy of elegant, efficient development
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-3">
                    <a href="https://github.com/teguh02/rijanphp" target="_blank" class="bg-white text-primary font-bold py-2 px-6 rounded-lg hover:bg-gray-100 transition-colors flex items-center justify-center shadow-lg">
                        <i class="fab fa-github mr-2"></i>
                        GitHub Repository
                    </a>
                    <a href="#" class="bg-transparent border-2 border-white text-white font-bold py-2 px-6 rounded-lg hover:bg-white/10 transition-colors flex items-center justify-center">
                        <i class="fas fa-book mr-2"></i>
                        Documentation
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <footer class="text-center text-gray-500 text-xs stagger-item" style="animation-delay: 0.4s">
            <div class="mb-1">
                <p class="flex items-center justify-center space-x-1">
                    <span>Crafted with</span>
                    <i class="fas fa-heart text-red-500 text-xs"></i>
                    <span>by</span>
                    <span class="font-semibold text-primary">Teguh Rijanandi</span>
                </p>
            </div>
            <div class="flex items-center justify-center space-x-4 mb-2">
                <a href="https://github.com/teguh02" target="_blank" class="text-gray-400 hover:text-white transition-colors">
                    <i class="fab fa-github text-xl"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors">
                    <i class="fab fa-twitter text-xl"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors">
                    <i class="fab fa-linkedin-in text-xl"></i>
                </a>
                <a href="#" class="text-gray-400 hover:text-white transition-colors">
                    <i class="fas fa-globe text-xl"></i>
                </a>
            </div>
            <p>© 2026 RijanPHP Framework. All rights reserved.</p>
            <p class="text-[10px] mt-1">Simple but Modern PHP Framework</p>
        </footer>
    </div>
    
    <script>
        // Create floating particles
        function createParticles() {
            const particlesContainer = document.getElementById('particles');
            const particleCount = 12;
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Random size
                const size = Math.random() * 35 + 8;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                
                // Random position
                particle.style.left = `${Math.random() * 100}%`;
                particle.style.top = `${Math.random() * 100}%`;
                
                // Random animation delay and duration
                particle.style.animationDelay = `${Math.random() * 8}s`;
                particle.style.animationDuration = `${Math.random() * 15 + 8}s`;
                
                // Random opacity
                particle.style.opacity = `${Math.random() * 0.25 + 0.05}`;
                
                particlesContainer.appendChild(particle);
            }
        }
        
        // Stagger animation on scroll
        function initStaggerAnimations() {
            const staggerItems = document.querySelectorAll('.stagger-item');
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1
            });
            
            staggerItems.forEach((item, index) => {
                item.style.animationDelay = `${index * 0.05}s`;
                observer.observe(item);
            });
        }
        
        // Initialize everything when page loads
        document.addEventListener('DOMContentLoaded', () => {
            createParticles();
            initStaggerAnimations();
            
            // Add subtle animation to feature cards on hover
            const featureCards = document.querySelectorAll('.feature-card');
            featureCards.forEach(card => {
                const icon = card.querySelector('.feature-icon');
                card.addEventListener('mouseenter', () => {
                    icon.style.transform = 'scale(1.2) rotate(10deg)';
                });
                card.addEventListener('mouseleave', () => {
                    icon.style.transform = 'scale(1) rotate(0deg)';
                });
            });
        });
    </script>
</body>
</html>