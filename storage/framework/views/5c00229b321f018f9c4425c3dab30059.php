<header class="site-header">
    <div class="header-inner">
        <a class="brand" href="<?php echo e($home); ?>" aria-label="FORMA BY AZURE — KAHEEL">
            <span class="brand-mark">
                <span class="brand-forma">FORMA</span>
                <span class="brand-azure">BY AZURE</span>
            </span>
            <span class="brand-name">KAHEEL</span>
        </a>
        <nav class="nav" aria-label="main">
            <a class="btn btn-sm nav-home" href="<?php echo e($home); ?>" data-ar="الرئيسية" data-en="Home">الرئيسية</a>
            <?php echo $__env->make('kaheel.partials.role-links', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php echo $__env->make('kaheel.partials.notice-bell', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <button class="btn btn-sm" type="button" id="lang-toggle" aria-label="language">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m5 8 6 6"/>
                    <path d="m4 14 6-6 2-3"/>
                    <path d="M2 5h12"/>
                    <path d="M7 2h1"/>
                    <path d="m22 22-5-10-5 10"/>
                    <path d="M14 18h6"/>
                </svg>
                <span id="lang-label">EN</span>
            </button>
            <?php echo $__env->make('kaheel.partials.account-button', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </nav>
    </div>
</header>
<?php echo $__env->make('kaheel.partials.native-validation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH E:\kaheel\resources\views/kaheel/partials/public-header.blade.php ENDPATH**/ ?>