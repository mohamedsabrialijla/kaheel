<?php
    $locale = app()->getLocale();
    $home = url($locale);
?>
<!DOCTYPE html>
<html lang="<?php echo e($locale); ?>" dir="<?php echo e($locale === 'ar' ? 'rtl' : 'ltr'); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>دخول الإدارة | FORMA BY AZURE – KAHEEL</title>
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
            </nav>
        </div>
    </header>

    <main class="auth-page">
        <a class="admin-logo" href="<?php echo e($home); ?>" aria-label="FORMA BY AZURE — KAHEEL">
            <span class="brand-mark">
                <span class="brand-forma">FORMA</span>
                <span class="brand-azure">BY AZURE</span>
            </span>
            <span class="brand-name">KAHEEL</span>
        </a>
        <h1 class="auth-title" data-ar="دخول الإدارة" data-en="Admin sign in">دخول الإدارة</h1>
        <p class="auth-lead" data-ar="سجّل الدخول إلى لوحة إدارة كحيل" data-en="Sign in to the Kaheel admin panel">سجّل الدخول إلى لوحة إدارة كحيل</p>

        <div class="auth-card">
            <p class="auth-alert" id="auth-alert" hidden></p>
            <form class="auth-form" id="admin-login-form">
                <div class="auth-field">
                    <label for="email" data-ar="البريد الإلكتروني" data-en="Email">البريد الإلكتروني</label>
                    <input id="email" type="email" dir="ltr" autocomplete="username" required>
                </div>
                <div class="auth-field">
                    <label for="password" data-ar="كلمة المرور" data-en="Password">كلمة المرور</label>
                    <input id="password" type="password" dir="ltr" autocomplete="current-password" required>
                </div>
                <button class="btn btn-lg btn-primary auth-submit" type="submit" id="login-submit" data-ar="دخول" data-en="Sign in">دخول</button>
            </form>
        </div>
    </main>

    <style>
        .admin-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin-bottom: 1.75rem;
            line-height: 1;
            color: inherit;
        }

        .admin-logo .brand-name {
            display: inline;
        }
    </style>

    <script>
        const locale = <?php echo json_encode($locale, 15, 512) ?>;
        const copy = {
            ar: {
                title: "دخول الإدارة | FORMA BY AZURE – KAHEEL",
                invalidEmail: "بريد إلكتروني غير صحيح",
                missingPassword: "كلمة المرور مطلوبة",
                failed: "تعذر تسجيل الدخول"
            },
            en: {
                title: "Admin sign in | FORMA BY AZURE – KAHEEL",
                invalidEmail: "Invalid email",
                missingPassword: "Password is required",
                failed: "Sign in could not be completed"
            }
        };

        const root = document.documentElement;
        const alertBox = document.getElementById("auth-alert");
        let lang = locale === "en" ? "en" : "ar";

        function text(key) {
            return copy[lang][key];
        }

        function showAlert(message) {
            alertBox.hidden = false;
            alertBox.textContent = message;
            alertBox.className = "auth-alert is-error";
        }

        function applyLang(next) {
            lang = next;
            root.lang = lang;
            root.dir = lang === "ar" ? "rtl" : "ltr";
            document.querySelectorAll("[data-ar]").forEach((node) => {
                node.textContent = lang === "ar" ? node.dataset.ar : node.dataset.en;
            });
            document.getElementById("lang-label").textContent = lang === "ar" ? "EN" : "ع";
            document.title = text("title");
            localStorage.setItem("forma-lang", lang);
        }

        document.getElementById("lang-toggle").addEventListener("click", () => {
            applyLang(lang === "ar" ? "en" : "ar");
        });

        document.getElementById("admin-login-form").addEventListener("submit", async (event) => {
            event.preventDefault();
            alertBox.hidden = true;
            const email = document.getElementById("email").value.trim();
            const password = document.getElementById("password").value;
            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                showAlert(text("invalidEmail"));
                return;
            }
            if (!password) {
                showAlert(text("missingPassword"));
                return;
            }

            const submit = document.getElementById("login-submit");
            submit.disabled = true;
            try {
                const response = await fetch("/web/admin/login", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "X-Requested-With": "XMLHttpRequest"
                    },
                    body: JSON.stringify({ email, password })
                });
                const raw = await response.text();
                let data = {};
                try {
                    data = JSON.parse(raw);
                } catch (error) {
                    showAlert(text("failed"));
                    return;
                }
                if (data.status === true && data.items && data.items.token) {
                    localStorage.setItem("token", data.items.token);
                    localStorage.setItem("authToken", data.items.token);
                    location.assign("/" + locale + "/dashboard");
                    return;
                }
                showAlert(data.message || text("failed"));
            } catch (error) {
                showAlert(text("failed"));
            } finally {
                submit.disabled = false;
            }
        });

        applyLang(lang);
    </script>
</body>
</html>
<?php /**PATH E:\kaheel\resources\views/kaheel/admin-login.blade.php ENDPATH**/ ?>