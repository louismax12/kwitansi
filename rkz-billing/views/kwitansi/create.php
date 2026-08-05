<?php
// views/kwitansi/create.php
?>
<div class="card">
    <div class="card-header bg-white">
        <h4 class="mb-0">Buat Tagihan Baru (Invoice)</h4>
    </div>
    <div class="card-body">
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="index.php?c=kwitansi&a=create" method="POST" id="invoiceForm">
            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Pasien (Pelanggan) Terdaftar</label>
                    <select name="id_pelanggan" class="form-control" id="id_pelanggan">
                        <option value="">-- Pilih atau Ketik Nama Pasien Baru --</option>
                        <?php foreach ($pelangganList as $p): ?>
                            <option value="<?php echo $p['id_pelanggan']; ?>" data-nama="<?php echo htmlspecialchars($p['nama_pelanggan']); ?>">
                                <?php echo htmlspecialchars($p['nama_pelanggan']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <!-- <div class="col-md-6 form-group">
                    <label>Nama Pasien (Wajib)</label>
                    <input type="text" name="nama_pasien" id="nama_pasien" class="form-control" required>
                </div> -->
            </div>

            <hr>
            <h5>Item Tagihan (Tindakan / Obat)</h5>
            <table class="table table-bordered" id="itemsTable">
                <thead>
                    <tr class="bg-light">
                        <th>Barang / Tindakan</th>
                        <th width="150">Harga Satuan</th>
                        <th width="100">Jumlah</th>
                        <th width="150">Subtotal</th>
                        <th width="50">#</th>
                    </tr>
                </thead>
                <tbody id="itemsBody">
                    <tr class="item-row">
                        <td>
                            <select name="items[0][id_barang]" class="form-control item-select" required>
                                <option value="">-- Pilih atau Ketik Item --</option>
                                <?php foreach ($barangList as $b): ?>
                                    <option value="<?php echo $b['id_barang']; ?>" data-harga="<?php echo $b['harga']; ?>">
                                        <?php echo htmlspecialchars($b['nama_barang']); ?> (Stok: <?php echo $b['stok']; ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td><input type="number" name="items[0][harga_baru]" class="form-control item-price" readonly value="0"></td>
                        <td><input type="number" name="items[0][jumlah]" class="form-control item-qty" value="1" min="1" required></td>
                        <td><input type="number" class="form-control item-subtotal" readonly value="0"></td>
                        <td><button type="button" class="btn btn-danger btn-sm remove-row">&times;</button></td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-right font-weight-bold">Total Tagihan (Rp)</td>
                        <td colspan="2"><input type="text" id="grandTotal" class="form-control font-weight-bold" readonly value="0"></td>
                    </tr>
                </tfoot>
            </table>
            <button type="button" class="btn btn-secondary btn-sm mb-3" id="addRow">+ Tambah Item</button>

            <div class="text-right">
                <button type="submit" class="btn btn-primary btn-lg">Simpan Tagihan</button>
            </div>
        </form>
    </div>
</div>

<?php ob_start(); ?>
<script>
$(document).ready(function() {
    var rowIdx = 1;
    var barangOptions = `<?php foreach ($barangList as $b): ?><option value="<?php echo $b['id_barang']; ?>" data-harga="<?php echo $b['harga']; ?>"><?php echo htmlspecialchars($b['nama_barang']); ?> (Stok: <?php echo $b['stok']; ?>)</option><?php endforeach; ?>`;

    // Initialize Select2 for Pelanggan
    $('#id_pelanggan').select2({
        tags: true,
        width: '100%',
        placeholder: "-- Pilih atau Ketik Nama Pasien Baru --"
    }).on('select2:select', function(e) {
        var data = e.params.data;
        var isNew = data.element ? false : true;
        if(isNew) {
            $('#nama_pasien').val(data.text);
        } else {
            var nama = $(data.element).data('nama');
            $('#nama_pasien').val(nama);
        }
    });

    // Initialize Select2 for Items
    function initSelect2(element) {
        element.select2({
            tags: true,
            width: '100%',
            placeholder: "-- Pilih atau Ketik Item --"
        }).on('select2:select', function(e) {
            var isNew = e.params.data.element ? false : true;
            var row = $(this).closest('tr');
            var priceInput = row.find('.item-price');
            
            if (isNew) {
                // New tag, unlock price input
                priceInput.prop('readonly', false).val(0);
            } else {
                // Existing tag, lock price and fill from data
                var price = $(e.params.data.element).data('harga') || 0;
                priceInput.prop('readonly', true).val(price);
            }
            calculateSubtotal(row);
        });
    }

    // Initialize first row
    initSelect2($('.item-select'));

    // Add Row
    $('#addRow').click(function() {
        var html = `
            <tr class="item-row">
                <td>
                    <select name="items[` + rowIdx + `][id_barang]" class="form-control item-select" required>
                        <option value="">-- Pilih atau Ketik Item --</option>
                        ` + barangOptions + `
                    </select>
                </td>
                <td><input type="number" name="items[` + rowIdx + `][harga_baru]" class="form-control item-price" readonly value="0"></td>
                <td><input type="number" name="items[` + rowIdx + `][jumlah]" class="form-control item-qty" value="1" min="1" required></td>
                <td><input type="number" class="form-control item-subtotal" readonly value="0"></td>
                <td><button type="button" class="btn btn-danger btn-sm remove-row">&times;</button></td>
            </tr>`;
        $('#itemsBody').append(html);
        
        // Init Select2 on the new element
        initSelect2($('#itemsBody').find('.item-select').last());
        
        rowIdx++;
    });

    // Remove Row
    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        calculateTotal();
    });

    // On manual price change
    $(document).on('input', '.item-price', function() {
        calculateSubtotal($(this).closest('tr'));
    });

    // On qty change
    $(document).on('input', '.item-qty', function() {
        calculateSubtotal($(this).closest('tr'));
    });

    function calculateSubtotal(row) {
        var price = parseInt(row.find('.item-price').val()) || 0;
        var qty = parseInt(row.find('.item-qty').val()) || 0;
        row.find('.item-subtotal').val(price * qty);
        calculateTotal();
    }

    function calculateTotal() {
        var total = 0;
        $('.item-subtotal').each(function() {
            total += parseInt($(this).val()) || 0;
        });
        $('#grandTotal').val(total);
    }
});
</script>
<?php $extra_js = ob_get_clean(); ?>
