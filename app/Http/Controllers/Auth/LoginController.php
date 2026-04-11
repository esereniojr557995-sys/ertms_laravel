<?php
// app/Http/Controllers/Auth/LoginController.php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return $this->redirectByRole(Auth::user()->role);
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validateWithBag('loginBag', [
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectByRole(Auth::user()->role);
        }

        return back()
            ->withErrors(['email' => 'Invalid email or password.'], 'loginBag')
            ->onlyInput('email');
    }

    public function register(Request $request)
    {
        $data = $request->validateWithBag('registerBag', [
            'first_name' => ['required', 'string', 'max:60'],
            'last_name'  => ['required', 'string', 'max:60'],
            'email'      => ['required', 'email', 'unique:users,email'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'password'   => ['required', 'min:8', 'confirmed'],
        ], [
            'email.unique'        => 'An account with this email already exists.',
            'password.min'        => 'Password must be at least 8 characters.',
            'password.confirmed'  => 'Passwords do not match.',
        ]);

        $user = User::create([
            'name'     => trim($data['first_name'] . ' ' . $data['last_name']),
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'phone'    => $data['phone'] ?? null,
            'role'     => 'citizen',
            'status'   => 'active',
        ]);

        Auth::login($user);

        return redirect()->route('citizen.dashboard')
            ->with('success', 'Welcome to ERTMS, ' . $user->name . '! Your citizen account has been created.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    private function redirectByRole(string $role)
    {
        return match($role) {
            'admin'     => redirect()->route('admin.dashboard'),
            'commander' => redirect()->route('commander.dashboard'),
            'responder' => redirect()->route('responder.dashboard'),
            'citizen'   => redirect()->route('citizen.dashboard'),
            default     => redirect()->route('login'),
        };
    }
}