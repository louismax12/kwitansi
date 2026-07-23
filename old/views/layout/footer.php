                </div>
            </main>
            <footer class="py-4 bg-light mt-auto no-print">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; RKZ POS 2026</div>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    
    <!-- Area Khusus Print -->
    <style>
        @media print {
            .no-print { display: none !important; }
            body { 
                margin: 0; 
                padding: 0; 
                background: white; 
                font-family: 'Courier New', Courier, monospace; 
                font-size: 14px;
                font-weight: bold;
            }
            @page {
                size: 21cm 17cm;
                margin: 0;
            }
            
            #print-view {
                position: relative;
                width: 21cm;
                height: 17cm;
                overflow: hidden;
                display: block !important;
            }

            .abs-pos {
                position: absolute;
            }

            /* Koordinat perkiraan kasar (Top, Left) - Sesuaikan saat test print */
            #print_no_kwitansi       { top: 4.5cm; left: 15.5cm; }
            #print_no_faktur         { top: 5.4cm; left: 15.5cm; }
            
            #print_terima_dari       { top: 8.3cm; left: 4.8cm; width: 15cm; }
            #print_uang_sejumlah     { top: 9.3cm; left: 4.8cm; width: 15cm; }
            #print_untuk_pembayaran  { top: 10.3cm; left: 4.8cm; width: 15cm; }
            
            #print_grand_total       { top: 13.8cm; left: 4.2cm; font-size: 16px; }
            #print_tanggal           { top: 11.5cm; left: 15.5cm; }
        }
    </style>
    
    <div class="d-none" id="print-view">
        <div id="print_no_kwitansi" class="abs-pos"></div>
        <div id="print_no_faktur" class="abs-pos"></div>
        
        <div id="print_terima_dari" class="abs-pos"></div>
        <div id="print_uang_sejumlah" class="abs-pos"></div>
        <div id="print_untuk_pembayaran" class="abs-pos"></div>
        
        <div id="print_grand_total" class="abs-pos"></div>
        <div id="print_tanggal" class="abs-pos"></div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script src="assets/sbadmin/js/scripts.js"></script>
</body>
</html>
