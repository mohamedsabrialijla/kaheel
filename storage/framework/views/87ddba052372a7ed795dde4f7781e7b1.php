<?php
    $pkg = strtoupper((string) request('pkg', ''));
    if (!in_array($pkg, ['VIP', 'CLASSIC'], true)) {
        $pkg = '';
    }
    $home = url(app()->getLocale());
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>تسجيل الدخول | FORMA BY AZURE – KAHEEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="<?php echo e(asset('kaheel/home.css')); ?>?v=10" rel="stylesheet">
</head>
<body>
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

    <main class="auth-page">
        <h1 class="auth-title" data-ar="الدخول إلى حسابك" data-en="Access your account">الدخول إلى حسابك</h1>
        <p class="auth-lead" id="auth-lead" data-ar="سجّل الدخول لإكمال طلبك" data-en="Sign in to continue your order">سجّل الدخول لإكمال طلبك</p>

        <?php if($pkg !== ''): ?>
            <div class="auth-package">
                <span class="auth-package-label" data-ar="الباقة المختارة:" data-en="Selected package:">الباقة المختارة:</span>
                <span class="auth-package-code"><?php echo e($pkg); ?></span>
            </div>
        <?php endif; ?>

        <div class="auth-card">
            <p class="auth-alert" id="auth-alert" hidden></p>

            <form class="auth-form" id="password-form">
                <div class="auth-field">
                    <label for="login" data-ar="اسم المستخدم" data-en="Username">اسم المستخدم</label>
                    <input id="login" type="text" maxlength="190" dir="ltr" autocomplete="username">
                </div>
                <div class="auth-field">
                    <label for="password" data-ar="كلمة المرور" data-en="Password">كلمة المرور</label>
                    <input id="password" type="password" dir="ltr" autocomplete="current-password">
                </div>
                <button class="btn btn-lg btn-primary auth-submit" type="submit" id="password-submit" data-ar="دخول" data-en="Log in">دخول</button>
            </form>
        </div>
    </main>

    <script>
        const authConfig = {
            locale: <?php echo json_encode(app()->getLocale(), 15, 512) ?>,
            redirect: <?php echo json_encode((string) request('redirect', ''), 512) ?>,
            pkg: <?php echo json_encode((string) request('pkg', ''), 512) ?>
        };

        const copy = {
            ar: {
                required: "اسم المستخدم وكلمة المرور مطلوبان",
                failed: "تعذر إكمال الطلب",
                login: "دخول",
                title: "تسجيل الدخول | FORMA BY AZURE – KAHEEL"
            },
            en: {
                required: "Username and password are required",
                failed: "The request could not be completed",
                login: "Log in",
                title: "Sign in | FORMA BY AZURE – KAHEEL"
            }
        };

        const root = document.documentElement;
        let lang = "ar";
        const alertBox = document.getElementById("auth-alert");
        const passwordForm = document.getElementById("password-form");

        function text(key) {
            return copy[lang][key];
        }

        function showAlert(message, kind) {
            alertBox.hidden = false;
            alertBox.textContent = message;
            alertBox.className = "auth-alert is-" + kind;
        }

        function clearAlert() {
            alertBox.hidden = true;
            alertBox.textContent = "";
        }

        function applyLang(next) {
            lang = next;
            root.lang = lang;
            root.dir = lang === "ar" ? "rtl" : "ltr";
            document.querySelectorAll("[data-ar]").forEach((node) => {
                if (node.id === "password-submit" || node.classList.contains("auth-title") || node.id === "auth-lead") return;
                node.textContent = lang === "ar" ? node.dataset.ar : node.dataset.en;
            });
            document.getElementById("password-submit").textContent = text("login");
            document.getElementById("auth-lead").textContent = lang === "ar" ? document.getElementById("auth-lead").dataset.ar : document.getElementById("auth-lead").dataset.en;
            document.querySelector(".auth-title").textContent = lang === "ar" ? document.querySelector(".auth-title").dataset.ar : document.querySelector(".auth-title").dataset.en;
            document.getElementById("lang-label").textContent = lang === "ar" ? "EN" : "ع";
            document.title = text("title");
            localStorage.setItem("forma-lang", lang);
        }

        function nextUrl() {
            const redirect = authConfig.redirect;
            let url = "/" + authConfig.locale + "/account";
            if (redirect.startsWith("/") && !redirect.startsWith("//")) {
                url = "/" + authConfig.locale + redirect;
            }
            if (authConfig.pkg) {
                url += (url.includes("?") ? "&" : "?") + "pkg=" + encodeURIComponent(authConfig.pkg);
            }
            return url;
        }

        document.getElementById("lang-toggle").addEventListener("click", () => {
            applyLang(lang === "ar" ? "en" : "ar");
        });

        passwordForm.addEventListener("submit", async (event) => {
            event.preventDefault();
            clearAlert();
            const login = document.getElementById("login").value.trim();
            const password = document.getElementById("password").value;
            if (!login || !password) {
                showAlert(text("required"), "error");
                return;
            }
            const response = await fetch("/" + authConfig.locale + "/auth/session", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: JSON.stringify({ login, password })
            });
            const raw = await response.text();
            let data;
            try {
                data = JSON.parse(raw);
            } catch (error) {
                data = { status: false, message: text("failed") };
            }
            if (!data || data.status !== true || !data.items || !data.items.token) {
                showAlert((data && data.message) || text("failed"), "error");
                return;
            }
            localStorage.setItem("authToken", data.items.token);
            localStorage.setItem("token", data.items.token);
            localStorage.setItem("user", JSON.stringify(data.items));
            location.assign(nextUrl());
        });

        applyLang(localStorage.getItem("forma-lang") === "en" ? "en" : "ar");
    </script>
</body>
</html>
<?php /**PATH E:\kaheel\resources\views/kaheel/auth.blade.php ENDPATH**/ ?>