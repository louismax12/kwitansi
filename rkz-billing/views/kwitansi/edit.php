<?php
// views/kwitansi/edit.php
?>
<div class="pak-nino-ui">
    <div class="header-bar">
        <div class="hospital-info">
            <strong>RUMAH SAKIT KATOLIK</strong><br>
            St. Vincentius a Paulo<br>
            Jl. Diponegoro 51
        </div>
        <div class="kwitansi-title">EDIT KWITANSI</div>
        <div class="no-box">
            <div>No.Kwitansi : <strong style="color:#2d5a8e"><?= htmlspecialchars($kwitansi['no_kwitansi']) ?></strong></div>
            <div style="margin-top:4px">No.Faktur :&nbsp;
            <input type="text" id="hdr_faktur" value="<?= htmlspecialchars($kwitansi['no_faktur']) ?>" readonly>
            </div>
        </div>
    </div>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="index.php?c=kwitansi&a=edit&id=<?= urlencode($kwitansi['no_kwitansi']) ?>" id="frmKwt" onsubmit="event.preventDefault(); simpan();">
    <input type="hidden" name="jumlah" id="f_jumlah" value="<?= htmlspecialchars($kwitansi['total_bayar']) ?>">

    <div class="form-wrap">
        <div class="form-row">
            <label>Telah Terima Dari :</label>
            <input type="text" name="nama_pasien" id="f_dari" value="<?= isset($_POST['nama_pasien']) ? htmlspecialchars($_POST['nama_pasien']) : htmlspecialchars($kwitansi['nama_pasien']) ?>" required autofocus>
        </div>
        <div class="form-row">
            <label>Uang Sejumlah :</label>
            <input type="text" id="f_uang" value="" readonly style="background:#f4f4ec;color:#555">
        </div>
        <div class="form-row">
            <label>Untuk Pembayaran :</label>
            <input type="text" name="untuk_pembayaran" id="f_untuk" required value="<?= isset($_POST['untuk_pembayaran']) ? htmlspecialchars($_POST['untuk_pembayaran']) : htmlspecialchars($kwitansi['untuk_pembayaran']) ?>">
        </div>
        <div class="form-row">
            <label>Keterangan :</label>
            <input type="text" name="keterangan" id="f_ket" required value="<?= isset($_POST['keterangan']) ? htmlspecialchars($_POST['keterangan']) : htmlspecialchars($kwitansi['keterangan']) ?>">
        </div>
        <div class="form-row">
            <label></label>
            <input type="text" name="no_faktur" id="f_faktur" placeholder="No. Faktur" value="<?= isset($_POST['no_faktur']) ? htmlspecialchars($_POST['no_faktur']) : htmlspecialchars($kwitansi['no_faktur']) ?>" required oninput="this.value=this.value.toUpperCase().replace(/[^A-Z0-9]/g,'')">
            <label style="width:auto;margin-left:20px">Jumlah : Rp.</label>
            <input type="text" id="disp_jumlah"
                value="<?= number_format($kwitansi['total_bayar'], 0, ',', '.') ?>"
                style="width:160px;font-weight:700;color:#1a3d6e;background:#fff;border:1px solid #aaa;padding:4px 6px;border-radius:2px"
                placeholder="0"
                oninput="this.value=this.value.replace(/[^0-9.,]/g,'')">
            &nbsp;
            <?php 
                $hasDetails = false;
                if (!empty($existingItems)) {
                    if (count($existingItems) > 1 || $existingItems[0]['id_barang'] !== null) {
                        $hasDetails = true;
                    }
                }
            ?>
            <label><input type="checkbox" id="chkDetail" <?= $hasDetails ? 'checked' : '' ?>> Detail</label>
        </div>
    </div>

    <div class="detail-section <?= $hasDetails ? 'open' : '' ?>" id="detailBox">
        <strong style="color:#1a3d6e">Detail Pembayaran</strong>
        <table class="det-tbl" style="margin-top:8px">
            <thead>
                <tr>
                    <th style="width:40px">#</th>
                    <th style="width:130px">Kategori</th>
                    <th>Nama</th>
                    <th style="width:130px">Jumlah (Rp)</th>
                    <th style="width:50px"></th>
                </tr>
            </thead>
            <tbody id="detBody">
                <?php if (!empty($existingItems)): ?>
                    <?php foreach ($existingItems as $idx => $eItem): ?>
                    <tr>
                        <td><?= $idx + 1 ?></td>
                        <td>
                            <input type="hidden" name="det_kd[]" value="<?= htmlspecialchars($eItem['id_barang']) ?>">
                            <select onchange="updateKdBrg(this)" style="width:100%">
                                <option value="">- Pilih Kategori -</option>
                                <?php foreach ($barangList as $b): ?>
                                    <option value="<?= $b['id_barang'] ?>" <?= ($b['id_barang'] == $eItem['id_barang']) ? 'selected' : '' ?>><?= htmlspecialchars($b['nama_barang']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="text" name="det_nama[]" class="det-nama" placeholder="Nama detail (wajib)" value="<?= htmlspecialchars($eItem['nama_detail']) ?>" oninput="clearNamaError(this)"></td>
                        <td class="amt"><input type="text" name="det_jml[]" class="det-jml" value="<?= number_format($eItem['subtotal'], 0, '', '') ?>" style="text-align:right" onchange="recalc()"></td>
                        <td><button type="button" class="btn btn-delete" onclick="delRow(this)">X</button></td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td>1</td>
                        <td>
                            <input type="hidden" name="det_kd[]" value="">
                            <select onchange="updateKdBrg(this)" style="width:100%">
                                <option value="">- Pilih Kategori -</option>
                                <?php foreach ($barangList as $b): ?>
                                    <option value="<?= $b['id_barang'] ?>"><?= htmlspecialchars($b['nama_barang']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="text" name="det_nama[]" class="det-nama" placeholder="Nama detail (wajib)" value="" oninput="clearNamaError(this)"></td>
                        <td class="amt"><input type="text" name="det_jml[]" class="det-jml" value="0" style="text-align:right" onchange="recalc()"></td>
                        <td><button type="button" class="btn btn-delete" onclick="delRow(this)">X</button></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <div style="margin-top:8px;display:flex;gap:8px;align-items:center">
            <button type="button" class="btn btn-new" onclick="addRow()">+ Tambah Baris</button>
            <span style="font-size:12px;color:#666">Pilih Kategori agar tercatat di master barang</span>
        </div>
    </div>

    <div class="btn-bar">
        <button type="button" class="btn btn-new" onclick="window.location.reload()">Baru</button>
        <button type="button" class="btn btn-save" id="btnSimpan" onclick="simpan()" disabled>Simpan</button>
        <button type="button" class="btn btn-cancel" onclick="window.location.reload()">Cancel</button>
        <button type="button" class="btn btn-batal" onclick="window.location.href='index.php?c=kwitansi&a=index'">Batal</button>
    </div>
    </form>
</div>

<?php ob_start(); ?>
<style>
.pak-nino-ui { font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;font-size:13px;background:#e8e8e0;color:#222; padding: 20px; border-radius: 8px;}
.pak-nino-ui .header-bar{background:#f0f0e8;border-bottom:2px solid #2d5a8e;padding:10px 16px;display:flex;align-items:flex-start;gap:40px}
.pak-nino-ui .hospital-info{font-size:12px;line-height:1.7}
.pak-nino-ui .hospital-info strong{font-size:14px;color:#1a3d6e}
.pak-nino-ui .kwitansi-title{font-size:22px;font-weight:700;letter-spacing:2px;color:#1a3d6e;text-align:center;flex:1;align-self:center}
.pak-nino-ui .no-box{font-size:13px;color:#333}
.pak-nino-ui .no-box input{border:1px solid #aaa;padding:2px 6px;font-size:13px;width:160px;background:#fff}
.pak-nino-ui .form-wrap{background:#f8f8f0;border:1px solid #ccc;margin:10px 0;padding:14px;border-radius:4px}
.pak-nino-ui .form-row{display:flex;align-items:center;margin-bottom:8px;gap:8px}
.pak-nino-ui .form-row label{width:130px;text-align:right;color:#333;flex-shrink:0}
.pak-nino-ui .form-row input[type=text], .pak-nino-ui .form-row textarea{flex:1;border:1px solid #aaa;padding:4px 8px;font-size:13px;font-family:inherit;background:#fff;border-radius:2px}
.pak-nino-ui .form-row input:focus{outline:2px solid #2d5a8e;border-color:#2d5a8e}
.pak-nino-ui .btn{display:inline-flex;align-items:center;gap:5px;padding:5px 14px;font-size:13px;font-family:inherit;border:1px solid;border-radius:3px;cursor:pointer;font-weight:600;text-decoration:none}
.pak-nino-ui .btn-new   {background:#e8f0ff;border-color:#4a7cc7;color:#1a3d6e}
.pak-nino-ui .btn-save  {background:#d4edda;border-color:#28a745;color:#155724}
.pak-nino-ui .btn-cancel{background:#f8d7da;border-color:#dc3545;color:#721c24}
.pak-nino-ui .btn-batal {background:#f0e6ff;border-color:#7a4fc7;color:#3a1a6e}
.pak-nino-ui .btn-delete{background:#f8d7da;border-color:#dc3545;color:#721c24;padding:2px 8px;font-size:12px}
.pak-nino-ui .btn:hover{filter:brightness(.92)}
.pak-nino-ui .btn-bar{display:flex;gap:6px;flex-wrap:wrap;margin:10px 0}
.pak-nino-ui .detail-section{margin:10px 0;border:1px solid #aaa;background:#fff;padding:10px;border-radius:3px;display:none}
.pak-nino-ui .detail-section.open{display:block}
.pak-nino-ui .det-tbl{width:100%;border-collapse:collapse;font-size:12.5px}
.pak-nino-ui .det-tbl th{background:#4a7cc7;color:#fff;padding:4px 8px;text-align:left;}
.pak-nino-ui .det-tbl td{border:1px solid #ccc;padding:3px 6px}
.pak-nino-ui .det-tbl select, .pak-nino-ui .det-tbl input{font-size:12.5px;border:none;outline:none;width:100%;background:transparent}
.pak-nino-ui .det-tbl tr:hover td{background:#f0f7ff}
.pak-nino-ui .alert-error  {background:#f8d7da;border:1px solid #dc3545;color:#721c24; padding:8px 14px; margin-top:10px;}
</style>
<script>
const brgs = <?= json_encode($barangList) ?>;
function terbilang(n) {
    n = Math.abs(parseInt(n)) || 0;
    var satuan = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas', 'Dua belas', 'Tiga belas', 'Empat belas', 'Lima belas', 'Enam belas', 'Tujuh belas', 'Delapan belas', 'Sembilan belas'];
    var kata = ['', '', 'Dua puluh', 'Tiga puluh', 'Empat puluh', 'Lima puluh', 'Enam puluh', 'Tujuh puluh', 'Delapan puluh', 'Sembilan puluh'];
    if (n === 0) return 'Nol';
    if (n < 20) return satuan[n];
    if (n < 100) return kata[Math.floor(n / 10)] + (n % 10 ? ' ' + terbilang(n % 10) : '');
    if (n < 200) return 'Seratus' + (n % 100 ? ' ' + terbilang(n % 100) : '');
    if (n < 1000) return terbilang(Math.floor(n / 100)) + ' ratus' + (n % 100 ? ' ' + terbilang(n % 100) : '');
    if (n < 2000) return 'Seribu' + (n % 1000 ? ' ' + terbilang(n % 1000) : '');
    if (n < 1000000) return terbilang(Math.floor(n / 1000)) + ' ribu' + (n % 1000 ? ' ' + terbilang(n % 1000) : '');
    if (n < 1000000000) return terbilang(Math.floor(n / 1000000)) + ' juta' + (n % 1000000 ? ' ' + terbilang(n % 1000000) : '');
    return terbilang(Math.floor(n / 1000000000)) + ' miliar' + (n % 1000000000 ? ' ' + terbilang(n % 1000000000) : '');
}

function addRow() {
    const tbody = document.getElementById('detBody');
    const idx   = tbody.rows.length + 1;
    const opts  = brgs.map(b => `<option value="${b.id_barang}">${b.nama_barang}</option>`).join('');
    const tr    = document.createElement('tr');
    tr.innerHTML = `
      <td>${idx}</td>
      <td>
        <input type="hidden" name="det_kd[]" value="${brgs.length > 0 ? brgs[0].id_barang : ''}">
        <select onchange="updateKdBrg(this)" style="width:100%"><option value="">- Pilih Kategori -</option>${opts}</select>
      </td>
      <td><input type="text" name="det_nama[]" class="det-nama" placeholder="Nama detail (wajib)" value="" oninput="clearNamaError(this)"></td>
      <td class="amt"><input type="text" name="det_jml[]" class="det-jml" value="0" style="text-align:right" onchange="recalc()"></td>
      <td><button type="button" class="btn btn-delete" onclick="delRow(this)">X</button></td>`;
    tbody.appendChild(tr);
    tr.querySelector('.det-nama').focus();
}

function updateKdBrg(sel) {
    const row = sel.closest('tr');
    const kd  = sel.value;
    row.querySelector('input[name="det_kd[]"]').value = kd;
}

function delRow(btn) {
    btn.closest('tr').remove();
    recalc();
}

function recalc() {
    if (!document.getElementById('chkDetail').checked) return;
    let total = 0;
    document.querySelectorAll('.det-jml').forEach(inp => {
        total += parseFloat(inp.value.replace(/\./g,'').replace(',','.')) || 0;
    });
    setJumlah(total);
}

function setJumlah(total) {
    document.getElementById('f_jumlah').value    = total;
    document.getElementById('disp_jumlah').value = total.toLocaleString('id-ID');
    toggleSimpan();
    if(total > 0) {
        document.getElementById('f_uang').value = terbilang(total) + ' rupiah';
    } else {
        document.getElementById('f_uang').value = '';
    }
}

function toggleDetail(checked) {
    document.getElementById('detailBox').classList.toggle('open', checked);
    const inp = document.getElementById('disp_jumlah');
    if (checked) {
        inp.readOnly = true;
        inp.style.background = '#eef4ff';
        inp.style.cursor = 'default';
        recalc();
    } else {
        inp.readOnly = false;
        inp.style.background = '#fff';
        inp.style.cursor = 'text';
        inp.focus();
    }
    toggleSimpan();
}

document.getElementById('chkDetail')?.addEventListener('change', function() {
    toggleDetail(this.checked);
});

window.addEventListener('load', function() {
    const chk = document.getElementById('chkDetail');
    if (chk) {
        toggleDetail(chk.checked);
    }
    toggleSimpan();
});

document.getElementById('disp_jumlah')?.addEventListener('change', function() {
    if (document.getElementById('chkDetail').checked) return;
    const raw = parseFloat(this.value.replace(/\./g,'').replace(',','.')) || 0;
    this.value = raw.toLocaleString('id-ID');
    document.getElementById('f_jumlah').value = raw;
    toggleSimpan();
    if(raw > 0) {
        document.getElementById('f_uang').value = terbilang(raw) + ' rupiah';
    } else {
        document.getElementById('f_uang').value = '';
    }
});

function toggleSimpan() {
    const dari   = document.getElementById('f_dari').value.trim();
    const untuk  = document.getElementById('f_untuk').value.trim();
    const ket    = document.getElementById('f_ket').value.trim();
    const faktur = document.getElementById('f_faktur').value.trim();
    const raw    = document.getElementById('disp_jumlah').value.replace(/\./g,'').replace(',','.');
    const jml    = parseFloat(raw) || 0;
    const btn    = document.getElementById('btnSimpan');
    btn.disabled = !(dari && untuk && ket && faktur && jml > 0);
}

['f_dari','f_untuk','f_ket','f_faktur','disp_jumlah'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', toggleSimpan);
});

function simpan() {
    const dari  = document.getElementById('f_dari').value.trim();
    const untuk = document.getElementById('f_untuk').value.trim();
    const ket   = document.getElementById('f_ket').value.trim();
    const rawJml = document.getElementById('disp_jumlah').value.replace(/\./g,'').replace(',','.');
    const jml    = parseFloat(rawJml) || 0;

    const faktur = document.getElementById('f_faktur').value.trim();
    let errors = [];
    if (!dari)   errors.push('Telah Terima Dari wajib diisi');
    if (!untuk)  errors.push('Untuk Pembayaran wajib diisi');
    if (!ket)    errors.push('Keterangan wajib diisi');
    if (!faktur) errors.push('No. Faktur wajib diisi');
    else if (!/^[A-Z0-9]+$/.test(faktur)) errors.push('No. Faktur hanya boleh huruf BESAR dan angka');
    if (jml <= 0) errors.push('Jumlah harus lebih dari 0');

    highlight('f_dari',   !dari);
    highlight('f_untuk',  !untuk);
    highlight('f_ket',    !ket);
    highlight('f_faktur', !faktur);
    highlightJml(jml <= 0);

    if (document.getElementById('chkDetail').checked) {
        let namaErrors = false;
        document.querySelectorAll('.det-nama').forEach(inp => {
            if (inp.closest('tr') && inp.value.trim() === '') {
                inp.style.borderColor = '#dc3545';
                inp.style.background  = '#fff5f5';
                namaErrors = true;
            }
        });
        if (namaErrors) errors.push('Nama detail tidak boleh ada yang kosong');
    }

    if (errors.length > 0) {
        alert('Data belum lengkap:\n\n' + errors.join('\n'));
        return;
    }
    document.getElementById('f_jumlah').value = jml;
    document.getElementById('frmKwt').submit();
}

function highlight(id, isError) {
    const el = document.getElementById(id);
    if (!el) return;
    el.style.borderColor = isError ? '#dc3545' : '#aaa';
    el.style.background  = isError ? '#fff5f5' : '#fff';
}

function highlightJml(isError) {
    const el = document.getElementById('disp_jumlah');
    if (!el) return;
    el.style.borderColor = isError ? '#dc3545' : '#aaa';
    el.style.background  = isError ? '#fff5f5' : (document.getElementById('chkDetail').checked ? '#eef4ff' : '#fff');
}

['f_dari','f_untuk','f_ket','f_faktur'].forEach(id => {
    document.getElementById(id)?.addEventListener('input', () => highlight(id, false));
});
document.getElementById('disp_jumlah')?.addEventListener('input', () => highlightJml(false));

function clearNamaError(inp) {
    inp.style.borderColor = '';
    inp.style.background  = '';
}

const enterFields = ['f_dari', 'f_uang', 'f_untuk', 'f_ket', 'f_faktur', 'disp_jumlah'];
enterFields.forEach((id, i) => {
    document.getElementById(id)?.addEventListener('keydown', function(e) {
        if (e.key !== 'Enter') return;
        e.preventDefault();
        for (let j = i + 1; j < enterFields.length; j++) {
            const next = document.getElementById(enterFields[j]);
            if (next && !next.readOnly && next.offsetParent !== null) {
                next.focus();
                next.select();
                return;
            }
        }
        simpan();
    });
});

document.getElementById('f_faktur')?.addEventListener('input', function() {
    document.getElementById('hdr_faktur').value = this.value;
});
</script>
<?php $extra_js = ob_get_clean(); ?>
