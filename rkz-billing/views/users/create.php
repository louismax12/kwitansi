<div class="mb-4">
    <h2 class="font-weight-bold text-dark">Tambah User</h2>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="index.php?c=users&a=create">
            <div class="form-group row">
                <label class="col-sm-3 col-form-label font-weight-bold">Username</label>
                <div class="col-sm-9">
                    <input type="text" name="username" class="form-control" required autocomplete="off">
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-3 col-form-label font-weight-bold">Password</label>
                <div class="col-sm-9">
                    <input type="password" name="password" class="form-control" required>
                </div>
            </div>

            <!-- Hak akses m1 dan m2 dihapus sesuai permintaan -->

            <hr>
            <div class="form-group row mb-0">
                <div class="col-sm-9 offset-sm-3">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan User</button>
                    <a href="index.php?c=users" class="btn btn-secondary ml-2">Batal</a>
                </div>
            </div>
        </form>
    </div>
</div>
