<?php
    $sessionUser = auth('admin')->user();
    $customer = $sessionUser ? null : kaheel_customer();
    $sessionLabel = $sessionUser
        ? (trim((string) $sessionUser->name) !== '' ? $sessionUser->name : $sessionUser->email)
        : ($customer ? ($customer->name ?: $customer->username) : null);
    $authUrl = url(app()->getLocale().'/auth');
?>
<?php if($sessionLabel): ?>
    <div class="session-chip">
        <span class="session-who">
            <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
            <span class="session-email"><?php echo e($sessionLabel); ?></span>
        </span>
        <form class="session-out" method="POST" action="<?php echo e(url(app()->getLocale().'/auth/logout')); ?>" onsubmit="localStorage.removeItem('authToken');localStorage.removeItem('token');localStorage.removeItem('user');">
            <?php echo csrf_field(); ?>
            <button class="btn btn-sm btn-outline" type="submit">
                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="m16 17 5-5-5-5"/>
                    <path d="M21 12H9"/>
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                </svg>
                <span class="session-out-label" data-t="signOut" data-ar="تسجيل الخروج" data-en="Sign out">تسجيل الخروج</span>
            </button>
        </form>
    </div>
<?php else: ?>
    <a class="btn btn-sm btn-primary" href="<?php echo e($authUrl); ?>">
        <svg class="icon login-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
            <circle cx="12" cy="7" r="4"/>
        </svg>
        <span class="login-label" data-t="signIn" data-ar="تسجيل الدخول" data-en="Sign in">تسجيل الدخول</span>
    </a>
<?php endif; ?>
<?php /**PATH E:\kaheel\resources\views/kaheel/partials/account-button.blade.php ENDPATH**/ ?>