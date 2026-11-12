@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<link href="https://unpkg.com/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
        .asri-profile-wrapper *:not(i) {
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        box-sizing: border-box;
    }
    .asri-profile-wrapper {
        background-color: #FDFDFB;
        min-height: 100vh;
        padding: 40px 5%;
        width: 100%;
    }
    .asri-card {
        border-radius: 20px;
        padding: 32px;
        border: 1px solid #e3e7f4;
    }
    .header-gradient {
        background: linear-gradient(135deg, #141a46 0%, #164d2d 100%);
        box-shadow: 0 12px 30px rgba(14, 56, 32, 0.12);
        margin-bottom: 32px;
        border: none;
    }
    .white-card {
        background: white;
        box-shadow: 0 4px 20px rgba(14, 56, 32, 0.02);
    }
    .avatar-container {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        border: 4px solid rgba(255, 255, 255, 0.2);
        overflow: hidden;
        background: #fff;
        transition: all 0.3s ease;
        position: relative;
    }
    .avatar-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .camera-btn {
        position: absolute;
        bottom: 0px;
        right: 0px;
        background: white;
        color: #141a46;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transition: all 0.2s ease;
        z-index: 2;
    }
    .camera-btn:hover { transform: scale(1.1); background: #f8fbf9; }
    .delete-avatar-btn {
        position: absolute;
        top: -4px;
        left: -4px;
        background: #ef4444;
        color: white;
        border: 2px solid #141a46;
        border-radius: 50%;
        width: 26px;
        height: 26px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
        z-index: 2;
    }
    .delete-avatar-btn:hover { transform: scale(1.1); background: #dc2626; }
    .badge {
        background: rgba(255, 255, 255, 0.12);
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 13px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        margin-top: 12px;
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .asri-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
        gap: 32px;
        width: 100%;
    }
    @media (max-width: 640px) {
        .asri-grid { grid-template-columns: 1fr; }
    }
    .form-title {
        margin: 0 0 24px 0;
        font-size: 18px;
        color: #141a46;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 12px;
    }
    .input-group { margin-bottom: 20px; }
    .input-group label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 8px; }
    .asri-input {
        width: 100%;
        padding: 12px 16px;
        border: 1.5px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.25s ease;
        background: #fdfdfd;
        font-size: 14px;
        color: #141a46;
    }
    .asri-input:focus {
        border-color: #164d2d;
        outline: none;
        background: white;
        box-shadow: 0 0 0 4px rgba(22, 77, 45, 0.06);
    }
    .asri-input.input-danger:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 4px rgba(220, 38, 38, 0.06);
    }
    .btn-primary {
        width: 100%;
        padding: 14px;
        background: #141a46;
        color: white;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-primary:hover { background: #164d2d; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(14,56,32,0.15); }
    .btn-secondary {
        width: 100%;
        padding: 14px;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        font-weight: 700;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .btn-secondary:hover { background: #e2e8f0; color: #141a46; }
    .asri-toast {
        position: fixed;
        top: 30px;
        right: 30px;
        background: white;
        padding: 16px 24px;
        border-radius: 14px;
        box-shadow: 0 12px 36px rgba(14,56,32,0.12);
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 9999;
        animation: slideIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        border-left: 6px solid #22c55e;
    }

    /* ========== CROPPER MODAL ========== */
    .modal-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(9, 28, 16, 0.8);
        backdrop-filter: blur(6px);
        z-index: 10000;
        align-items: center;
        justify-content: center;
    }
    .modal-overlay.open { display: flex; }
    .modal-content {
        background: white;
        padding: 32px;
        border-radius: 24px;
        width: 90%;
        max-width: 480px;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    }
    .crop-area {
        border-radius: 16px;
        overflow: hidden;
        background: #f8fafc;
        margin: 20px 0;
        max-height: 380px;
        width: 100%;
    }
    .modal-footer { display: flex; justify-content: flex-end; gap: 12px; margin-top: 24px; }
    .btn-save { background: #141a46; color: white; padding: 12px 24px; border-radius: 10px; border: none; font-weight: 600; font-size: 14px; cursor: pointer; transition: background 0.2s; }
    .btn-save:hover { background: #164d2d; }
    .btn-save:disabled { background: #cbd5e1; cursor: not-allowed; }
    .btn-cancel { background: #f1f5f9; color: #64748b; padding: 12px 24px; border-radius: 10px; border: none; font-weight: 600; font-size: 14px; cursor: pointer; }

    /* ========== KONFIRMASI MODAL ========== */
    .conf-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(9, 28, 16, 0.75);
        backdrop-filter: blur(5px);
        z-index: 11000;
        align-items: center;
        justify-content: center;
    }
    .conf-overlay.open { display: flex; }
    .conf-box {
        background: white;
        border-radius: 20px;
        width: 90%;
        max-width: 420px;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0,0,0,0.25);
        animation: popIn 0.22s cubic-bezier(0.34,1.56,0.64,1);
    }
    @keyframes popIn { from { transform: scale(0.88); opacity: 0; } to { transform: scale(1); opacity: 1; } }
    .conf-head { padding: 24px 28px 0; display: flex; align-items: flex-start; gap: 16px; }
    .conf-icon-wrap {
        width: 48px; height: 48px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .conf-icon-wrap.green { background: #E1F5EE; color: #0F6E56; }
    .conf-icon-wrap.red   { background: #FCEBEB; color: #A32D2D; }
    .conf-icon-wrap.amber { background: #FAEEDA; color: #854F0B; }
    .conf-title { font-size: 16px; font-weight: 700; color: #141a46; margin: 0 0 4px; }
    .conf-desc  { font-size: 13px; color: #64748b; margin: 0; line-height: 1.6; }
    .conf-body  { padding: 16px 28px 0; }
    .conf-warn-box {
        display: flex; align-items: flex-start; gap: 10px;
        background: #FAEEDA; border-radius: 10px; padding: 12px 14px; margin-top: 12px;
    }
    .conf-warn-box i { color: #854F0B; font-size: 16px; margin-top: 1px; flex-shrink: 0; }
    .conf-warn-box p { margin: 0; font-size: 12px; color: #633806; line-height: 1.6; }
    .conf-hint { font-size: 12px; color: #64748b; margin: 14px 0 6px; }
    .conf-hint strong { color: #141a46; }
    .conf-divider { height: 1px; background: #f1f5f9; margin: 16px 0 0; }
    .conf-foot { padding: 16px 28px 24px; display: flex; justify-content: flex-end; gap: 10px; }
    .btn-cf { padding: 11px 22px; border-radius: 10px; font-size: 14px; font-weight: 700; cursor: pointer; border: none; transition: all 0.15s; }
    .btn-cf-cancel { background: #f1f5f9; color: #64748b; }
    .btn-cf-cancel:hover { background: #e2e8f0; }
    .btn-cf-green  { background: #141a46; color: white; }
    .btn-cf-green:hover  { background: #164d2d; }
    .btn-cf-amber  { background: #854F0B; color: white; }
    .btn-cf-amber:hover  { background: #633806; }
    .btn-cf-red    { background: #a32d2d; color: white; }
    .btn-cf-red:hover    { background: #791f1f; }
    .btn-cf:disabled { background: #e2e8f0 !important; color: #94a3b8 !important; cursor: not-allowed; }

    /* Danger Zone Card */
    .danger-zone-card {
        background: white;
        border: 1.5px solid #fecaca;
        margin-top: 32px;
    }

    @keyframes slideIn { from { transform: translateX(120%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>

<div class="asri-profile-wrapper">
    <div style="width: 100%;">

        {{-- Toast Sukses --}}
        @if (session('status'))
        <div id="status-alert" class="asri-toast">
            <i class="ri-checkbox-circle-fill" style="color: #22c55e; font-size: 22px;"></i>
            <span style="font-size: 14px; font-weight: 600; color: #141a46;">
                @if(session('status') == 'profile-updated') Profil diperbarui! @endif
                @if(session('status') == 'password-updated') Password berhasil diganti! @endif
                @if(session('status') == 'avatar-deleted') Foto profil dihapus. @endif
            </span>
        </div>
        @endif

        {{-- Toast Error Password --}}
        @if ($errors->updatePassword->any())
        <div id="error-alert" class="asri-toast" style="border-left: 6px solid #ef4444;">
            <i class="ri-error-warning-fill" style="color: #ef4444; font-size: 22px;"></i>
            <div style="display: flex; flex-direction: column; gap: 2px;">
                @foreach ($errors->updatePassword->all() as $error)
                    <span style="font-size: 13px; font-weight: 600; color: #7f1d1d;">{{ $error }}</span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Toast Error Delete Account --}}
        @if ($errors->deleteAccount->any())
        <div id="error-alert-delete" class="asri-toast" style="border-left: 6px solid #ef4444;">
            <i class="ri-error-warning-fill" style="color: #ef4444; font-size: 22px;"></i>
            <div style="display: flex; flex-direction: column; gap: 2px;">
                @foreach ($errors->deleteAccount->all() as $error)
                    <span style="font-size: 13px; font-weight: 600; color: #7f1d1d;">{{ $error }}</span>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Header Card --}}
        <div class="asri-card header-gradient">
            <div style="display: flex; align-items: center; gap: 32px; flex-wrap: wrap;">
                <div style="position: relative;">
                    <div class="avatar-container">
                        <img id="avatar-main" 
                            src="{{ $user->foto_profil ? asset('storage/' . $user->foto_profil) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=E8F5ED&color=0e3820&bold=true' }}" 
                            class="avatar-img">
                    </div>

                    <label for="avatar-input" class="camera-btn" title="Ubah foto">
                        <i class="ri-camera-3-line" style="font-size: 18px;"></i>
                        <input type="file" id="avatar-input" hidden accept="image/*">
                    </label>

                    @if($user->foto_profil)
                    <form action="{{ route('profile.avatar.destroy') }}" method="POST" id="form-delete-avatar">
                        @csrf @method('DELETE')
                        <<button type="button" id="btn-delete-avatar" class="delete-avatar-btn" title="Hapus foto"
                        onclick="showConfirmModal('delete-avatar')">
                        <i class="ri-delete-bin-line" style="font-size: 13px;"></i>
                    </button>
                    </form>
                    @endif
                </div>

                <div style="color: white;">
                    <h2 style="margin: 0; font-size: 28px; font-weight: 700; letter-spacing: -0.5px;">{{ $user->name }}</h2>
                    <p style="opacity: 0.8; margin: 6px 0 0 0; font-size: 15px;">{{ $user->email }}</p>
                    <div class="badge">
                        <i class="ri-shield-user-line"></i>
                        {{ $user->role === 'penghuni' ? 'Penghuni' : 'Calon Penghuni' }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Grid Form --}}
        <div class="asri-grid">

            {{-- Informasi Akun --}}
            <div class="asri-card white-card">
                <h3 class="form-title"><i class="ri-user-settings-line"></i> Informasi Akun</h3>
                <form action="{{ route('profile.update') }}" method="POST" id="form-update-profile">
                    @csrf @method('PATCH')
                    <div class="input-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" class="asri-input" required>
                    </div>
                    <div class="input-group">
                        <label>Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" class="asri-input" required>
                    </div>
                    <div class="input-group">
                        <label>Nomor HP (WhatsApp)</label>
                        <input type="tel" name="no_hp" value="{{ old('no_hp', $user->no_hp) }}"
                            placeholder="Contoh: 08123456789" class="asri-input">
                    </div>
                    <div style="margin-top: 32px;">
                        <button type="button" class="btn-primary" onclick="showConfirmModal('edit-profile')">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

            {{-- Ganti Password --}}
            <div class="asri-card white-card">
                <h3 class="form-title"><i class="ri-lock-password-line"></i> Ganti Keamanan Password</h3>
                <form action="{{ route('profile.password.update') }}" method="POST" id="form-update-password">
                    @csrf @method('PUT')
                    <div class="input-group">
                        <label>Password Saat Ini</label>
                        <input type="password" name="current_password" placeholder="••••••••" class="asri-input" required>
                    </div>
                    <div class="input-group">
                        <label>Password Baru</label>
                        <input type="password" name="password" placeholder="Minimal 8 karakter" class="asri-input" required>
                    </div>
                    <div class="input-group">
                        <label>Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru" class="asri-input" required>
                    </div>
                    <div style="margin-top: 32px;">
                        <button type="button" class="btn-secondary" onclick="showConfirmModal('update-password')">
                            Update Kata Sandi
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Danger Zone --}}
        <div class="asri-card white-card danger-zone-card">
            <h3 class="form-title" style="color: #991b1b; border-bottom-color: #fee2e2;">
                <i class="ri-error-warning-line"></i> Zona Bahaya
            </h3>
            <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 16px;">
                <div>
                    <p style="font-size: 14px; font-weight: 600; color: #141a46; margin: 0 0 4px;">Hapus Akun Saya</p>
                    <p style="font-size: 13px; color: #64748b; margin: 0;">
                        Seluruh data, riwayat, dan akses akan dihapus secara permanen dan tidak bisa dipulihkan.
                    </p>
                </div>
                    <button type="button" id="btn-trigger-delete-account" onclick="showConfirmModal('delete-account')"
                style="padding: 12px 24px; background: white; color: #a32d2d; border: 1.5px solid #fca5a5;
                    border-radius: 12px; font-weight: 700; font-size: 14px; cursor: pointer;
                    transition: all 0.2s; white-space: nowrap;"
                onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='white'">
                <i class="ri-user-unfollow-line"></i> Hapus Akun
            </button>
                        
            </div>
        </div>

    </div>
</div>

{{-- ============================================================ --}}
{{-- CROPPER MODAL                                                --}}
{{-- ============================================================ --}}
<div id="cropper-modal" class="modal-overlay">
    <div class="modal-content">
        <h3 style="margin:0; font-size:18px; color:#141a46; font-weight:700; display:flex; align-items:center; gap:8px;">
            <i class="ri-crop-2-line" style="color:#164d2d"></i> Sesuaikan Foto Profil
        </h3>
        <p style="font-size:13px; color:#64748b; margin:6px 0 0 0;">Geser dan sesuaikan kotak tengah untuk memotong foto Anda.</p>
        <div class="crop-area">
            <img id="image-to-crop" style="max-width:100%; display:block;">
        </div>
        <div class="modal-footer">
            <button onclick="closeCropper()" class="btn-cancel">Batal</button>
            <button id="btn-save-crop" class="btn-save" onclick="confirmCropAndSave()">
                <i class="ri-save-3-line" id="icon-save"></i> <span id="save-text">Pasang Foto</span>
            </button>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: Simpan Perubahan Profil                               --}}
{{-- ============================================================ --}}
<div id="modal-edit-profile" class="conf-overlay" onclick="handleOverlayClick(event, 'edit-profile')">
    <div class="conf-box">
        <div class="conf-head">
            <div class="conf-icon-wrap green">
                <i class="ri-user-check-line" style="font-size:22px;"></i>
            </div>
            <div>
                <p class="conf-title">Simpan perubahan profil?</p>
                <p class="conf-desc">Nama lengkap, email, dan nomor HP akan diperbarui. Pastikan data yang diisi sudah benar.</p>
            </div>
        </div>
        <div class="conf-foot">
            <button class="btn-cf btn-cf-cancel" onclick="closeConfirmModal('edit-profile')">Batal</button>
            <button class="btn-cf btn-cf-green" onclick="submitConfirm('form-update-profile', 'edit-profile')">
                <i class="ri-check-line"></i> Ya, Simpan
            </button>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: Update Password                                       --}}
{{-- ============================================================ --}}
<div id="modal-update-password" class="conf-overlay" onclick="handleOverlayClick(event, 'update-password')">
    <div class="conf-box">
        <div class="conf-head">
            <div class="conf-icon-wrap green">
                <i class="ri-shield-keyhole-line" style="font-size:22px;"></i>
            </div>
            <div>
                <p class="conf-title">Perbarui password?</p>
                <p class="conf-desc">Setelah berhasil, gunakan password baru untuk login berikutnya. Session aktif Anda tidak akan terputus.</p>
            </div>
        </div>
        <div class="conf-foot">
            <button class="btn-cf btn-cf-cancel" onclick="closeConfirmModal('update-password')">Batal</button>
            <button class="btn-cf btn-cf-green" onclick="submitConfirm('form-update-password', 'update-password')">
                <i class="ri-lock-line"></i> Ya, Update
            </button>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: Hapus Foto Profil                                     --}}
{{-- ============================================================ --}}
<div id="modal-delete-avatar" class="conf-overlay" onclick="handleOverlayClick(event, 'delete-avatar')">
    <div class="conf-box">
        <div class="conf-head">
            <div class="conf-icon-wrap amber">
                <i class="ri-image-2-line" style="font-size:22px;"></i>
            </div>
            <div>
                <p class="conf-title">Hapus foto profil?</p>
                <p class="conf-desc">Foto profil Anda akan dihapus dan diganti dengan avatar inisial nama secara otomatis.</p>
            </div>
        </div>
        <div class="conf-foot">
            <button class="btn-cf btn-cf-cancel" onclick="closeConfirmModal('delete-avatar')">Batal</button>
            <button class="btn-cf btn-cf-amber" onclick="submitConfirm('form-delete-avatar', 'delete-avatar')">
                <i class="ri-delete-bin-line"></i> Ya, Hapus Foto
            </button>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- MODAL: Hapus Akun Permanen                                   --}}
{{-- ============================================================ --}}
<div id="modal-delete-account" class="conf-overlay" onclick="handleOverlayClick(event, 'delete-account')">
    <div class="conf-box">
        <div class="conf-head">
            <div class="conf-icon-wrap red">
                <i class="ri-alert-line" style="font-size:22px;"></i>
            </div>
            <div>
                <p class="conf-title">Hapus akun secara permanen?</p>
                <p class="conf-desc">Aksi ini tidak dapat dibatalkan. Seluruh data dan akses Anda akan dihapus selamanya.</p>
            </div>
        </div>
        <div class="conf-body">
            <div class="conf-warn-box">
                <i class="ri-information-line"></i>
                <p>Data yang dihapus meliputi: profil, riwayat, dokumen, dan semua hak akses. <strong>Tidak bisa dipulihkan.</strong></p>
            </div>
            <div class="conf-hint">Masukkan password Anda untuk konfirmasi:</div>
            <input type="password" id="delete-password-input" class="asri-input input-danger"
                placeholder="Password Anda" oninput="validateDeleteInput()">
            <div class="conf-hint" style="margin-top: 12px;">Ketik <strong>HAPUS AKUN</strong> untuk melanjutkan:</div>
            <input type="text" id="delete-confirm-input" class="asri-input input-danger"
                placeholder="HAPUS AKUN" oninput="validateDeleteInput()">
        </div>
        <div class="conf-divider"></div>
        <div class="conf-foot">
            <button class="btn-cf btn-cf-cancel" onclick="closeConfirmModal('delete-account')">Batal</button>
            <button class="btn-cf btn-cf-red" id="btn-delete-account" disabled onclick="submitDeleteAccount()">
                <i class="ri-delete-bin-2-line"></i> Hapus Permanen
            </button>
        </div>
    </div>
</div>

{{-- Form hidden hapus akun --}}
<form action="{{ route('profile.destroy') }}" method="POST" id="form-delete-account">
    @csrf @method('DELETE')
    <input type="hidden" name="confirm_text" id="hidden-confirm-text">
    <input type="hidden" name="password" id="hidden-password">
</form>

<script>
let cropper;
const avatarInput = document.getElementById('avatar-input');
const cropperModal = document.getElementById('cropper-modal');
const imageToCrop  = document.getElementById('image-to-crop');

avatarInput.addEventListener('change', function(e) {
    const files = e.target.files;
    if (files && files.length > 0) {
        const reader = new FileReader();
        reader.onload = function(event) {
            imageToCrop.src = event.target.result;
            cropperModal.classList.add('open');
            if (cropper) cropper.destroy();
            cropper = new Cropper(imageToCrop, {
                aspectRatio: NaN,
                viewMode: 1,
                dragMode: 'move',
                cropBoxMovable: true,
                cropBoxResizable: true,
                toggleDragModeOnDblclick: false,
                autoCropArea: 0.8
            });
        };
        reader.readAsDataURL(files[0]);
    }
});

function confirmCropAndSave() {
    const btn  = document.getElementById('btn-save-crop');
    const text = document.getElementById('save-text');
    const icon = document.getElementById('icon-save');
    btn.disabled = true;
    text.innerText = "Mengunggah...";
    icon.className = "ri-loader-4-line ri-spin";

    cropper.getCroppedCanvas().toBlob(function(blob) {
        const formData = new FormData();
        formData.append('avatar', blob, 'avatar.jpg');
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('_method', 'PATCH');

        fetch("{{ route('profile.avatar.update') }}", {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) { location.reload(); }
            else { alert('Gagal memperbarui foto profil.'); resetCropBtn(); }
        })
        .catch(() => { alert('Terjadi kesalahan jaringan.'); resetCropBtn(); });
    }, 'image/jpeg');
}

function resetCropBtn() {
    const btn  = document.getElementById('btn-save-crop');
    const text = document.getElementById('save-text');
    const icon = document.getElementById('icon-save');
    btn.disabled = false;
    text.innerText = "Pasang Foto";
    icon.className = "ri-save-3-line";
}

function closeCropper() {
    cropperModal.classList.remove('open');
    avatarInput.value = '';
}

/* ---- Konfirmasi Modal Helpers ---- */
function showConfirmModal(key) {
    // Validasi form profil sebelum buka modal
    if (key === 'edit-profile') {
        const form = document.getElementById('form-update-profile');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
    }

    // Validasi form password sebelum buka modal
    if (key === 'update-password') {
        const form = document.getElementById('form-update-password');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
    }

    document.getElementById('modal-' + key).classList.add('open');

    if (key === 'delete-account') {
        document.getElementById('delete-confirm-input').value = '';
        document.getElementById('delete-password-input').value = '';
        document.getElementById('btn-delete-account').disabled = true;
    }
}

function closeConfirmModal(key) {
    document.getElementById('modal-' + key).classList.remove('open');
}

function handleOverlayClick(e, key) {
    if (e.target === document.getElementById('modal-' + key)) closeConfirmModal(key);
}

function submitConfirm(formId, modalKey) {
    closeConfirmModal(modalKey);
    document.getElementById(formId).submit();
}

/* ---- Hapus Akun ---- */
function validateDeleteInput() {
    const text = document.getElementById('delete-confirm-input').value.trim();
    const pass = document.getElementById('delete-password-input').value;
    document.getElementById('btn-delete-account').disabled = (text !== 'HAPUS AKUN' || pass.length < 1);
}

function submitDeleteAccount() {
    document.getElementById('hidden-confirm-text').value = document.getElementById('delete-confirm-input').value;
    document.getElementById('hidden-password').value     = document.getElementById('delete-password-input').value;
    closeConfirmModal('delete-account');
    document.getElementById('form-delete-account').submit();
}

/* ---- Auto fade toast ---- */
setTimeout(() => {
    ['status-alert', 'error-alert', 'error-alert-delete'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.style.transition = 'opacity 0.5s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        }
    });
}, 4000);
</script>
@endsection