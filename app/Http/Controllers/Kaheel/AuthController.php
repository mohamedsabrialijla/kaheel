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
    public function login(Request $request, BricksCustomerClient $bricks)
    {
        $isAr = app()->getLocale() === 'ar';

        $validator = Validator::make($request->all(), [
            'login' => 'required|string|max:190',
            'password' => 'required|string|max:190',
        ], [
            'login.required' => $isAr ? 'اسم المستخدم مطلوب.' : 'Username is required.',
            'password.required' => $isAr ? 'كلمة المرور مطلوبة.' : 'Password is required.',
        ]);

        if ($validator->fails()) {
            return mainResponse(false, '', null, 422, 'items', $validator);
        }

        $result = $bricks->login(trim((string) $request->input('login')), (string) $request->input('password'));
        if (!empty($result['down'])) {
            $message = $isAr ? 'تعذر الاتصال بخدمة الدخول حالياً.' : 'The sign-in service is unavailable right now.';

            return mainResponse(false, $message, null, 503, 'items', '');
        }
        if (empty($result['ok'])) {
            $message = $isAr ? 'اسم المستخدم أو كلمة المرور غير صحيحة.' : 'Username or password is incorrect.';

            return mainResponse(false, $message, null, 422, 'items', '');
        }

        $user = $result['user'];
        $request->session()->regenerate();
        KaheelCustomerSession::query()
            ->where('session_id', $request->session()->getId())
            ->orWhere('updated_at', '<', now()->subHours(12))
            ->delete();

        $name = trim((string) ($user['display_name'] ?? $user['name'] ?? ''));
        $row = KaheelCustomerSession::query()->create([
            'session_id' => $request->session()->getId(),
            'bricks_user_id' => $user['id'] ?? null,
            'username' => (string) ($user['username'] ?? $request->input('login')),
            'name' => $name !== '' ? $name : null,
            'name_ar' => $user['name_ar'] ?? null,
            'email' => $user['email'] ?? null,
            'mobile' => $user['mobile_number'] ?? null,
            'auth_token' => $result['token'],
            'slug' => (string) config('services.bricks.slug'),
            'units' => $result['units'] ?? [],
        ]);
        $request->session()->put('kaheel_customer_session_id', $row->id);

        return mainResponse(true, $isAr ? 'تم تسجيل الدخول بنجاح.' : 'Login successful.', [
            'id' => $row->bricks_user_id,
            'name' => $row->name ?: $row->username,
            'login' => $row->username,
            'email' => $row->email,
            'mobile' => $row->mobile,
            'token' => hash('sha256', $row->id.'|'.$row->session_id),
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

    public function resetPassword(Request $request)
    {
        return $this->closed();
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
