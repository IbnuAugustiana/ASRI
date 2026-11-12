<x-auth-shell title="Buat password baru" heading="Satu langkah lagi." lead="Buat password baru, lalu masuk kembali ke akunmu.">
    <h1 class="auth__title">Password baru</h1>
    <p class="auth__sub">Gunakan minimal 8 karakter.</p>

    <form method="POST" action="{{ route('password.store') }}" class="form" novalidate>
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form__group">
            <label for="email" class="form__label">Email</label>
            <div class="form__control">
                <i class="ri-mail-line" aria-hidden="true"></i>
                <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autocomplete="username" @error('email') aria-invalid="true" @enderror>
            </div>
            @error('email')<p class="form__error">{{ $message }}</p>@enderror
        </div>
        <div class="form__group">
            <label for="password" class="form__label">Password baru</label>
            <div class="form__control">
                <i class="ri-lock-line" aria-hidden="true"></i>
                <input id="password" type="password" name="password" required autofocus autocomplete="new-password" @error('password') aria-invalid="true" @enderror>
                <button type="button" class="form__eye" data-pass-toggle aria-label="Tampilkan password"><i class="ri-eye-line" aria-hidden="true"></i></button>
            </div>
            @error('password')<p class="form__error">{{ $message }}</p>@enderror
        </div>
        <div class="form__group">
            <label for="password_confirmation" class="form__label">Ulangi password baru</label>
            <div class="form__control">
                <i class="ri-lock-2-line" aria-hidden="true"></i>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
            </div>
            @error('password_confirmation')<p class="form__error">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn btn--leaf btn--lg btn--block">Simpan password baru</button>
    </form>
</x-auth-shell>
