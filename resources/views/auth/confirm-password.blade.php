<x-auth-shell title="Konfirmasi password" heading="Area terlindungi." lead="Untuk keamanan, masukkan lagi password sebelum melanjutkan.">
    <h1 class="auth__title">Konfirmasi password</h1>
    <p class="auth__sub">Halaman ini berisi pengaturan sensitif.</p>

    <form method="POST" action="{{ route('password.confirm') }}" class="form" novalidate>
        @csrf
        <div class="form__group">
            <label for="password" class="form__label">Password</label>
            <div class="form__control">
                <i class="ri-lock-line" aria-hidden="true"></i>
                <input id="password" type="password" name="password" required autofocus autocomplete="current-password" @error('password') aria-invalid="true" @enderror>
                <button type="button" class="form__eye" data-pass-toggle aria-label="Tampilkan password"><i class="ri-eye-line" aria-hidden="true"></i></button>
            </div>
            @error('password')<p class="form__error">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn btn--leaf btn--lg btn--block">Lanjutkan</button>
    </form>
</x-auth-shell>
