<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }
    public function register(Request $request)
    {
        $v = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:users,email',
            'mobile' => 'nullable|string|unique:users,mobile',
            'password' => 'required|string|min:6|confirmed',
            'role' => 'nullable|in:customer,staff,admin'
        ]);
        $v['role'] = $v['role'] ?? 'customer';
        $v['password'] = bcrypt($v['password']);
        $user = User::create($v);
        Auth::login($user);
        return redirect()->route('services.index')
            ->with('status', 'Registered & logged in');
    }
    public function showLogin()
    {
        return view('auth.login');
    }
    public function login(Request $request)
    {
        $v = $request->validate([
            'email_or_mobile' => 'required|string',
            'password' => 'required|string'
        ]);
        $field = filter_var($v['email_or_mobile'], FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';
        if (Auth::attempt([$field => $v['email_or_mobile'], 'password' => $v['password']])) {
            $request->session()->regenerate();
            return redirect()->intended(route('services.index'));
        }
        return back()->withErrors(['email_or_mobile' => 'Invalid credentials'])->withInput();
    }
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        return redirect()->route('services.index');
    }
}
