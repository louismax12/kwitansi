<?php require_once 'views/layout/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Master Pengguna</h3>
    <button class="btn btn-primary" onclick="openModal()">+ Tambah Pengguna</button>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <table class="table table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th>Username</th>
                    <th>Nama Petugas</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr>
                    <td><?php echo $u['username']; ?></td>
                    <td><?php echo $u['nama_petugas']; ?></td>
                    <td><span class="badge <?php echo $u['role'] == 'admin' ? 'bg-danger' : 'bg-primary'; ?>"><?php echo strtoupper($u['role']); ?></span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" onclick='editData(<?php echo json_encode($u); ?>)'>✏️ Edit</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="modalUser" class="modal" tabindex="-1" style="display: none; background: rgba(0,0,0,0.5); position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 1050;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="index.php?c=user&a=save">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Pengguna</h5>
                    <button type="button" class="btn-close" onclick="closeModal()"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_user" id="f_id">
                    <div class="mb-3">
                        <label>Username</label>
                        <input type="text" name="username" id="f_username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Nama Petugas</label>
                        <input type="text" name="nama_petugas" id="f_nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Role</label>
                        <select name="role" id="f_role" class="form-select">
                            <option value="kasir">Kasir</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" placeholder="Kosongi jika tidak ingin mengubah (saat edit)">
                        <small class="text-muted">Password akan dienkripsi MD5</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('modalTitle').innerText = 'Tambah Pengguna';
    document.getElementById('f_id').value = '';
    document.getElementById('f_username').value = '';
    document.getElementById('f_nama').value = '';
    document.getElementById('modalUser').style.display = 'block';
}
function closeModal() {
    document.getElementById('modalUser').style.display = 'none';
}
function editData(data) {
    document.getElementById('modalTitle').innerText = 'Edit Pengguna';
    document.getElementById('f_id').value = data.id_user;
    document.getElementById('f_username').value = data.username;
    document.getElementById('f_nama').value = data.nama_petugas;
    document.getElementById('f_role').value = data.role;
    document.getElementById('modalUser').style.display = 'block';
}
</script>

<?php require_once 'views/layout/footer.php'; ?>
