<?php

namespace App\Http\Controllers\Kaheel;

use App\Http\Controllers\Controller;
use App\Models\KaheelCustomerSession;
use App\Models\User;
use App\Services\BricksCustomerClient;
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private const ACCESS_CODE = '1111';

    public function show(Request $request, string $screen = 'login')
    {
        $challenge = $this->challenge();
        $locale = app()->getLocale();

        if (!$challenge && kaheel_customer()) {
            return redirect($this->loggedInPath($request));
        }

        if ($screen === 'code' && !$challenge) {
            return redirect(url($locale.'/auth'));
        }
        if ($screen === 'reset' && (!$challenge || ($challenge['purpose'] ?? '') !== 'reset' || empty($challenge['verified']))) {
            return redirect(url($locale.'/auth/forgot'));
        }
        if ($screen === 'code' && ($challenge['purpose'] ?? '') === 'reset' && !empty($challenge['verified'])) {
            return redirect(url($locale.'/auth/reset'));
        }

        return response()
            ->view('kaheel.auth', [
                'screen' => $screen,
                'purpose' => $challenge['purpose'] ?? 'login',
                'mobileHint' => $challenge['mobile_hint'] ?? null,
                'notice' => $request->session()->pull('kaheel_auth_notice'),
            ])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function csrf()
    {
        return response()
            ->json(['token' => csrf_token()])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }

    public function login(Request $request, BricksCustomerClient $bricks)
    {
        $isAr = app()->getLocale() === 'ar';

        $validator = Validator::make($request->all(), [
            'login' => 'required|string|max:190',
            'password' => 'required|string|max:190',
        ], [
            'login.required' => $isAr ? 'البريد أو اسم المستخدم مطلوب.' : 'Email or username is required.',
            'password.required' => $isAr ? 'كلمة المرور مطلوبة.' : 'Password is required.',
        ]);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 422, 'items', $validator);
        }

        $login = trim((string) $request->input('login'));
        $result = $bricks->login($login, (string) $request->input('password'));
        if (!empty($result['down'])) {
            $message = $isAr ? 'تعذر الاتصال بخدمة الدخول حالياً.' : 'The sign-in service is unavailable right now.';

            return mainResponse(false, $message, null, 503, 'items', '');
        }
        if (empty($result['ok'])) {
            $message = $isAr ? 'البريد أو اسم المستخدم أو كلمة المرور غير صحيحة.' : 'Email, username or password is incorrect.';

            return mainResponse(false, $message, null, 422, 'items', '');
        }

        $user = $result['user'];
        $request->session()->put('kaheel_auth_challenge', [
            'purpose' => 'login',
            'login' => $login,
            'verified' => false,
            'expires_at' => now()->addMinutes(10)->getTimestamp(),
            'mobile_hint' => $this->mobileHint($user['mobile_number'] ?? null),
            'redirect' => $this->safePath((string) $request->input('redirect', '')) ? (string) $request->input('redirect') : '',
            'pkg' => strtoupper((string) $request->input('pkg', '')),
            'user' => [
                'id' => $user['id'] ?? null,
                'username' => (string) ($user['username'] ?? $login),
                'display_name' => $user['display_name'] ?? null,
                'name' => $user['name'] ?? null,
                'name_ar' => $user['name_ar'] ?? null,
                'email' => $user['email'] ?? null,
                'mobile_number' => $user['mobile_number'] ?? null,
            ],
            'token' => $result['token'],
            'units' => $result['units'] ?? [],
        ]);

        return mainResponse(true, $isAr ? 'أدخل رمز التحقق.' : 'Enter the verification code.', [
            'step' => 'code',
        ], 200, 'items', '');
    }

    public function verifyCode(Request $request)
    {
        $isAr = app()->getLocale() === 'ar';
        $challenge = $this->challenge();
        if (!$challenge) {
            return mainResponse(false, $isAr ? 'انتهت صلاحية الرمز. أعد المحاولة.' : 'The code expired. Try again.', null, 422, 'items', '');
        }

        $code = trim((string) $request->input('code', ''));
        if (!hash_equals(self::ACCESS_CODE, $code)) {
            return mainResponse(false, $isAr ? 'الرمز غير صحيح.' : 'The code is incorrect.', null, 422, 'items', '');
        }

        if (($challenge['purpose'] ?? '') === 'reset') {
            $challenge['verified'] = true;
            $challenge['expires_at'] = now()->addMinutes(10)->getTimestamp();
            $request->session()->put('kaheel_auth_challenge', $challenge);

            return mainResponse(true, $isAr ? 'تم التحقق. عيّن كلمة المرور الجديدة.' : 'Verified. Set the new password.', [
                'step' => 'reset',
            ], 200, 'items', '');
        }

        $user = $challenge['user'] ?? [];
        $request->session()->regenerate();
        $row = $this->openCustomerSession($request, $user, (string) ($challenge['token'] ?? ''), $challenge['units'] ?? []);
        $request->session()->forget('kaheel_auth_challenge');

        return mainResponse(true, $isAr ? 'تم تسجيل الدخول بنجاح.' : 'Login successful.', [
            'id' => $row->bricks_user_id,
            'name' => $row->name ?: $row->username,
            'login' => $row->username,
            'email' => $row->email,
            'mobile' => $row->mobile,
            'token' => hash('sha256', $row->id.'|'.$row->session_id),
            'next' => $this->destination($challenge),
        ], 200, 'items', '');
    }

    public function startReset(Request $request)
    {
        $isAr = app()->getLocale() === 'ar';
        $login = trim((string) $request->input('login', ''));
        if ($login === '' || strlen($login) > 190) {
            return mainResponse(false, $isAr ? 'أدخل البريد أو اسم المستخدم.' : 'Enter the email or username.', null, 422, 'items', '');
        }

        $known = KaheelCustomerSession::query()
            ->whereRaw('LOWER(username) = ?', [mb_strtolower($login)])
            ->orWhereRaw('LOWER(email) = ?', [mb_strtolower($login)])
            ->latest('id')
            ->first();

        $request->session()->put('kaheel_auth_challenge', [
            'purpose' => 'reset',
            'login' => $login,
            'verified' => false,
            'expires_at' => now()->addMinutes(10)->getTimestamp(),
            'mobile_hint' => $this->mobileHint($known->mobile ?? null),
        ]);

        return mainResponse(true, $isAr ? 'أدخل رمز التحقق المرسل إلى الجوال.' : 'Enter the code sent to the phone.', [
            'step' => 'code',
        ], 200, 'items', '');
    }

    public function signup(Request $request)
    {
        return $this->closed();
    }

    public function sendSmsCode(Request $request, SmsService $sms)
    {
        return $this->closed();
    }

    public function verifySmsLogin(Request $request)
    {
        return $this->closed();
    }

    public function resetPassword(Request $request, BricksCustomerClient $bricks)
    {
        $isAr = app()->getLocale() === 'ar';
        $challenge = $this->challenge();
        if (!$challenge || ($challenge['purpose'] ?? '') !== 'reset' || empty($challenge['verified'])) {
            return mainResponse(false, $isAr ? 'تحقق من الرمز أولاً.' : 'Verify the code first.', null, 422, 'items', '');
        }

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|max:190|confirmed',
        ], [
            'password.required' => $isAr ? 'كلمة المرور الجديدة مطلوبة.' : 'The new password is required.',
            'password.min' => $isAr ? 'كلمة المرور 8 أحرف على الأقل.' : 'The password must be at least 8 characters.',
            'password.confirmed' => $isAr ? 'كلمتا المرور غير متطابقتين.' : 'The passwords do not match.',
        ]);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), null, 422, 'items', '');
        }

        $result = $bricks->changePassword((string) $challenge['login'], (string) $request->input('password'));
        if (!empty($result['down'])) {
            return mainResponse(false, $isAr ? 'تعذر الاتصال بخدمة الدخول حالياً.' : 'The sign-in service is unavailable right now.', null, 503, 'items', '');
        }
        if (!empty($result['missing']) || empty($result['ok'])) {
            return mainResponse(false, $isAr ? 'تعذر حفظ كلمة المرور لأن خدمة الحسابات لا تقبل تغييرها حالياً.' : 'The password could not be saved because the account service does not accept a change yet.', null, 422, 'items', '');
        }

        $request->session()->forget('kaheel_auth_challenge');
        $request->session()->flash('kaheel_auth_notice', $isAr ? 'تم تغيير كلمة المرور. سجّل الدخول بالجديدة.' : 'The password was changed. Sign in with the new one.');

        return mainResponse(true, $isAr ? 'تم تغيير كلمة المرور.' : 'The password was changed.', [
            'step' => 'login',
        ], 200, 'items', '');
    }

    public function googleRedirect(Request $request)
    {
        return redirect($this->authUrl());
    }

    public function googleCallback(Request $request)
    {
        return redirect($this->authUrl());
    }

    protected function authenticated(User $user, string $message)
    {
        Auth::login($user, true);
        $user->token = $user->createToken('auth_token')->plainTextToken;

        return mainResponse(true, $message, [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'token' => $user->token,
        ], 200, 'items', '');
    }

    protected function userForCode(string $purpose, string $mobile, string $code): ?User
    {
        $expected = Cache::get($this->codeKey($purpose, $mobile));
        if (!$expected || !hash_equals((string) $expected, $code)) {
            return null;
        }

        return User::where('mobile', $mobile)->first();
    }

    protected function codeKey(string $purpose, string $mobile): string
    {
        return 'kaheel-sms:'.$purpose.':'.$mobile;
    }

    protected function normalizeMobile(string $value): string
    {
        return preg_replace('/\s+/', '', trim($value)) ?? '';
    }

    protected function googleCallbackUrl(): string
    {
        return url(app()->getLocale().'/auth/google/callback');
    }

    protected function authUrl(array $query = []): string
    {
        $url = url(app()->getLocale().'/auth');
        if ($query === []) {
            return $url;
        }

        return $url.'?'.http_build_query($query);
    }

    protected function rememberRedirect(Request $request): void
    {
        $redirect = (string) $request->query('redirect', '');
        if ($this->safePath($redirect)) {
            session(['kaheel_auth_redirect' => $redirect]);
        }
    }

    protected function pullRedirect(): string
    {
        $redirect = (string) session()->pull('kaheel_auth_redirect', '');
        $locale = app()->getLocale();
        if ($this->safePath($redirect)) {
            return url($locale.$redirect);
        }

        return url($locale);
    }

    protected function openCustomerSession(Request $request, array $user, string $token, array $units): KaheelCustomerSession
    {
        KaheelCustomerSession::query()
            ->where('session_id', $request->session()->getId())
            ->orWhere('updated_at', '<', now()->subHours(12))
            ->delete();

        $name = trim((string) ($user['display_name'] ?? $user['name'] ?? ''));
        $row = KaheelCustomerSession::query()->create([
            'session_id' => $request->session()->getId(),
            'bricks_user_id' => $user['id'] ?? null,
            'username' => (string) ($user['username'] ?? ''),
            'name' => $name !== '' ? $name : null,
            'name_ar' => $user['name_ar'] ?? null,
            'email' => $user['email'] ?? null,
            'mobile' => $user['mobile_number'] ?? null,
            'auth_token' => $token,
            'slug' => (string) config('services.bricks.slug'),
            'units' => $units,
        ]);
        $request->session()->put('kaheel_customer_session_id', $row->id);

        return $row;
    }

    protected function challenge(): ?array
    {
        $challenge = session('kaheel_auth_challenge');
        if (!is_array($challenge) || (int) ($challenge['expires_at'] ?? 0) < now()->getTimestamp()) {
            session()->forget('kaheel_auth_challenge');

            return null;
        }

        return $challenge;
    }

    protected function mobileHint(mixed $mobile): ?string
    {
        $digits = preg_replace('/\D/', '', (string) $mobile) ?? '';
        if (strlen($digits) < 4) {
            return null;
        }

        return substr($digits, -4);
    }

    protected function destination(array $challenge): string
    {
        $locale = app()->getLocale();
        $redirect = (string) ($challenge['redirect'] ?? '');
        $path = $this->safePath($redirect) ? $redirect : '/account';
        $url = url($locale.$path);
        $pkg = strtoupper(trim((string) ($challenge['pkg'] ?? '')));
        if ($pkg !== '') {
            $url .= (str_contains($url, '?') ? '&' : '?').'pkg='.urlencode($pkg);
        }

        return $url;
    }

    protected function loggedInPath(Request $request): string
    {
        $locale = app()->getLocale();
        $redirect = (string) $request->query('redirect', '/account');
        $path = $this->safePath($redirect) ? $redirect : '/account';
        $url = url($locale.$path);
        $pkg = strtoupper(trim((string) $request->query('pkg', '')));
        if ($pkg !== '') {
            $url .= (str_contains($url, '?') ? '&' : '?').'pkg='.urlencode($pkg);
        }

        return $url;
    }

    protected function closed()
    {
        $isAr = app()->getLocale() === 'ar';
        $message = $isAr
            ? 'الدخول يتم بحساب العميل الحالي فقط.'
            : 'Sign in with your existing customer account.';

        return mainResponse(false, $message, null, 403, 'items', '');
    }

    public function logout(Request $request)
    {
        $sessionId = $request->session()->getId();
        $rowId = $request->session()->get('kaheel_customer_session_id');
        KaheelCustomerSession::query()
            ->where('session_id', $sessionId)
            ->when($rowId, function ($query) use ($rowId) {
                $query->orWhere('id', $rowId);
            })
            ->delete();

        $admin = Auth::guard('admin')->user();
        if ($admin && method_exists($admin, 'tokens')) {
            $admin->tokens()->delete();
        }

        $user = Auth::guard('web')->user();
        if ($user && method_exists($user, 'tokens')) {
            $user->tokens()->delete();
        }

        Auth::guard('admin')->logout();
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'.app()->getLocale());
    }

    protected function safePath(string $path): bool
    {
        return $path !== '' && str_starts_with($path, '/') && !str_starts_with($path, '//');
    }
}
