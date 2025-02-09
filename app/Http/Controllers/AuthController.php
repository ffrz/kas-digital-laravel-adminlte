<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    private function _logout(Request $request)
    {
        $user = Auth::user();
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }

    public function login()
    {
        return view('pages.auth.login');
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }

        $error = '';
        $data = $request->only(['username', 'password']);
        if (!Auth::attempt($data)) {
            $error = 'Username atau password salah!';
        } else if (!Auth::user()->is_active) {
            $error = 'Akun anda tidak aktif. Silahkan hubungi administrator!';
            $this->_logout($request);
        } else {
            $request->session()->regenerate();
            return redirect('/dashboard');
        }

        return redirect()->back()->withInput()->with('error', $error);
    }

    public function logout(Request $request)
    {
        $this->_logout($request);
        return redirect('/login');
    }
}
