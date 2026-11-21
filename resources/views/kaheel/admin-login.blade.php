@extends('kaheel.layouts.forma')
@php
    $title = 'دخول الإدارة | FORMA INTERIORS';
    $nav = '';
    $locale = app()->getLocale();
@endphp
@push('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .auth-page { max-width: 28rem; margin-inline: auto; display: flex; flex-direction: column; padding: 3.5rem 1.25rem 4rem; }
    .auth-title { margin: 0; font-family: var(--font-display); font-size: 1.875rem; font-weight: 400; line-height: 1.2; }
    .auth-lead { margin: 0.5rem 0 0; color: var(--muted-foreground); font-size: 0.875rem; }
    .auth-card { margin-top: 1.5rem; border: 1px solid var(--border); border-radius: 1.25rem; background: var(--card); box-shadow: 0 24px 60px -34px color-mix(in oklab, var(--foreground) 28%, transparent); padding: 1.5rem; }
    .auth-form { display: flex; flex-direction: column; gap: 1rem; margin-top: 0; }
    .auth-alert[hidden] { display: none; }
    .auth-alert { margin: 0 0 1rem; border: 1px solid color-mix(in oklab, var(--destructive) 35%, transparent); background: color-mix(in oklab, var(--destructive) 8%, transparent); padding: 0.75rem; font-size: 0.875rem; }
    .auth-alert.is-error { color: var(--destructive); }
    .auth-field { display: flex; flex-direction: column; gap: 0.375rem; }
    .auth-field label { font-size: 0.875rem; font-weight: 500; }
    .auth-field input { height: 2.25rem; width: 100%; border: 1px solid var(--input, var(--border)); border-radius: 6px; background: transparent; padding: 0.25rem 0.75rem; color: var(--foreground); font: inherit; font-size: 1rem; box-shadow: 0 1px 2px rgb(0 0 0 / 0.05); }
    .auth-field input:focus { outline: 1px solid var(--primary); }
    .auth-submit { width: 100%; height: 2.75rem; border: 0; background: var(--primary); color: var(--primary-foreground); font: inherit; font-size: 0.875rem; cursor: pointer; }
    .auth-submit:hover { opacity: 0.92; }
    .auth-submit:disabled { cursor: wait; opacity: 0.85; }
    .auth-submit-wait { display: inline-flex; align-items: center; gap: 0.5rem; }
    .auth-spinner { width: 1rem; height: 1rem; border: 2px solid currentColor; border-right-color: transparent; border-radius: 50%; animation: auth-spin .7s linear infinite; }
    @keyframes auth-spin { to { transform: rotate(360deg); } }
</style>
@endpush
@section('content')
<main class="auth-page">
    <h1 class="auth-title">دخول الإدارة</h1>
    <p class="auth-lead">سجّل الدخول إلى لوحة إدارة فورما</p>
    <div class="auth-card">
        <p class="auth-alert is-error" id="auth-alert" hidden></p>
        <form class="auth-form" id="admin-login-form">
            <div class="auth-field">
                <label for="email">البريد الإلكتروني</label>
                <input id="email" type="email" dir="ltr" autocomplete="username" required>
            </div>
            <div class="auth-field">
                <label for="password">كلمة المرور</label>
                <input id="password" type="password" dir="ltr" autocomplete="current-password" required>
            </div>
            <button class="auth-submit" type="submit" id="login-submit">
                <span class="auth-submit-label">دخول</span>
                <span class="auth-submit-wait" hidden>
                    <span class="auth-spinner" aria-hidden="true"></span>
                    <span>جارٍ الدخول…</span>
                </span>
            </button>
        </form>
    </div>
</main>
@endsection
@push('scripts')
<script>
    const locale = @json($locale);
    const copy = {
        ar: { invalidEmail: "بريد إلكتروني غير صحيح", missingPassword: "كلمة المرور مطلوبة", failed: "تعذر تسجيل الدخول" },
        en: { invalidEmail: "Invalid email", missingPassword: "Password is required", failed: "Sign in could not be completed" }
    };
    const alertBox = document.getElementById("auth-alert");
    const lang = () => document.documentElement.lang === "en" ? "en" : "ar";
    function showAlert(message) {
        alertBox.hidden = false;
        alertBox.textContent = message;
    }
    document.getElementById("admin-login-form").addEventListener("submit", async (event) => {
        event.preventDefault();
        alertBox.hidden = true;
        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value;
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showAlert(copy[lang()].invalidEmail);
            return;
        }
        if (!password) {
            showAlert(copy[lang()].missingPassword);
            return;
        }
        const submit = document.getElementById("login-submit");
        submit.disabled = true;
        submit.querySelector(".auth-submit-label").hidden = true;
        submit.querySelector(".auth-submit-wait").hidden = false;
        try {
            const response = await fetch("/web/admin/login", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "Accept": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "X-Requested-With": "XMLHttpRequest"
                },
                credentials: "same-origin",
                body: JSON.stringify({ email, password })
            });
            const raw = await response.text();
            let data = {};
            try { data = JSON.parse(raw); } catch (error) {
                showAlert(copy[lang()].failed);
                return;
            }
            if (data.status === true && data.items && data.items.token) {
                localStorage.setItem("token", data.items.token);
                localStorage.setItem("authToken", data.items.token);
                location.assign("/" + locale + "/dashboard");
                return;
            }
            showAlert(data.message || copy[lang()].failed);
        } catch (error) {
            showAlert(copy[lang()].failed);
        } finally {
            submit.disabled = false;
            submit.querySelector(".auth-submit-label").hidden = false;
            submit.querySelector(".auth-submit-wait").hidden = true;
        }
    });
</script>
@endpush
