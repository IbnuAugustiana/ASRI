<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RegisteredUserController extends Controller
{
    /**
     * Menampilkan halaman registrasi
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Proses registrasi user baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email',
            ],

            'no_hp' => [
                'required',
                'string',
                'max:15',
            ],

            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'no_hp'     => $validated['no_hp'],
            'password'  => Hash::make($validated['password']),
            'role'      => 'calon_penghuni',
            'is_active' => true,
        ]);

      event(new Registered($user));

Auth::login($user);

return redirect()->route('calon.dashboard')->with([
    'success' => true,
    'title'   => 'Registrasi Berhasil',
    'message' => 'Akun Anda berhasil dibuat.'
]);
    }
}
