<x-auth-shell title="Masuk" heading="Selamat datang kembali di ASRI." lead="Masuk untuk melihat status pengajuan, tagihan, dan laporan kerusakan unitmu.">
    <h1 class="auth__title">Masuk</h1>
    <p class="auth__sub">Belum punya akun? <a href="{{ route('register') }}">Daftar sebagai calon penghuni</a></p>

    @if (session('status'))
        <div class="alert alert--ok" role="status"><i class="ri-checkbox-circle-line" aria-hidden="true"></i> {{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert--err" role="alert"><i class="ri-error-warning-line" aria-hidden="true"></i> {{ $errors->first() }}</div>
    @endif

    @if (count(config('portfolio.demo_accounts')))
        <div class="demo">
            <p class="demo__label">Coba langsung dengan akun demo</p>
            <div class="demo__list">
                @foreach (config('portfolio.demo_accounts') as $demo)
                    <button type="button" class="demo__btn" data-demo-email="{{ $demo['email'] }}" data-demo-password="{{ $demo['password'] }}">
                        <i class="{{ $demo['icon'] }}" aria-hidden="true"></i> {{ $demo['label'] }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="form" novalidate>
        @csrf
        <div class="form__group">
            <label for="email" class="form__label">Email</label>
            <div class="form__control">
                <i class="ri-mail-line" aria-hidden="true"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus autocomplete="email" @error('email') aria-invalid="true" @enderror>
            </div>
            @error('email')<p class="form__error">{{ $message }}</p>@enderror
        </div>

        <div class="form__group">
            <div class="form__row">
                <label for="password" class="form__label">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="form__link">Lupa password?</a>
                @endif
            </div>
            <div class="form__control">
                <i class="ri-lock-line" aria-hidden="true"></i>
                <input id="password" type="password" name="password" placeholder="Password akunmu" required autocomplete="current-password">
                <button type="button" class="form__eye" data-pass-toggle aria-label="Tampilkan password"><i class="ri-eye-line" aria-hidden="true"></i></button>
            </div>
            @error('password')<p class="form__error">{{ $message }}</p>@enderror
        </div>

        <label class="check">
            <input type="checkbox" name="remember" @checked(old('remember'))>
            <span class="check__box" aria-hidden="true"><i class="ri-check-line"></i></span>
            Ingat saya di perangkat ini
        </label>

        <button type="submit" class="btn btn--leaf btn--lg btn--block" data-magnetic>
            <i class="ri-login-circle-line" aria-hidden="true"></i> Masuk
        </button>
    </form>
</x-auth-shell>
