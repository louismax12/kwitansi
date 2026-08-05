<div class="mb-4">
    <h2 class="font-weight-bold text-dark">Edit User</h2>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?c=users&a=edit&id=<?php echo urlencode($user['username']); ?>">
            <div class="form-group row">
                <label class="col-sm-3 col-form-label font-weight-bold">Username</label>
                <div class="col-sm-9">
                    <input type="text" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-3 col-form-label font-weight-bold">Password</label>
                <div class="col-sm-9">
                    <input type="text" name="password" class="form-control" value="<?php echo htmlspecialchars($user['password']); ?>" required>
                    <small class="form-text text-muted">Password ditampilkan untuk kemudahan pengeditan sesuai dengan format sebelumnya.</small>
                </div>
            </div>

            <div class="form-group row">
                <label class="col-sm-3 col-form-label font-weight-bold">Hak Akses</label>
                <div class="col-sm-9">
                    <div class="custom-control custom-checkbox mb-2">
                        <input type="checkbox" class="custom-control-input" id="m1Check" name="m1" value="1" <?php echo $user['m1'] ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="m1Check">M1 (Akses Admin / Manajemen User)</label>
                    </div>
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="m2Check" name="m2" value="1" <?php echo $user['m2'] ? 'checked' : ''; ?>>
                        <label class="custom-control-label" for="m2Check">M2 (Akses Tambahan)</label>
                    </div>
                </div>
            </div>

            <hr>
            <div class="form-group row mb-0">
                <div class="col-sm-9 offset-sm-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Update User</button>
                    <a href="index.php?c=users" class="btn btn-secondary ml-2">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
