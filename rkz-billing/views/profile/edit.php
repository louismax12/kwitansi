<?php
// views/profile/edit.php
$page_title = "Edit Profile";
?>

<div class="row justify-content-center">
    <div class="col-md-6 mt-4">
        <div class="card shadow-sm border-0 rounded-lg">
            <div class="card-header bg-white pb-0 pt-4 border-0">
                <h4 class="font-weight-bold text-primary"><i class="fas fa-user-edit mr-2"></i> Pengaturan Profil</h4>
                <p class="text-muted small">Perbarui username atau password Anda di sini.</p>
            </div>
            <div class="card-body px-4 pb-4">
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger" style="border-radius: 8px;">
                        <i class="fas fa-exclamation-circle mr-2"></i> <?= htmlspecialchars($error) ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="alert alert-success" style="border-radius: 8px;">
                        <i class="fas fa-check-circle mr-2"></i> <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?c=profile&a=edit">
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-secondary">Username</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-user text-muted"></i></span>
                            </div>
                            <input type="text" class="form-control border-left-0 pl-0" name="username" value="<?= htmlspecialchars($user['username']) ?>" required style="background-color: #fafafa;">
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-secondary">Password Baru</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0"><i class="fas fa-lock text-muted"></i></span>
                            </div>
                            <input type="password" class="form-control border-left-0 pl-0" name="password" placeholder="Kosongkan jika tidak ingin mengubah password" style="background-color: #fafafa;">
                        </div>
                        <small class="form-text text-muted mt-2"><i class="fas fa-info-circle"></i> Jika password tidak ingin diubah, biarkan kolom ini kosong.</small>
                    </div>

                    <div class="form-group mb-0 text-right">
                        <a href="index.php?c=dashboard" class="btn btn-light px-4 mr-2" style="border-radius: 8px;">Batal</a>
                        <button type="submit" class="btn btn-primary px-4" style="border-radius: 8px;"><i class="fas fa-save mr-2"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
