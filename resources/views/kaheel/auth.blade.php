@php
    $pkg = strtoupper((string) request('pkg', ''));
    if (!in_array($pkg, ['VIP', 'CLASSIC'], true)) {
        $pkg = '';
    }
    $home = url(app()->getLocale());
@endphp
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>تسجيل الدخول | FORMA BY AZURE – KAHEEL</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600&family=Instrument+Serif:ital@0;1&family=Work+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('kaheel/home.css') }}?v=25" rel="stylesheet">
</head>
<body>
    <header class="site-header">
        <div class="header-inner">
            <a class="brand" href="{{ $home }}" aria-label="FORMA BY AZURE — KAHEEL">
                <span class="brand-mark">
                    <span class="brand-forma">FORMA</span>
                    <span class="brand-azure">BY AZURE</span>
                </span>
                <span class="brand-name">KAHEEL</span>
            </a>
            <nav class="nav" aria-label="main">
                <a class="btn btn-sm nav-home" href="{{ $home }}" data-ar="الرئيسية" data-en="Home">الرئيسية</a>
                @include('kaheel.partials.role-links')
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
                @include('kaheel.partials.account-button')
            </nav>
        </div>
    </header>

    <main class="auth-page">
        <h1 class="auth-title" data-ar="{{ $screen === 'code' ? 'رمز التحقق' : ($screen === 'forgot' ? 'نسيت كلمة المرور' : ($screen === 'reset' ? 'كلمة مرور جديدة' : 'الدخول إلى حسابك')) }}" data-en="{{ $screen === 'code' ? 'Verification code' : ($screen === 'forgot' ? 'Forgot password' : ($screen === 'reset' ? 'New password' : 'Access your account')) }}">{{ $screen === 'code' ? 'رمز التحقق' : ($screen === 'forgot' ? 'نسيت كلمة المرور' : ($screen === 'reset' ? 'كلمة مرور جديدة' : 'الدخول إلى حسابك')) }}</h1>
        <p class="auth-lead" id="auth-lead" data-ar="{{ $screen === 'code' ? ($purpose === 'reset' ? 'أدخل الرمز المرسل إلى جوالك لإعادة تعيين كلمة المرور.' : 'أدخل الرمز المرسل إلى جوالك لإكمال الدخول.') : ($screen === 'forgot' ? 'أدخل البريد أو اسم المستخدم لإرسال الرمز إلى الجوال.' : ($screen === 'reset' ? 'اختر كلمة المرور الجديدة لحسابك.' : 'سجّل الدخول لإكمال طلبك')) }}" data-en="{{ $screen === 'code' ? ($purpose === 'reset' ? 'Enter the code sent to your phone to reset the password.' : 'Enter the code sent to your phone to finish signing in.') : ($screen === 'forgot' ? 'Enter the email or username to send the code to the phone.' : ($screen === 'reset' ? 'Choose a new password for your account.' : 'Sign in to continue your order')) }}">{{ $screen === 'code' ? ($purpose === 'reset' ? 'أدخل الرمز المرسل إلى جوالك لإعادة تعيين كلمة المرور.' : 'أدخل الرمز المرسل إلى جوالك لإكمال الدخول.') : ($screen === 'forgot' ? 'أدخل البريد أو اسم المستخدم لإرسال الرمز إلى الجوال.' : ($screen === 'reset' ? 'اختر كلمة المرور الجديدة لحسابك.' : 'سجّل الدخول لإكمال طلبك')) }}</p>

        @if ($pkg !== '')
            <div class="auth-package">
                <span class="auth-package-label" data-ar="الباقة المختارة:" data-en="Selected package:">الباقة المختارة:</span>
                <span class="auth-package-code">{{ $pkg }}</span>
            </div>
        @endif

        <div class="auth-card">
            <p class="auth-alert {{ $notice ? 'is-success' : '' }}" id="auth-alert" @if(!$notice) hidden @endif>{{ $notice }}</p>

            @if ($screen === 'login')
                <form class="auth-form" id="password-form">
                    <div class="auth-field">
                        <label for="login" data-ar="البريد الإلكتروني أو اسم المستخدم" data-en="Email or username">البريد الإلكتروني أو اسم المستخدم</label>
                        <input id="login" type="text" maxlength="190" dir="ltr" autocomplete="username">
                    </div>
                    <div class="auth-field">
                        <label for="password" data-ar="كلمة المرور" data-en="Password">كلمة المرور</label>
                        <input id="password" type="password" dir="ltr" autocomplete="current-password">
                    </div>
                    <button class="btn btn-lg btn-primary auth-submit" type="submit" id="password-submit">
                        <span class="auth-submit-label" data-ar="دخول" data-en="Log in">دخول</span>
                        <span class="auth-submit-wait" hidden>
                            <span class="auth-spinner" aria-hidden="true"></span>
                            <span data-ar="جارٍ الدخول…" data-en="Signing in…">جارٍ الدخول…</span>
                        </span>
                    </button>
                    <a class="auth-text-btn" href="{{ url(app()->getLocale().'/auth/forgot') }}" data-ar="نسيت كلمة المرور؟" data-en="Forgot password?">نسيت كلمة المرور؟</a>
                </form>
            @elseif ($screen === 'code')
                <form class="auth-form" id="code-form">
                    @if ($mobileHint)
                        <p class="auth-note" data-ar="الرمز يذهب إلى الجوال المنتهي بـ {{ $mobileHint }}" data-en="The code goes to the phone ending in {{ $mobileHint }}">الرمز يذهب إلى الجوال المنتهي بـ {{ $mobileHint }}</p>
                    @endif
                    <p class="auth-note" data-ar="إلى حين ربط الرسائل، الرمز هو 1111." data-en="Until messages are connected, the code is 1111.">إلى حين ربط الرسائل، الرمز هو 1111.</p>
                    <div class="auth-field">
                        <label for="code-1" data-ar="رمز التحقق" data-en="Verification code">رمز التحقق</label>
                        <div class="auth-code-row" dir="ltr">
                            <input id="code-1" class="auth-code" type="text" inputmode="numeric" maxlength="1" autocomplete="one-time-code" aria-label="1">
                            <input id="code-2" class="auth-code" type="text" inputmode="numeric" maxlength="1" aria-label="2">
                            <input id="code-3" class="auth-code" type="text" inputmode="numeric" maxlength="1" aria-label="3">
                            <input id="code-4" class="auth-code" type="text" inputmode="numeric" maxlength="1" aria-label="4">
                        </div>
                    </div>
                    <button class="btn btn-lg btn-primary auth-submit" type="submit" id="code-submit" data-ar="تأكيد" data-en="Confirm">تأكيد</button>
                </form>
            @elseif ($screen === 'forgot')
                <form class="auth-form" id="forgot-form">
                    <div class="auth-field">
                        <label for="login" data-ar="البريد الإلكتروني أو اسم المستخدم" data-en="Email or username">البريد الإلكتروني أو اسم المستخدم</label>
                        <input id="login" type="text" maxlength="190" dir="ltr" autocomplete="username">
                    </div>
                    <button class="btn btn-lg btn-primary auth-submit" type="submit" id="forgot-submit" data-ar="إرسال الرمز" data-en="Send code">إرسال الرمز</button>
                    <a class="auth-text-btn" href="{{ url(app()->getLocale().'/auth') }}" data-ar="العودة لتسجيل الدخول" data-en="Back to sign in">العودة لتسجيل الدخول</a>
                </form>
            @else
                <form class="auth-form" id="reset-form">
                    <div class="auth-field">
                        <label for="password" data-ar="كلمة المرور الجديدة" data-en="New password">كلمة المرور الجديدة</label>
                        <input id="password" type="password" dir="ltr" autocomplete="new-password">
                    </div>
                    <div class="auth-field">
                        <label for="password_confirmation" data-ar="تأكيد كلمة المرور" data-en="Confirm password">تأكيد كلمة المرور</label>
                        <input id="password_confirmation" type="password" dir="ltr" autocomplete="new-password">
                    </div>
                    <button class="btn btn-lg btn-primary auth-submit" type="submit" id="reset-submit" data-ar="حفظ كلمة المرور" data-en="Save password">حفظ كلمة المرور</button>
                </form>
            @endif
        </div>
    </main>

    <script>
        const authConfig = {
            locale: @json(app()->getLocale()),
            screen: @json($screen),
            redirect: @json((string) request('redirect', '')),
            pkg: @json((string) request('pkg', ''))
        };

        const copy = {
            ar: {
                required: "البريد أو اسم المستخدم وكلمة المرور مطلوبان",
                forgotRequired: "أدخل البريد أو اسم المستخدم",
                codeRequired: "أدخل رمز التحقق",
                resetRequired: "أدخل كلمة المرور وتأكيدها",
                failed: "تعذر إكمال الطلب",
                title: "تسجيل الدخول | FORMA BY AZURE – KAHEEL"
            },
            en: {
                required: "Email or username and password are required",
                forgotRequired: "Enter the email or username",
                codeRequired: "Enter the verification code",
                resetRequired: "Enter the password and its confirmation",
                failed: "The request could not be completed",
                title: "Sign in | FORMA BY AZURE – KAHEEL"
            }
        };

        const root = document.documentElement;
        let lang = "ar";
        const alertBox = document.getElementById("auth-alert");

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
                node.textContent = lang === "ar" ? node.dataset.ar : node.dataset.en;
            });
            document.getElementById("lang-label").textContent = lang === "ar" ? "EN" : "ع";
            document.title = text("title");
            localStorage.setItem("forma-lang", lang);
        }

        async function csrfToken() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            try {
                const response = await fetch("/" + authConfig.locale + "/auth/csrf", {
                    headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" },
                    credentials: "same-origin",
                    cache: "no-store"
                });
                const data = await response.json();
                if (data && data.token) {
                    meta.setAttribute("content", data.token);
                    return data.token;
                }
            } catch (error) {}
            return meta.content;
        }

        async function post(path, body) {
            const response = await fetch("/" + authConfig.locale + path, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": await csrfToken(),
                    "X-Requested-With": "XMLHttpRequest"
                },
                credentials: "same-origin",
                body: JSON.stringify(body)
            });
            const raw = await response.text();
            try {
                return JSON.parse(raw);
            } catch (error) {
                return { status: false, message: text("failed") };
            }
        }

        document.getElementById("lang-toggle").addEventListener("click", () => {
            applyLang(lang === "ar" ? "en" : "ar");
        });

        const passwordForm = document.getElementById("password-form");
        const codeForm = document.getElementById("code-form");
        const forgotForm = document.getElementById("forgot-form");
        const resetForm = document.getElementById("reset-form");

        if (passwordForm) passwordForm.addEventListener("submit", async (event) => {
            event.preventDefault();
            clearAlert();
            const login = document.getElementById("login").value.trim();
            const password = document.getElementById("password").value;
            if (!login || !password) {
                showAlert(text("required"), "error");
                return;
            }
            const submit = document.getElementById("password-submit");
            submit.disabled = true;
            submit.setAttribute("aria-busy", "true");
            submit.querySelector(".auth-submit-label").hidden = true;
            submit.querySelector(".auth-submit-wait").hidden = false;
            const data = await post("/auth/session", { login, password, redirect: authConfig.redirect, pkg: authConfig.pkg });
            if (!data || data.status !== true || !data.items || data.items.step !== "code") {
                submit.disabled = false;
                submit.removeAttribute("aria-busy");
                submit.querySelector(".auth-submit-label").hidden = false;
                submit.querySelector(".auth-submit-wait").hidden = true;
                showAlert((data && data.message) || text("failed"), "error");
                return;
            }
            location.assign("/" + authConfig.locale + "/auth/code");
        });

        if (codeForm) {
            const boxes = [...codeForm.querySelectorAll(".auth-code")];
            boxes[0].focus();
            boxes.forEach((box, index) => {
                box.addEventListener("input", () => {
                    const digit = box.value.replace(/\D/g, "").slice(-1);
                    box.value = digit;
                    if (digit && boxes[index + 1]) boxes[index + 1].focus();
                });
                box.addEventListener("keydown", (event) => {
                    if (event.key === "Backspace" && !box.value && boxes[index - 1]) {
                        boxes[index - 1].focus();
                    }
                });
                box.addEventListener("paste", (event) => {
                    event.preventDefault();
                    const digits = (event.clipboardData.getData("text") || "").replace(/\D/g, "").slice(0, boxes.length);
                    digits.split("").forEach((digit, offset) => {
                        boxes[offset].value = digit;
                    });
                    boxes[Math.min(digits.length, boxes.length - 1)].focus();
                });
            });
        }

        if (codeForm) codeForm.addEventListener("submit", async (event) => {
            event.preventDefault();
            clearAlert();
            const code = [...codeForm.querySelectorAll(".auth-code")].map((box) => box.value).join("");
            if (code.length < 4) {
                showAlert(text("codeRequired"), "error");
                return;
            }
            const data = await post("/auth/code", { code });
            if (!data || data.status !== true || !data.items) {
                showAlert((data && data.message) || text("failed"), "error");
                return;
            }
            if (data.items.step === "reset") {
                location.assign("/" + authConfig.locale + "/auth/reset");
                return;
            }
            if (data.items.token) {
                localStorage.setItem("authToken", data.items.token);
                localStorage.setItem("token", data.items.token);
                localStorage.setItem("user", JSON.stringify(data.items));
            }
            location.assign(data.items.next || ("/" + authConfig.locale + "/account"));
        });

        if (forgotForm) forgotForm.addEventListener("submit", async (event) => {
            event.preventDefault();
            clearAlert();
            const login = document.getElementById("login").value.trim();
            if (!login) {
                showAlert(text("forgotRequired"), "error");
                return;
            }
            const data = await post("/auth/password/forgot", { login });
            if (!data || data.status !== true) {
                showAlert((data && data.message) || text("failed"), "error");
                return;
            }
            location.assign("/" + authConfig.locale + "/auth/code");
        });

        if (resetForm) resetForm.addEventListener("submit", async (event) => {
            event.preventDefault();
            clearAlert();
            const password = document.getElementById("password").value;
            const passwordConfirmation = document.getElementById("password_confirmation").value;
            if (!password || !passwordConfirmation) {
                showAlert(text("resetRequired"), "error");
                return;
            }
            const data = await post("/auth/password/reset", { password, password_confirmation: passwordConfirmation });
            if (!data || data.status !== true) {
                showAlert((data && data.message) || text("failed"), "error");
                return;
            }
            location.assign("/" + authConfig.locale + "/auth");
        });

        applyLang(localStorage.getItem("forma-lang") === "en" ? "en" : "ar");
    </script>
    @include('kaheel.partials.native-validation')
</body>
</html>
