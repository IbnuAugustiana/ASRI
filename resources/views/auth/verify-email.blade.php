<x-auth-shell title="Verifikasi email" heading="Cek kotak masukmu." lead="Kami sudah mengirim tautan verifikasi ke email yang kamu daftarkan.">
    <h1 class="auth__title">Verifikasi email</h1>
    <p class="auth__sub">Klik tautan di email untuk mengaktifkan akun. Tidak menerima email? Kirim ulang di bawah.</p>

    @if (session('status') == 'verification-link-sent')
        <div class="alert alert--ok" role="status"><i class="ri-mail-check-line" aria-hidden="true"></i> Tautan verifikasi baru sudah dikirim.</div>
    @endif

    <form method="POST" action="{{ route('verification.send') }}" class="form">
        @csrf
        <button type="submit" class="btn btn--leaf btn--lg btn--block"><i class="ri-mail-send-line" aria-hidden="true"></i> Kirim ulang email verifikasi</button>
    </form>
    <form method="POST" action="{{ route('logout') }}" class="form">
        @csrf
        <button type="submit" class="btn btn--outline btn--block">Keluar</button>
    </form>
</x-auth-shell>
