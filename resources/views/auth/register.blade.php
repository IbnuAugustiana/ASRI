<x-auth-shell title="Daftar" heading="Mulai cari unit rusunmu." lead="Akun calon penghuni dipakai untuk mengajukan sewa, mengunggah dokumen, dan memilih jadwal survei.">
    <h1 class="auth__title">Buat akun</h1>
    <p class="auth__sub">Sudah punya akun? <a href="{{ route('login') }}">Masuk di sini</a></p>

    @if ($errors->any())
        <div class="alert alert--err" role="alert">
            <i class="ri-error-warning-line" aria-hidden="true"></i>
            <span>Periksa lagi isian yang ditandai merah.</span>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="form" novalidate>
        @csrf
        <div class="form__group">
            <label for="name" class="form__label">Nama lengkap</label>
            <div class="form__control">
                <i class="ri-user-3-line" aria-hidden="true"></i>
                <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="Sesuai KTP" required autofocus autocomplete="name" @error('name') aria-invalid="true" @enderror>
            </div>
            @error('name')<p class="form__error">{{ $message }}</p>@enderror
        </div>

        <div class="form__grid">
            <div class="form__group">
                <label for="email" class="form__label">Email</label>
                <div class="form__control">
                    <i class="ri-mail-line" aria-hidden="true"></i>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autocomplete="email" @error('email') aria-invalid="true" @enderror>
                </div>
                @error('email')<p class="form__error">{{ $message }}</p>@enderror
            </div>
            <div class="form__group">
                <label for="no_hp" class="form__label">Nomor WhatsApp</label>
                <div class="form__control">
                    <i class="ri-whatsapp-line" aria-hidden="true"></i>
                    <input id="no_hp" type="tel" name="no_hp" value="{{ old('no_hp') }}" placeholder="081234567890" maxlength="15" required autocomplete="tel" @error('no_hp') aria-invalid="true" @enderror>
                </div>
                @error('no_hp')<p class="form__error">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="form__grid">
            <div class="form__group">
                <label for="password" class="form__label">Password</label>
                <div class="form__control">
                    <i class="ri-lock-line" aria-hidden="true"></i>
                    <input id="password" type="password" name="password" placeholder="Minimal 8 karakter" required autocomplete="new-password" @error('password') aria-invalid="true" @enderror>
                    <button type="button" class="form__eye" data-pass-toggle aria-label="Tampilkan password"><i class="ri-eye-line" aria-hidden="true"></i></button>
                </div>
                @error('password')<p class="form__error">{{ $message }}</p>@enderror
            </div>
            <div class="form__group">
                <label for="password_confirmation" class="form__label">Ulangi password</label>
                <div class="form__control">
                    <i class="ri-lock-2-line" aria-hidden="true"></i>
                    <input id="password_confirmation" type="password" name="password_confirmation" placeholder="Ketik ulang" required autocomplete="new-password">
                    <button type="button" class="form__eye" data-pass-toggle aria-label="Tampilkan password"><i class="ri-eye-line" aria-hidden="true"></i></button>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn--leaf btn--lg btn--block" data-magnetic>
            <i class="ri-user-add-line" aria-hidden="true"></i> Buat akun
        </button>
        <p class="form__fine">Dengan mendaftar, akunmu otomatis berperan sebagai calon penghuni.</p>
    </form>
</x-auth-shell>
