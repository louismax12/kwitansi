<?php require_once 'views/layout/header.php'; ?>

<div class="row">
    <!-- Kolom Register / Keranjang -->
    <div class="col-md-8">
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Pencarian Barang</h5>
            </div>
            <div class="card-body">
                <div class="position-relative">
                    <input type="text" id="search_item" class="form-control form-control-lg" placeholder="Mulai ketik nama atau scan barcode barang...">
                    <div id="autocomplete_results" class="autocomplete-suggestions" style="display:none;"></div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-striped mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Barang</th>
                            <th width="15%">Harga</th>
                            <th width="15%">Qty</th>
                            <th width="20%">Subtotal</th>
                            <th width="5%"></th>
                        </tr>
                    </thead>
                    <tbody id="cart_table_body">
                        <!-- Keranjang Kosong -->
                        <tr id="empty_cart"><td colspan="5" class="text-center text-muted py-4">Keranjang kosong.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Kolom Checkout / Summary -->
    <div class="col-md-4">
        <div class="card shadow-sm bg-light">
            <div class="card-body">
                <h5 class="card-title mb-4">Summary Transaksi</h5>
                
                <div class="mb-3">
                    <label class="form-label text-muted small">Pelanggan / Pasien</label>
                    <input type="text" class="form-control" id="nama_pasien" placeholder="Masukkan nama..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small">No. Faktur</label>
                    <input type="text" class="form-control" id="no_faktur" placeholder="Masukkan No Faktur..." required>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small">Untuk Pembayaran</label>
                    <textarea class="form-control" id="untuk_pembayaran" rows="2" placeholder="Cth: Pembayaran Obat Rawat Jalan..." required></textarea>
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-2">
                    <span class="fs-5">Total</span>
                    <span class="fs-3 fw-bold text-success">Rp <span id="grand_total_display">0</span></span>
                </div>

                <div class="mb-4">
                    <label class="form-label text-muted small">Uang Bayar</label>
                    <input type="number" class="form-control form-control-lg text-end" id="uang_bayar" placeholder="0">
                </div>
                
                <div class="d-flex justify-content-between mb-4">
                    <span class="fs-6 text-muted">Kembalian</span>
                    <span class="fs-4 fw-bold text-danger">Rp <span id="kembalian_display">0</span></span>
                </div>

                <button class="btn btn-success btn-lg w-100 mb-2" id="btn_bayar" disabled>BAYAR & CETAK</button>
                <button class="btn btn-outline-secondary w-100" id="btn_batal">BATAL TRANSAKSI</button>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = [];

    const searchInput = document.getElementById('search_item');
    const autoResults = document.getElementById('autocomplete_results');
    const tbody = document.getElementById('cart_table_body');
    const emptyCart = document.getElementById('empty_cart');
    const grandTotalDisp = document.getElementById('grand_total_display');
    const uangBayar = document.getElementById('uang_bayar');
    const kembalianDisp = document.getElementById('kembalian_display');
    const btnBayar = document.getElementById('btn_bayar');
    const namaPasien = document.getElementById('nama_pasien');
    const noFaktur = document.getElementById('no_faktur');
    const untukPembayaran = document.getElementById('untuk_pembayaran');

    // Terbilang JS (simplified)
    function terbilang(n) {
        let angka = ["", "Satu", "Dua", "Tiga", "Empat", "Lima", "Enam", "Tujuh", "Delapan", "Sembilan", "Sepuluh", "Sebelas"];
        let res = "";
        if(n < 12) res = angka[n];
        else if(n < 20) res = terbilang(n - 10) + " belas";
        else if(n < 100) res = terbilang(Math.floor(n/10)) + " puluh " + terbilang(n % 10);
        else if(n < 200) res = "Seratus " + terbilang(n - 100);
        else if(n < 1000) res = terbilang(Math.floor(n/100)) + " ratus " + terbilang(n % 100);
        else if(n < 2000) res = "Seribu " + terbilang(n - 1000);
        else if(n < 1000000) res = terbilang(Math.floor(n/1000)) + " ribu " + terbilang(n % 1000);
        else if(n < 1000000000) res = terbilang(Math.floor(n/1000000)) + " juta " + terbilang(n % 1000000);
        return res.trim();
    }

    // Pencarian Barang (OSPOS Style)
    searchInput.addEventListener('input', function() {
        let val = this.value;
        if(val.length < 2) { autoResults.style.display = 'none'; return; }

        fetch('index.php?c=sales&a=search_item&keyword=' + encodeURIComponent(val))
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success' && data.data.length > 0) {
                let html = '';
                data.data.forEach(item => {
                    html += `<div class="autocomplete-suggestion" onclick="addToCart(${item.id_barang}, '${item.nama_barang}', ${item.harga}, ${item.stok})">
                        ${item.nama_barang} (Stok: ${item.stok}) - Rp ${item.harga.toLocaleString('id-ID')}
                    </div>`;
                });
                autoResults.innerHTML = html;
                autoResults.style.display = 'block';
            } else {
                autoResults.style.display = 'none';
            }
        });
    });

    // Menambah item ke keranjang
    window.addToCart = function(id, nama, harga, stok) {
        if(stok <= 0) { alert('Stok Habis!'); return; }
        
        let existing = cart.find(x => x.id_barang === id);
        if(existing) {
            if(existing.jumlah < stok) existing.jumlah++;
            else alert('Melebihi batas stok!');
        } else {
            cart.push({ id_barang: id, nama_barang: nama, harga: harga, jumlah: 1, max_stok: stok });
        }
        
        searchInput.value = '';
        autoResults.style.display = 'none';
        renderCart();
    };

    // Render ulang UI Keranjang
    function renderCart() {
        if(cart.length === 0) {
            tbody.innerHTML = '<tr id="empty_cart"><td colspan="5" class="text-center text-muted py-4">Keranjang kosong.</td></tr>';
            grandTotalDisp.innerText = '0';
            updateBayarState();
            return;
        }

        let html = '';
        let total = 0;
        cart.forEach((item, index) => {
            let subtotal = item.harga * item.jumlah;
            total += subtotal;
            html += `<tr>
                <td>${item.nama_barang}</td>
                <td>Rp ${item.harga.toLocaleString('id-ID')}</td>
                <td><input type="number" class="form-control form-control-sm" value="${item.jumlah}" min="1" max="${item.max_stok}" onchange="updateQty(${index}, this.value)"></td>
                <td>Rp ${subtotal.toLocaleString('id-ID')}</td>
                <td><button class="btn btn-sm btn-danger" onclick="removeItem(${index})">X</button></td>
            </tr>`;
        });
        tbody.innerHTML = html;
        grandTotalDisp.innerText = total.toLocaleString('id-ID');
        updateBayarState();
    }

    window.updateQty = function(index, val) {
        let n = parseInt(val);
        if(n > 0 && n <= cart[index].max_stok) {
            cart[index].jumlah = n;
        }
        renderCart();
    };

    window.removeItem = function(index) {
        cart.splice(index, 1);
        renderCart();
    };

    // Logika Pembayaran
    function updateBayarState() {
        let total = parseInt(grandTotalDisp.innerText.replace(/\./g, '')) || 0;
        let bayar = parseInt(uangBayar.value) || 0;
        let pasien = namaPasien.value.trim();
        let faktur = noFaktur.value.trim();
        
        if(cart.length > 0 && bayar >= total && pasien !== '' && faktur !== '') {
            btnBayar.disabled = false;
            kembalianDisp.innerText = (bayar - total).toLocaleString('id-ID');
        } else {
            btnBayar.disabled = true;
            kembalianDisp.innerText = '0';
        }
    }

    uangBayar.addEventListener('input', updateBayarState);
    namaPasien.addEventListener('input', updateBayarState);
    noFaktur.addEventListener('input', updateBayarState);

    // Proses Transaksi
    btnBayar.addEventListener('click', function() {
        let payload = {
            nama_pasien: namaPasien.value,
            no_faktur: noFaktur.value,
            untuk_pembayaran: untukPembayaran.value,
            items: cart
        };

        fetch('index.php?c=sales&a=save', {
            method: 'POST',
            body: JSON.stringify(payload),
            headers: { 'Content-Type': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === 'success') {
                cetakStruk(data.no_kwitansi, payload, parseInt(grandTotalDisp.innerText.replace(/\./g, '')));
                cart = [];
                namaPasien.value = '';
                noFaktur.value = '';
                untukPembayaran.value = '';
                uangBayar.value = '';
                renderCart();
            } else {
                alert('Gagal: ' + data.message);
            }
        });
    });

    // Simulasi Cetak Pre-printed 
    function cetakStruk(no_kwitansi, payload, total) {
        document.getElementById('print_no_kwitansi').innerText = no_kwitansi;
        document.getElementById('print_no_faktur').innerText = payload.no_faktur;
        
        let tgl = new Date();
        let formattedTgl = tgl.getDate() + ' ' + tgl.toLocaleString('default', { month: 'long' }) + ' ' + tgl.getFullYear();
        document.getElementById('print_tanggal').innerText = formattedTgl;
        
        document.getElementById('print_terima_dari').innerText = payload.nama_pasien;
        document.getElementById('print_uang_sejumlah').innerText = "= " + terbilang(total) + " Rupiah =";
        document.getElementById('print_untuk_pembayaran').innerText = payload.untuk_pembayaran;
        
        document.getElementById('print_grand_total').innerText = total.toLocaleString('id-ID') + ",-";
        
        window.print();
    }

    document.getElementById('btn_batal').addEventListener('click', function() {
        if(confirm('Batalkan transaksi ini?')) {
            cart = [];
            namaPasien.value = '';
            uangBayar.value = '';
            renderCart();
        }
    });

</script>

<?php require_once 'views/layout/footer.php'; ?>
