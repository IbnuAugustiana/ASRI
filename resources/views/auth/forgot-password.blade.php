<x-auth-shell title="Lupa password" heading="Kunci akunmu tertinggal?" lead="Kami kirim tautan untuk membuat password baru ke email akunmu.">
    <h1 class="auth__title">Lupa password</h1>
    <p class="auth__sub">Masukkan email yang terdaftar. Tautan reset berlaku 60 menit.</p>

    @if (session('status'))
        <div class="alert alert--ok" role="status"><i class="ri-mail-check-line" aria-hidden="true"></i> {{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="form" novalidate>
        @csrf
        <div class="form__group">
            <label for="email" class="form__label">Email</label>
            <div class="form__control">
                <i class="ri-mail-line" aria-hidden="true"></i>
                <input id="email" type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required autofocus autocomplete="email" @error('email') aria-invalid="true" @enderror>
            </div>
            @error('email')<p class="form__error">{{ $message }}</p>@enderror
        </div>
        <button type="submit" class="btn btn--leaf btn--lg btn--block" data-magnetic>
            <i class="ri-send-plane-line" aria-hidden="true"></i> Kirim tautan reset
        </button>
        <p class="form__fine"><a href="{{ route('login') }}">Kembali ke halaman masuk</a></p>
    </form>
</x-auth-shell>
