<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Sistem Kasir RKZ</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .autocomplete-suggestions {
            position: absolute;
            z-index: 1000;
            background: white;
            border: 1px solid #ccc;
            max-height: 200px;
            overflow-y: auto;
            width: 100%;
        }
        .autocomplete-suggestion {
            padding: 10px;
            cursor: pointer;
        }
        .autocomplete-suggestion:hover {
            background-color: #f0f0f0;
        }
        .relative-col { position: relative; }
        
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12pt; }
        }
    </style>
</head>
<body>
    <div class="container mt-4 no-print" id="kasir-view">
        <h2 class="mb-4">Kasir - RKZ Surabaya Hospital</h2>
        
        <div class="row mb-3">
            <div class="col-md-8"></div>
            <div class="col-md-4">
                <label>Nama Pasien</label>
                <input type="text" id="nama_pasien" class="form-control mb-2" placeholder="Masukkan nama pasien...">
                
                <label>Tanggal Transaksi</label>
                <input type="date" id="tanggal" class="form-control mb-2" readonly>
            </div>
        </div>

        <table class="table table-bordered" id="table-items">
            <thead class="table-dark">
                <tr>
                    <th>Nama Barang</th>
                    <th>Harga</th>
                    <th>Jumlah</th>
                    <th>Subtotal</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tbody-items">
                <!-- Baris item dinamis -->
            </tbody>
        </table>
        
        <button class="btn btn-primary mb-3" onclick="tambahBaris()">+ Tambah Item</button>

        <div class="row">
            <div class="col-md-8"></div>
            <div class="col-md-4">
                <h3 class="text-end">Total: Rp <span id="grand_total">0</span></h3>
                <button class="btn btn-success w-100 mt-2" onclick="simpanTransaksi()">Simpan & Cetak</button>
            </div>
        </div>
    </div>

    <!-- Tampilan Khusus Cetak -->
    <div class="d-none d-print-block" id="print-view">
        <div class="text-center mb-4">
            <h2>RKZ SURABAYA HOSPITAL</h2>
            <p>Jl. Diponegoro No.51, Surabaya<br>Telp: (031) 5677562</p>
            <hr>
        </div>
        
        <div class="row mb-3">
            <div class="col-6">
                <strong>No. Kwitansi: </strong> <span id="print_no_kwitansi"></span><br>
                <strong>Tanggal: </strong> <span id="print_tanggal"></span>
            </div>
            <div class="col-6 text-end">
                <strong>Nama Pasien: </strong> <span id="print_nama_pasien"></span>
            </div>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nama Barang</th>
                    <th>Harga Satuan</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody id="print_tbody">
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="3" class="text-end">Grand Total</th>
                    <th id="print_grand_total"></th>
                </tr>
            </tfoot>
        </table>

        <div class="row mt-5">
            <div class="col-8"></div>
            <div class="col-4 text-center">
                <p>Hormat Kami,</p>
                <br><br><br>
                <p><strong>Petugas Kasir</strong></p>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('tanggal').valueAsDate = new Date();

        function tambahBaris() {
            const tbody = document.getElementById('tbody-items');
            const rowId = 'row_' + Date.now();
            
            const tr = document.createElement('tr');
            tr.id = rowId;
            tr.innerHTML = `
                <td class="relative-col">
                    <input type="text" class="form-control input-barang" placeholder="Ketik nama barang..." autocomplete="off">
                    <input type="hidden" class="input-id-barang">
                    <div class="autocomplete-suggestions d-none"></div>
                </td>
                <td>
                    <input type="number" class="form-control input-harga" readonly value="0">
                </td>
                <td>
                    <input type="number" class="form-control input-jumlah" min="1" value="1" onchange="hitungSubtotal('${rowId}')">
                </td>
                <td>
                    <input type="number" class="form-control input-subtotal" readonly value="0">
                </td>
                <td>
                    <button class="btn btn-danger btn-sm" onclick="hapusBaris('${rowId}')">Hapus</button>
                </td>
            `;
            tbody.appendChild(tr);
            
            const inputBarang = tr.querySelector('.input-barang');
            inputBarang.addEventListener('input', debounce(function(e) {
                cariBarang(e.target, tr);
            }, 300));
        }

        function hapusBaris(rowId) {
            document.getElementById(rowId).remove();
            hitungGrandTotal();
        }

        function hitungSubtotal(rowId) {
            const row = document.getElementById(rowId);
            const harga = parseInt(row.querySelector('.input-harga').value) || 0;
            const jumlah = parseInt(row.querySelector('.input-jumlah').value) || 0;
            row.querySelector('.input-subtotal').value = harga * jumlah;
            hitungGrandTotal();
        }

        function hitungGrandTotal() {
            let total = 0;
            document.querySelectorAll('.input-subtotal').forEach(input => {
                total += parseInt(input.value) || 0;
            });
            document.getElementById('grand_total').innerText = total.toLocaleString('id-ID');
        }

        function debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        function cariBarang(input, row) {
            const keyword = input.value;
            const suggBox = row.querySelector('.autocomplete-suggestions');
            
            if (keyword.length < 2) {
                suggBox.classList.add('d-none');
                return;
            }

            fetch('api_cari_barang.php?keyword=' + encodeURIComponent(keyword))
                .then(res => res.json())
                .then(data => {
                    if(data.status === 'success') {
                        suggBox.innerHTML = '';
                        if(data.data.length > 0) {
                            data.data.forEach(item => {
                                const div = document.createElement('div');
                                div.className = 'autocomplete-suggestion';
                                div.innerText = `${item.nama_barang} - Rp ${parseInt(item.harga).toLocaleString('id-ID')} (Stok: ${item.stok})`;
                                div.onclick = () => pilihBarang(row, item);
                                suggBox.appendChild(div);
                            });
                            suggBox.classList.remove('d-none');
                        } else {
                            suggBox.classList.add('d-none');
                        }
                    }
                });
        }

        function pilihBarang(row, item) {
            row.querySelector('.input-id-barang').value = item.id_barang;
            row.querySelector('.input-barang').value = item.nama_barang;
            row.querySelector('.input-harga').value = item.harga;
            row.querySelector('.autocomplete-suggestions').classList.add('d-none');
            hitungSubtotal(row.id);
        }

        function simpanTransaksi() {
            const nama_pasien = document.getElementById('nama_pasien').value;
            if(!nama_pasien) {
                alert("Nama pasien harus diisi!");
                return;
            }

            const items = [];
            const rows = document.querySelectorAll('#tbody-items tr');
            
            let adaError = false;
            rows.forEach(row => {
                const id_barang = row.querySelector('.input-id-barang').value;
                const jumlah = row.querySelector('.input-jumlah').value;
                const harga = row.querySelector('.input-harga').value;
                
                if(!id_barang) {
                    adaError = true;
                } else {
                    items.push({
                        id_barang: parseInt(id_barang),
                        jumlah: parseInt(jumlah),
                        harga: parseInt(harga)
                    });
                }
            });

            if(adaError || items.length === 0) {
                alert("Harap lengkapi data barang terlebih dahulu!");
                return;
            }

            const payload = {
                nama_pasien: nama_pasien,
                items: items
            };

            fetch('api_simpan_kwitansi.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    siapkanCetak(data.data.no_kwitansi, nama_pasien, items);
                    window.print();
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    alert("Error: " + data.message);
                }
            })
            .catch(err => alert("Terjadi kesalahan jaringan."));
        }

        function siapkanCetak(no_kwitansi, nama_pasien, items) {
            document.getElementById('print_no_kwitansi').innerText = no_kwitansi;
            document.getElementById('print_tanggal').innerText = new Date().toLocaleDateString('id-ID');
            document.getElementById('print_nama_pasien').innerText = nama_pasien;
            
            const tbody = document.getElementById('print_tbody');
            tbody.innerHTML = '';
            
            const rows = document.querySelectorAll('#tbody-items tr');
            
            let total = 0;
            rows.forEach(row => {
                const nama = row.querySelector('.input-barang').value;
                const harga = parseInt(row.querySelector('.input-harga').value);
                const jumlah = parseInt(row.querySelector('.input-jumlah').value);
                const sub = harga * jumlah;
                total += sub;
                
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${nama}</td>
                    <td>Rp ${harga.toLocaleString('id-ID')}</td>
                    <td>${jumlah}</td>
                    <td>Rp ${sub.toLocaleString('id-ID')}</td>
                `;
                tbody.appendChild(tr);
            });
            
            document.getElementById('print_grand_total').innerText = 'Rp ' + total.toLocaleString('id-ID');
        }

        tambahBaris();
    </script>
</body>
</html>
