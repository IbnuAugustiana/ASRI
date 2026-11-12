<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // $request->validated() sekarang sudah otomatis menyertakan 'no_hp' 
        // setelah Anda menambahkannya di ProfileUpdateRequest
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Hapus foto_profil lama jika ada
        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        // Simpan file baru
        $path = $request->file('avatar')->store('avatars', 'public');

        // Update ke database menggunakan kolom foto_profil
        $user->foto_profil = $path;
        $user->save();

        return response()->json([
            'success' => true,
            'avatar_url' => asset('storage/' . $path)
        ]);
    }

    public function destroyAvatar(Request $request)
    {
        /** @var User $user */
        $user = $request->user();

        if ($user && $user->foto_profil) {
            Storage::disk('public')->delete($user->foto_profil);
            $user->foto_profil = null; // Set null ke kolom foto_profil
            $user->save();
        }

        return back()->with('status', 'avatar-deleted');
    }
    public function destroy(Request $request): RedirectResponse
{
    $request->validateWithBag('deleteAccount', [
        'confirm_text' => ['required', 'in:HAPUS AKUN'],
        'password' => ['required', 'current_password'],
    ], [
        'confirm_text.in' => 'Ketik HAPUS AKUN untuk konfirmasi.',
        'password.current_password' => 'Password tidak sesuai.',
    ]);

    $user = $request->user();

    // Hapus foto profil dari storage jika ada
    if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
        Storage::disk('public')->delete($user->foto_profil);
    }

    Auth::logout();

    $user->delete();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return Redirect::to('/')->with('status', 'account-deleted');
}

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi!',
            'current_password.current_password' => 'Password saat ini tidak sesuai.',
            'password.required' => 'Password baru wajib diisi!',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password.min' => 'Password baru minimal harus 8 karakter.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('status', 'password-updated');
    }
}