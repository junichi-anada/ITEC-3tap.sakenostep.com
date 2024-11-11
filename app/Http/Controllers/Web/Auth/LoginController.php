<?php

namespace App\Http\Controllers\Web\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Auth\AuthService;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function index()
    {
        return view('index');
    }

    public function login(Request $request)
    {
        $credentials = $this->validateLogin($request);
        Log::info('Login attempt with credentials: ' . json_encode($credentials));

        try {
            $site = $this->authService->validateSite($credentials['site_code']);
            Log::info('Site validated: ' . $site->id);

            $auth = $this->authService->authenticateUser($credentials['login_code'], $site->id, $credentials['password']);
            Log::info('User authenticated: ' . $auth->id);

            return $this->handleUserWasAuthenticated($request, $auth);
        } catch (ValidationException $e) {
            Log::warning('Login failed: ' . json_encode($e->errors()));
            return back()->withErrors($e->errors());
        } catch (\Exception $e) {
            Log::error('Unexpected error during login: ' . $e->getMessage());
            return back()->withErrors(['error' => 'ログインに失敗しました。']);
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    private function validateLogin(Request $request)
    {
        return $request->validate([
            'login_code' => 'required|string',
            'password' => 'required|string',
            'site_code' => 'required|string',
        ]);
    }

    private function handleUserWasAuthenticated(Request $request, $auth)
    {
        Auth::login($auth);
        $auth->touch();
        $request->session()->regenerate();

        if (!Auth::check()) {
            Log::error('User is not authenticated after login.');
            return redirect('/login')->withErrors(['error' => 'ログインに失敗しました。']);
        }

        if ($auth->entity_type === 'App\Models\User') {
            Log::info('Redirecting to user order list');
            return redirect()->intended(route('user.order.item.list'));
        } elseif ($auth->entity_type === 'App\Models\Operator') {
            Log::info('Redirecting to operator dashboard');
            return redirect()->intended(route('operator.dashboard'));
        }

        Log::info('Redirecting to default user order list');
        return redirect()->intended(route('user.order.item.list'));
    }
}
