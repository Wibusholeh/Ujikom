<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    // ==========================
    // WEB LOGIN
    // ==========================

    // Menampilkan Form Login
    public function showLoginForm()
    {
        return view('auth.login');
    }


    // Memproses Login Web
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Redirect berdasarkan role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');

            } elseif ($user->role === 'petugas') {
                return redirect()->route('petugas.peminjaman.index');

            } elseif ($user->role === 'peminjam') {
                return redirect()->route('peminjam.katalog');
            }

            Auth::logout();

            return redirect()
                ->route('login')
                ->with('error', 'Role tidak dikenali.');
        }

        return back()
            ->withErrors([
                'email' => 'Email atau password salah.',
            ])
            ->onlyInput('email');
    }


    // ==========================
    // WEB LOGOUT
    // ==========================

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }


    // ==========================
    // REST API SANCTUM
    // ==========================

    // Register API
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'role'     => 'nullable|string',
        ]);


        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => $validated['role'] ?? 'peminjam',
        ]);


        return response()->json([
            'message' => 'Registrasi berhasil',
            'user' => $user
        ], 201);
    }


    // Login API Sanctum
    public function loginApi(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);


        if (!Auth::attempt($credentials)) {

            return response()->json([
                'message' => 'Email atau password salah'
            ], 401);

        }


        $user = Auth::user();


        $token = $user->createToken('api-token')->plainTextToken;


        return response()->json([
            'message' => 'Login berhasil',
            'token' => $token,
            'user' => $user
        ]);
    }


    // Logout API Sanctum
    public function logoutApi(Request $request)
    {
        $request->user()->currentAccessToken()->delete();


        return response()->json([
            'message' => 'Logout berhasil'
        ]);
    }
}