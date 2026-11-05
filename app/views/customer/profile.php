<?php require APPROOT . '/views/templates/customer_header.php'; ?>

<!-- Toolbar -->
<div class="d-flex align-items-center gap-2 mb-4">
    <a href="<?php echo URLROOT; ?>/customer/dashboard" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
        <i class="fa-solid fa-arrow-left me-1"></i>Dashboard
    </a>
    <span class="small" style="color: #c9ded5;">/ Profil Pengguna</span>
</div>

<div class="row g-4">

    <!-- Profile Banner & Avatar -->
    <div class="col-12">
        <div class="sc-card overflow-hidden">
            <div class="sc-profile-banner"></div>
            <div class="sc-card-body pt-0 pb-4">
                <div class="d-flex align-items-end gap-3" style="margin-top:-34px;">
                    <div class="position-relative" style="flex-shrink:0;">
                        <div class="sc-avatar-lg shadow-sm" id="avatarDisplay" style="cursor: pointer;" title="Klik untuk memilih foto baru">
                            <?php if (!empty($data['user']->profile_image) && file_exists(APPROOT . '/../public/' . $data['user']->profile_image)) : ?>
                                <img src="<?php echo URLROOT; ?>/public/<?php echo htmlspecialchars($data['user']->profile_image); ?>?v=<?php echo time(); ?>" id="avatarImgPreview" alt="Profile" style="width:100%;height:100%;object-fit:cover;">
                                <span id="avatarInitial" style="display:none;"><?php echo strtoupper(substr($data['user']->name ?? $_SESSION['user_name'], 0, 1)); ?></span>
                            <?php else : ?>
                                <span id="avatarInitial"><?php echo strtoupper(substr($data['user']->name ?? $_SESSION['user_name'], 0, 1)); ?></span>
                                <img id="avatarImgPreview" src="" alt="Profile" style="width:100%;height:100%;object-fit:cover;display:none;">
                            <?php endif; ?>
                        </div>
                        <button type="button" class="btn position-absolute bottom-0 end-0 rounded-circle p-0 d-flex align-items-center justify-content-center sc-avatar-edit-badge" id="btnTriggerUpload" title="Ganti Foto Profil">
                            <i class="fa-solid fa-camera"></i>
                        </button>
                    </div>
                    <div class="pb-1">
                        <h3 class="mb-0 fw-bold text-white"><?php echo htmlspecialchars($data['user']->name ?? $_SESSION['user_name']); ?></h3>
                        <p class="mb-0 small" style="color: #94a3b8;"><?php echo htmlspecialchars($data['user']->email ?? $_SESSION['user_email']); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Profile Form -->
    <div class="col-md-7">
        <div class="sc-card">
            <div class="sc-card-header">
                <h5 class="mb-0 text-white"><i class="fa-solid fa-pen-to-square me-2" style="color: #dfc68b;"></i>Edit Profile</h5>
            </div>
            <div class="sc-card-body">

                <?php if (!empty($data['success'])) : ?>
                    <script>document.addEventListener('DOMContentLoaded', () => { if (window.showToast) window.showToast(<?php echo json_encode($data['success']); ?>, 'success'); });</script>
                <?php endif; ?>

                <?php if (!empty($data['avatar_err'])) : ?>
                    <script>document.addEventListener('DOMContentLoaded', () => { if (window.showToast) window.showToast(<?php echo json_encode($data['avatar_err']); ?>, 'error'); });</script>
                <?php endif; ?>

                <form action="<?php echo URLROOT; ?>/customer/profile" method="POST" enctype="multipart/form-data" id="profileForm">

                    <!-- Profile Image Upload Field -->
                    <div class="mb-4 p-3 rounded-3" style="background: rgba(255, 255, 255, 0.02); border: 1px dashed rgba(223, 198, 139, 0.28);">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="profile_image" class="form-label mb-0 fw-semibold" style="color: #dfc68b; font-size: 0.88rem;">
                                <i class="fa-solid fa-camera-retro me-1.5"></i>Ubah Foto Profil
                            </label>
                            <span class="small" style="color: #94a3b8; font-size: 0.72rem;">Maks. 5 MB (JPG, PNG, WebP)</span>
                        </div>
                        <input type="file" name="profile_image" id="profile_image"
                               class="form-control form-control-sm mt-2 <?php echo !empty($data['avatar_err']) ? 'is-invalid' : ''; ?>"
                               accept="image/jpeg,image/png,image/webp">
                        <div class="form-text mt-1.5" style="color: #94a3b8; font-size: 0.75rem;">
                            Pilih gambar dari perangkat Anda. Klik <em>Simpan Perubahan</em> di bawah untuk menerapkan foto baru.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="name" class="form-label">Display Name</label>
                        <input type="text" name="name" id="name"
                               class="form-control <?php echo !empty($data['name_err']) ? 'is-invalid' : ''; ?>"
                               value="<?php echo htmlspecialchars($data['user']->name ?? $_SESSION['user_name']); ?>"
                               placeholder="Nama lengkap Anda"
                               maxlength="100" required>
                        <?php if (!empty($data['name_err'])) : ?>
                            <div class="invalid-feedback"><?php echo htmlspecialchars($data['name_err']); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($_SESSION['user_email']); ?>" disabled readonly>
                        <div class="form-text">Alamat email terdaftar dan terikat pada akun Anda. Hubungi dukungan bila ingin mengubahnya.</div>
                    </div>

                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Simpan Perubahan
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Account Info -->
    <div class="col-md-5">
        <div class="sc-card">
            <div class="sc-card-header">
                <h5 class="mb-0 text-white"><i class="fa-solid fa-circle-info me-2" style="color: #dfc68b;"></i>Informasi Akun</h5>
            </div>
            <div class="sc-card-body">
                <ul class="list-unstyled mb-0">
                    <li class="d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                        <span class="small" style="color: #94a3b8; font-size: 0.88rem;">Peran</span>
                        <span class="badge" style="background: rgba(191,161,95,0.15); color: #dfc68b; border: 1px solid rgba(191,161,95,0.3); border-radius: 50px; font-size: 0.78rem; padding: 0.4rem 0.85rem;">Customer</span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center py-3" style="border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
                        <span class="small" style="color: #94a3b8; font-size: 0.88rem;">Bergabung Sejak</span>
                        <span class="fw-semibold text-white" style="font-size: 0.9rem;">
                            <?php echo $data['user'] && $data['user']->created_at ? date('d M Y', strtotime($data['user']->created_at)) : 'N/A'; ?>
                        </span>
                    </li>
                    <li class="d-flex justify-content-between align-items-center py-3">
                        <span class="small" style="color: #94a3b8; font-size: 0.88rem;">Status Akun</span>
                        <span class="badge sc-badge-secure px-3 py-1.5 rounded-pill" style="font-size: 0.78rem;">
                            <?php echo ucfirst($data['user']->status ?? 'Active'); ?>
                        </span>
                    </li>
                </ul>

                <div class="mt-4 pt-3" style="border-top: 1px solid rgba(255, 255, 255, 0.08);">
                    <a href="<?php echo URLROOT; ?>/auth/logout" class="btn btn-outline-danger btn-sm w-100 rounded-pill py-2">
                        <i class="fa-solid fa-arrow-right-from-bracket me-2"></i>Keluar Akun
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('profile_image');
    const btnTrigger = document.getElementById('btnTriggerUpload');
    const avatarDisplay = document.getElementById('avatarDisplay');
    const previewImg = document.getElementById('avatarImgPreview');
    const initialSpan = document.getElementById('avatarInitial');

    function openFilePicker() {
        if (fileInput) fileInput.click();
    }

    if (btnTrigger) btnTrigger.addEventListener('click', openFilePicker);
    if (avatarDisplay) avatarDisplay.addEventListener('click', openFilePicker);

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files && e.target.files[0];
            if (file) {
                if (file.size > 5 * 1024 * 1024) {
                    if (window.showToast) {
                        window.showToast('Ukuran file terlalu besar. Maksimal 5 MB.', 'warning');
                    } else {
                        alert('Ukuran file terlalu besar. Maksimal 5 MB.');
                    }
                    fileInput.value = '';
                    return;
                }
                const objectUrl = URL.createObjectURL(file);
                if (previewImg) {
                    previewImg.src = objectUrl;
                    previewImg.style.display = 'block';
                }
                if (initialSpan) {
                    initialSpan.style.display = 'none';
                }
            }
        });
    }
});
</script>

<?php require APPROOT . '/views/templates/customer_footer.php'; ?>
