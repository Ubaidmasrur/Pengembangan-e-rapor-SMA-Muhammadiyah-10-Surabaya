<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo e(config('app.name', 'Laravel')); ?></title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles & Scripts -->
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>

<body class="flex flex-col min-h-screen antialiased">
    
    <div class="flex-grow bg-gray-100">
        <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php if(isset($header)): ?>
            <header class="bg-white shadow" role="banner">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <?php echo e($header); ?>

                </div>
            </header>
        <?php endif; ?>

        <main class="pb-24" role="main" tabindex="-1">
            
            <?php echo e($slot ?? $__env->yieldContent('content')); ?>

        </main>
    </div>

    <?php echo $__env->make('layouts.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>

    <!-- Back to Top FAB -->
    <button id="backToTopBtn"
        type="button"
        aria-label="Kembali ke atas"
        class="fixed bottom-6 right-6 z-50 bg-blue-600 text-white rounded-full shadow-lg p-4 transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400"
        style="display: none;">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
            <title>Kembali ke atas</title>
            <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7"/>
        </svg>
    </button>
    <script>
        // Show/hide FAB on scroll
        const backToTopBtn = document.getElementById('backToTopBtn');
        window.addEventListener('scroll', function () {
            if (window.scrollY > 200) {
                backToTopBtn.style.display = 'block';
            } else {
                backToTopBtn.style.display = 'none';
            }
        });
        backToTopBtn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    </script>
</body>
</html>
<?php /**PATH D:\Priv\Project\erapor\e-rapor-sederhana\resources\views/layouts/app.blade.php ENDPATH**/ ?>