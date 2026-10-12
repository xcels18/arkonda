<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Contract;
use App\Models\Opd;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        // Batasi durasi "Ingat saya" menjadi 14 hari (default Laravel 400 hari
        // dinilai terlalu lama untuk aplikasi arsip kontrak yang sensitif).
        Auth::guard('web')->setRememberDuration(60 * 24 * 14);

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $request->session()->put('password_hash_web', Auth::guard('web')->hashPasswordForCookie(Auth::user()->getAuthPassword()));

            // Log login activity
            AuditLog::logLogin(Auth::user());

            // Redirect based on verification status
            if (! Auth::user()->isAdmin() && ! Auth::user()->isVerified()) {
                return redirect()->route('verification.pending');
            }

            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Show registration form
     */
    public function showRegisterForm()
    {
        $opds = Opd::orderBy('nama')->get();

        return view('auth.register', compact('opds'));
    }

    /**
     * Handle registration request
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'opd_id' => ['required', 'exists:opds,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'opd_id' => $validated['opd_id'],
            'role' => 'user',
            'is_verified' => false,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('password_hash_web', Auth::guard('web')->hashPasswordForCookie($user->getAuthPassword()));

        return redirect()->route('verification.pending');
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            AuditLog::logLogout($user);
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Show verification pending page
     */
    public function verificationPending()
    {
        $user = Auth::user();

        // If already verified, redirect to dashboard
        if ($user && ($user->isAdmin() || $user->isVerified())) {
            return redirect()->route('dashboard');
        }

        return view('auth.verification-pending');
    }

    /**
     * Show dashboard
     */
    public function dashboard()
    {
        $query = Contract::visibleTo(Auth::user());
        $stats = [
            'total' => (clone $query)->count(),
            'nilai' => (clone $query)->sum('nilai_kontrak'),
            'dokumen' => (clone $query)->whereNotNull('file_path')->count(),
            'tahun' => (clone $query)->where('tahun_anggaran', now()->year)->count(),
        ];
        $recentContracts = (clone $query)->with('opd')->latest()->take(6)->get();
        $pendingUsers = Auth::user()->isAdmin() ? User::users()->unverified()->count() : 0;

        return view('dashboard', compact('stats', 'recentContracts', 'pendingUsers'));
    }
}
