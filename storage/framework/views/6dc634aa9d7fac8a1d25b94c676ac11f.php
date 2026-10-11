<?php
    $isAdmin = auth('admin')->check();
    $customer = kaheel_customer();
    $isUser = auth()->check() || $customer;
    $ordersUrl = $customer
        ? url(app()->getLocale().'/orders')
        : ($isAdmin ? url(app()->getLocale().'/dashboard') : url(app()->getLocale().'/auth'));
    $adminUrl = url(app()->getLocale().'/dashboard');
?>
<?php if($isUser || $isAdmin): ?>
    <a class="btn btn-sm nav-role" href="<?php echo e($ordersUrl); ?>">
        <span data-t="navOrders" data-ar="طلباتي" data-en="My Orders">طلباتي</span>
    </a>
<?php endif; ?>
<?php if($isAdmin): ?>
    <a class="btn btn-sm nav-role" href="<?php echo e($adminUrl); ?>">
        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <rect width="7" height="7" x="3" y="3" rx="1"/>
            <rect width="7" height="7" x="14" y="3" rx="1"/>
            <rect width="7" height="7" x="14" y="14" rx="1"/>
            <rect width="7" height="7" x="3" y="14" rx="1"/>
        </svg>
        <span data-t="navAdmin" data-ar="الإدارة" data-en="Admin">الإدارة</span>
    </a>
<?php endif; ?>
<?php /**PATH E:\kaheel\resources\views/kaheel/partials/role-links.blade.php ENDPATH**/ ?>