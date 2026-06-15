<?php
$dataDir = __DIR__;
$paidDir = $dataDir . '/paid';
$unpaidDir = $dataDir . '/unpaid';

$allPos = [];
foreach ([$paidDir, $unpaidDir] as $dir) {
    if (is_dir($dir)) {
        foreach (glob($dir . '/*.json') as $file) {
            $content = file_get_contents($file);
            $data = json_decode($content, true);
            if ($data) {
                // Ensure date and quantities are parsed correctly
                $allPos[] = [
                    'id' => isset($data['id']) ? $data['id'] : basename($file, '.json'),
                    'date' => isset($data['date']) ? $data['date'] : '',
                    'ref' => isset($data['ref']) ? $data['ref'] : '',
                    'source' => isset($data['source']) ? $data['source'] : '',
                    'q_tt' => isset($data['q_tt']) ? (int)$data['q_tt'] : 0,
                    'q_tb' => isset($data['q_tb']) ? (int)$data['q_tb'] : 0,
                    'q_tj' => isset($data['q_tj']) ? (int)$data['q_tj'] : 0,
                    'status' => basename($dir)
                ];
            }
        }
    }
}
$allPosJson = json_encode($allPos);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji & Operasional Angger - Mitra Telur Premium</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-hover: #4338ca;
            --bg: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
            --accent: #f59e0b;
            --success: #10b981;
            --danger: #ef4444;
            --shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            padding: 30px 20px;
            min-height: 100vh;
        }

        .header-section {
            max-width: 1300px;
            margin: 0 auto 24px auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .header-title h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 26px;
            font-weight: 800;
            color: var(--text-main);
        }

        .header-title p {
            font-size: 14px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .btn {
            background-color: var(--primary);
            color: white;
            padding: 10px 20px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
            text-decoration: none;
            box-shadow: 0 2px 4px rgba(79, 70, 229, 0.2);
        }

        .btn:hover {
            background-color: var(--primary-hover);
            transform: translateY(-1px);
        }

        .btn-outline {
            background-color: transparent;
            color: var(--text-main);
            border: 1px solid var(--border);
            box-shadow: none;
        }

        .btn-outline:hover {
            background-color: #f1f5f9;
            transform: none;
        }

        .main-grid {
            max-width: 1300px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 400px;
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 1024px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background-color: var(--card-bg);
            border-radius: 20px;
            border: 1px solid var(--border);
            padding: 24px;
            box-shadow: var(--shadow);
        }

        .filters-card {
            margin-bottom: 24px;
        }

        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .form-group label {
            font-size: 12px;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control {
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
            font-family: inherit;
            font-size: 14px;
            color: var(--text-main);
            transition: all 0.2s;
            width: 100%;
        }

        .form-control:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.15);
        }

        /* Radio Toggle Styles */
        .toggle-container {
            display: flex;
            background-color: #f1f5f9;
            padding: 4px;
            border-radius: 10px;
            width: fit-content;
        }

        .toggle-option {
            display: none;
        }

        .toggle-label {
            padding: 8px 16px;
            font-size: 13px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.2s;
            color: var(--text-muted);
        }

        .toggle-option:checked + .toggle-label {
            background-color: white;
            color: var(--primary);
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        /* Table Card Styles */
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .table-header h2 {
            font-size: 18px;
            font-weight: 700;
        }

        .badge {
            font-size: 11px;
            font-weight: 600;
            padding: 4px 8px;
            border-radius: 6px;
            text-transform: uppercase;
        }

        .badge-paid {
            background-color: #ecfdf5;
            color: var(--success);
        }

        .badge-unpaid {
            background-color: #fef2f2;
            color: var(--danger);
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        th {
            background-color: #f8fafc;
            color: var(--text-muted);
            font-weight: 600;
            padding: 12px 16px;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid #f1f5f9;
        }

        tr:hover td {
            background-color: #fafafc;
        }

        .no-data {
            text-align: center;
            color: var(--text-muted);
            padding: 40px 0;
            font-style: italic;
        }

        /* Invoice Card (Right Panel) */
        .invoice-card {
            position: sticky;
            top: 24px;
        }

        .invoice-brand {
            text-align: center;
            border-bottom: 2px dashed var(--border);
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .invoice-brand h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 800;
            letter-spacing: 0.5px;
        }

        .invoice-brand p {
            font-size: 12px;
            color: var(--text-muted);
            margin-top: 4px;
        }

        .invoice-meta {
            display: flex;
            flex-direction: column;
            gap: 8px;
            font-size: 13px;
            margin-bottom: 20px;
            background-color: #f8fafc;
            padding: 12px;
            border-radius: 12px;
        }

        .invoice-meta-row {
            display: flex;
            justify-content: space-between;
        }

        .invoice-meta-row span:first-child {
            color: var(--text-muted);
        }

        .invoice-meta-row span:last-child {
            font-weight: 500;
        }

        .invoice-items {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 24px;
        }

        .invoice-item {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .invoice-item-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .invoice-item-title {
            font-weight: 600;
            font-size: 14px;
        }

        .invoice-item-subtitle {
            font-size: 12px;
            color: var(--text-muted);
        }

        .invoice-item-price {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-main);
        }

        .invoice-divider {
            border-bottom: 1px solid var(--border);
            margin-bottom: 16px;
        }

        .invoice-total {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid var(--text-main);
        }

        .invoice-total span {
            font-family: 'Outfit', sans-serif;
            font-weight: 800;
            font-size: 18px;
        }

        .invoice-total-val {
            color: var(--primary);
        }

        .invoice-footer {
            margin-top: 24px;
            text-align: center;
            font-size: 11px;
            color: var(--text-muted);
        }

        /* Print Media Styles */
        @media print {
            body {
                background-color: white;
                color: black;
                padding: 0;
            }

            .header-section, .filters-card, .btn-print-wrapper, .table-card {
                display: none !important;
            }

            .main-grid {
                grid-template-columns: 1fr;
                max-width: 100%;
                margin: 0;
                gap: 0;
            }

            .invoice-card {
                border: none;
                box-shadow: none;
                padding: 0;
                position: static;
            }

            .invoice-brand {
                border-bottom: 2px dashed black;
            }

            .invoice-total {
                border-top: 2px solid black;
            }
        }
    </style>
</head>
<body>
    <div class="header-section">
        <div class="header-title">
            <a href="nota-telur-masuk.php" class="btn btn-outline" style="margin-bottom: 10px; padding: 6px 12px; font-size: 12px;">
                &larr; Kembali ke PO Utama
            </a>
            <h1>Slip Gaji &amp; Operasional Driver</h1>
            <p>Perhitungan Gaji Pokok, Biaya Antar, dan Biaya Sortir untuk Angger</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="copyToClipboard()" class="btn btn-outline" style="border-color: var(--primary); color: var(--primary);">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16" style="margin-right: 4px;">
                    <path d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z"/>
                    <path d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z"/>
                </svg>
                Salin Rincian (WA)
            </button>
            <button onclick="window.print()" class="btn">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"/>
                </svg>
                Cetak Slip Gaji
            </button>
        </div>
    </div>

    <div class="main-grid">
        <!-- Left Column: Filters and PO Details -->
        <div>
            <div class="card filters-card">
                <div class="filter-row">
                    <div class="form-group">
                        <label for="start-date">Tanggal Mulai</label>
                        <input type="date" id="start-date" class="form-control" value="2026-05-27">
                    </div>
                    <div class="form-group">
                        <label for="end-date">Tanggal Selesai</label>
                        <input type="date" id="end-date" class="form-control" value="2026-06-12">
                    </div>
                    <div class="form-group">
                        <label>Mode Hitung Biaya</label>
                        <div class="toggle-container">
                            <input type="radio" name="calc-mode" id="mode-tray" class="toggle-option" value="tray" checked>
                            <label for="mode-tray" class="toggle-label">Per Tray (Default)</label>
                            
                            <input type="radio" name="calc-mode" id="mode-egg" class="toggle-option" value="egg">
                            <label for="mode-egg" class="toggle-label">Per Butir</label>
                        </div>
                    </div>
                </div>
                
                <div class="filter-row" style="margin-top: 20px; border-top: 1px solid var(--border); padding-top: 20px;">
                    <div class="form-group">
                        <label for="salary-days">Hari Gaji Pokok</label>
                        <input type="number" id="salary-days" class="form-control" value="15" min="0">
                    </div>
                    <div class="form-group">
                        <label for="salary-rate">Rate Gaji Pokok (Rp/Hari)</label>
                        <input type="number" id="salary-rate" class="form-control" value="40000" min="0" step="1000">
                    </div>
                    <div class="form-group">
                        <label id="delivery-rate-label" for="delivery-rate">Rate Antar (Rp/Tray)</label>
                        <input type="number" id="delivery-rate" class="form-control" value="1000" min="0">
                    </div>
                    <div class="form-group">
                        <label id="sort-rate-label" for="sort-rate">Rate Sortir (Rp/Tray)</label>
                        <input type="number" id="sort-rate" class="form-control" value="500" min="0">
                    </div>
                    <div class="form-group">
                        <label for="overtime-count">Kali Lembur</label>
                        <input type="number" id="overtime-count" class="form-control" value="1" min="0">
                    </div>
                    <div class="form-group">
                        <label for="overtime-rate">Rate Lembur (Rp/Kali)</label>
                        <input type="number" id="overtime-rate" class="form-control" value="20000" min="0" step="1000">
                    </div>
                </div>
            </div>

            <div class="card table-card">
                <div class="table-header">
                    <h2>Daftar PO Masuk dalam Periode</h2>
                    <span class="badge badge-paid" id="po-count-badge">0 PO</span>
                </div>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Ref</th>
                                <th>Supplier</th>
                                <th style="text-align: right;">Tanggung (TT)</th>
                                <th style="text-align: right;">Besar (TB)</th>
                                <th style="text-align: right;">Jumbo (TJ)</th>
                                <th style="text-align: right;">Total Tray</th>
                                <th style="text-align: right;">Total Butir</th>
                            </tr>
                        </thead>
                        <tbody id="po-table-body">
                            <!-- Injected by JS -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Column: Salary Receipt -->
        <div>
            <div class="card invoice-card">
                <div class="invoice-brand">
                    <h2>MITRA TELUR PREMIUM</h2>
                    <p>Slip Rincian Upah &amp; Operasional Karyawan</p>
                </div>
                
                <div class="invoice-meta">
                    <div class="invoice-meta-row">
                        <span>Nama Karyawan:</span>
                        <span>Angger (Driver &amp; Sortir)</span>
                    </div>
                    <div class="invoice-meta-row">
                        <span>Periode Kerja:</span>
                        <span id="invoice-period">27 Mei 2026 - 12 Juni 2026</span>
                    </div>
                    <div class="invoice-meta-row">
                        <span>Tanggal Cetak:</span>
                        <span><?php echo date('d M Y'); ?></span>
                    </div>
                </div>

                <div class="invoice-items">
                    <!-- Basic Salary -->
                    <div class="invoice-item">
                        <div class="invoice-item-info">
                            <span class="invoice-item-title">Gaji Pokok</span>
                            <span class="invoice-item-subtitle" id="basic-salary-subtitle">15 Hari x Rp 40.000</span>
                        </div>
                        <span class="invoice-item-price" id="basic-salary-val">Rp 600.000</span>
                    </div>

                    <!-- Delivery Cost -->
                    <div class="invoice-item">
                        <div class="invoice-item-info">
                            <span class="invoice-item-title">Biaya Antar</span>
                            <span class="invoice-item-subtitle" id="delivery-cost-subtitle">0 butir x Rp 1.000</span>
                        </div>
                        <span class="invoice-item-price" id="delivery-cost-val">Rp 0</span>
                    </div>

                    <!-- Sorting Cost -->
                    <div class="invoice-item">
                        <div class="invoice-item-info">
                            <span class="invoice-item-title">Biaya Sortir</span>
                            <span class="invoice-item-subtitle" id="sort-cost-subtitle">0 butir x Rp 500</span>
                        </div>
                        <span class="invoice-item-price" id="sort-cost-val">Rp 0</span>
                    </div>

                    <!-- Overtime (Lembur) -->
                    <div class="invoice-item">
                        <div class="invoice-item-info">
                            <span class="invoice-item-title">Lembur</span>
                            <span class="invoice-item-subtitle" id="overtime-subtitle">1 Kali x Rp 20.000</span>
                        </div>
                        <span class="invoice-item-price" id="overtime-val">Rp 20.000</span>
                    </div>
                </div>

                <div class="invoice-divider"></div>

                <div class="invoice-items" style="margin-bottom: 12px;">
                    <div class="invoice-item" style="font-size: 13px; color: var(--text-muted);">
                        <span>Total Volume Kerja:</span>
                        <span id="total-vol-text">0 Tray (0 Butir)</span>
                    </div>
                </div>

                <div class="invoice-total">
                    <span>TOTAL UPAH</span>
                    <span id="grand-total-val">Rp 600.000</span>
                </div>

                <div class="invoice-footer">
                    <p>Terima kasih atas dedikasi dan kerja keras Anda.</p>
                    <p style="margin-top: 6px; font-weight: 500;">Slip gaji ini sah dan diproses secara sistem.</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Injected PO list from PHP
        const allPos = <?php echo $allPosJson; ?>;
        
        // DOM Elements
        const startDateInput = document.getElementById('start-date');
        const endDateInput = document.getElementById('end-date');
        const modeTrayRadio = document.getElementById('mode-tray');
        const modeEggRadio = document.getElementById('mode-egg');
        const salaryDaysInput = document.getElementById('salary-days');
        const salaryRateInput = document.getElementById('salary-rate');
        const deliveryRateInput = document.getElementById('delivery-rate');
        const sortRateInput = document.getElementById('sort-rate');
        const overtimeCountInput = document.getElementById('overtime-count');
        const overtimeRateInput = document.getElementById('overtime-rate');
        
        const deliveryRateLabel = document.getElementById('delivery-rate-label');
        const sortRateLabel = document.getElementById('sort-rate-label');
        
        const poTableBody = document.getElementById('po-table-body');
        const poCountBadge = document.getElementById('po-count-badge');
        
        const invoicePeriod = document.getElementById('invoice-period');
        const basicSalarySubtitle = document.getElementById('basic-salary-subtitle');
        const basicSalaryVal = document.getElementById('basic-salary-val');
        const deliveryCostSubtitle = document.getElementById('delivery-cost-subtitle');
        const deliveryCostVal = document.getElementById('delivery-cost-val');
        const sortCostSubtitle = document.getElementById('sort-cost-subtitle');
        const sortCostVal = document.getElementById('sort-cost-val');
        const overtimeSubtitle = document.getElementById('overtime-subtitle');
        const overtimeVal = document.getElementById('overtime-val');
        const totalVolText = document.getElementById('total-vol-text');
        const grandTotalVal = document.getElementById('grand-total-val');

        // Helper to format currency
        function formatIDR(amount) {
            return new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                maximumFractionDigits: 0
            }).format(amount);
        }

        // Helper to format date Indonesian
        function formatDateIndo(dateStr) {
            if (!dateStr) return '-';
            const dateObj = new Date(dateStr);
            if (isNaN(dateObj)) return dateStr;
            
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            
            const dayName = days[dateObj.getDay()];
            const date = dateObj.getDate();
            const month = months[dateObj.getMonth()];
            const year = dateObj.getFullYear();
            const hours = String(dateObj.getHours()).padStart(2, '0');
            const minutes = String(dateObj.getMinutes()).padStart(2, '0');
            
            return `${dayName}, ${date} ${month} ${year} | ${hours}:${minutes}`;
        }

        // Recalculate and render
        function updateReport() {
            const startVal = startDateInput.value;
            const endVal = endDateInput.value;
            const calcMode = document.querySelector('input[name="calc-mode"]:checked').value;
            
            const salaryDays = parseInt(salaryDaysInput.value) || 0;
            const salaryRate = parseInt(salaryRateInput.value) || 0;
            const deliveryRate = parseInt(deliveryRateInput.value) || 0;
            const sortRate = parseInt(sortRateInput.value) || 0;

            // Update Labels
            if (calcMode === 'tray') {
                deliveryRateLabel.textContent = 'Rate Antar (Rp/Tray)';
                sortRateLabel.textContent = 'Rate Sortir (Rp/Tray)';
            } else {
                deliveryRateLabel.textContent = 'Rate Antar (Rp/Butir)';
                sortRateLabel.textContent = 'Rate Sortir (Rp/Butir)';
            }

            // Parse Date
            const startDate = startVal ? new Date(startVal + 'T00:00:00') : null;
            const endDate = endVal ? new Date(endVal + 'T23:59:59') : null;

            // Filter POs
            const filteredPos = allPos.filter(po => {
                if (!po.date) return false;
                const poDate = new Date(po.date);
                if (startDate && poDate < startDate) return false;
                if (endDate && poDate > endDate) return false;
                return true;
            });

            // Sort filtered POs by date ascending
            filteredPos.sort((a, b) => new Date(a.date) - new Date(b.date));

            // Totals
            let sumTT = 0;
            let sumTB = 0;
            let sumTJ = 0;
            let sumTrays = 0;
            let sumEggs = 0;

            // Render Table
            poTableBody.innerHTML = '';
            if (filteredPos.length === 0) {
                poTableBody.innerHTML = `<tr><td colspan="8" class="no-data">Tidak ada data PO dalam periode ini</td></tr>`;
            } else {
                filteredPos.forEach(po => {
                    const totalTrays = po.q_tt + po.q_tb + po.q_tj;
                    const totalEggs = totalTrays * 30; // 1 Tray = 30 Eggs

                    sumTT += po.q_tt;
                    sumTB += po.q_tb;
                    sumTJ += po.q_tj;
                    sumTrays += totalTrays;
                    sumEggs += totalEggs;

                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${formatDateIndo(po.date)}</td>
                        <td><strong>${po.ref}</strong> <span class="badge ${po.status === 'paid' ? 'badge-paid' : 'badge-unpaid'}">${po.status}</span></td>
                        <td>${po.source}</td>
                        <td style="text-align: right;">${po.q_tt}</td>
                        <td style="text-align: right;">${po.q_tb}</td>
                        <td style="text-align: right;">${po.q_tj}</td>
                        <td style="text-align: right; font-weight: 600;">${totalTrays}</td>
                        <td style="text-align: right; color: var(--text-muted);">${totalEggs.toLocaleString('id-ID')}</td>
                    `;
                    poTableBody.appendChild(tr);
                });
            }

            poCountBadge.textContent = `${filteredPos.length} PO`;

            // Update Period text
            let periodText = 'Semua Periode';
            if (startVal && endVal) {
                const sDate = new Date(startVal);
                const eDate = new Date(endVal);
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                periodText = `${sDate.getDate()} ${months[sDate.getMonth()]} ${sDate.getFullYear()} - ${eDate.getDate()} ${months[eDate.getMonth()]} ${eDate.getFullYear()}`;
            }
            invoicePeriod.textContent = periodText;

            // Calculate Gaji
            const basicSalaryTotal = salaryDays * salaryRate;
            
            // Biaya Antar & Sortir
            let deliveryTotal = 0;
            let sortTotal = 0;
            let qtyUnitText = '';

            if (calcMode === 'tray') {
                deliveryTotal = sumTrays * deliveryRate;
                sortTotal = sumTrays * sortRate;
                qtyUnitText = 'tray';
                
                deliveryCostSubtitle.textContent = `${sumTrays} tray x ${formatIDR(deliveryRate)}`;
                sortCostSubtitle.textContent = `${sumTrays} tray x ${formatIDR(sortRate)}`;
            } else {
                deliveryTotal = sumEggs * deliveryRate;
                sortTotal = sumEggs * sortRate;
                qtyUnitText = 'butir';
                
                deliveryCostSubtitle.textContent = `${sumEggs.toLocaleString('id-ID')} butir x ${formatIDR(deliveryRate)}`;
                sortCostSubtitle.textContent = `${sumEggs.toLocaleString('id-ID')} butir x ${formatIDR(sortRate)}`;
            }

            const overtimeCount = parseInt(overtimeCountInput.value) || 0;
            const overtimeRate = parseInt(overtimeRateInput.value) || 0;
            const overtimeTotal = overtimeCount * overtimeRate;

            const grandTotal = basicSalaryTotal + deliveryTotal + sortTotal + overtimeTotal;

            // Update slip view
            basicSalarySubtitle.textContent = `${salaryDays} Hari x ${formatIDR(salaryRate)}`;
            basicSalaryVal.textContent = formatIDR(basicSalaryTotal);
            
            deliveryCostVal.textContent = formatIDR(deliveryTotal);
            sortCostVal.textContent = formatIDR(sortTotal);
            
            overtimeSubtitle.textContent = `${overtimeCount} Kali x ${formatIDR(overtimeRate)}`;
            overtimeVal.textContent = formatIDR(overtimeTotal);
            
            totalVolText.textContent = `${sumTrays} Tray (${sumEggs.toLocaleString('id-ID')} Butir)`;
            grandTotalVal.textContent = formatIDR(grandTotal);
        }

        // Event Listeners
        [startDateInput, endDateInput, salaryDaysInput, salaryRateInput, deliveryRateInput, sortRateInput, overtimeCountInput, overtimeRateInput].forEach(elem => {
            elem.addEventListener('input', updateReport);
        });

        [modeTrayRadio, modeEggRadio].forEach(elem => {
            elem.addEventListener('change', updateReport);
        });

        function copyToClipboard() {
            const startVal = startDateInput.value;
            const endVal = endDateInput.value;
            const calcMode = document.querySelector('input[name="calc-mode"]:checked').value;
            
            const salaryDays = parseInt(salaryDaysInput.value) || 0;
            const salaryRate = parseInt(salaryRateInput.value) || 0;
            const deliveryRate = parseInt(deliveryRateInput.value) || 0;
            const sortRate = parseInt(sortRateInput.value) || 0;
            const overtimeCount = parseInt(overtimeCountInput.value) || 0;
            const overtimeRate = parseInt(overtimeRateInput.value) || 0;
            
            let sumTT = 0;
            let sumTB = 0;
            let sumTJ = 0;
            let sumTrays = 0;
            
            const startDate = startVal ? new Date(startVal + 'T00:00:00') : null;
            const endDate = endVal ? new Date(endVal + 'T23:59:59') : null;

            allPos.forEach(po => {
                if (!po.date) return;
                const poDate = new Date(po.date);
                if (startDate && poDate < startDate) return;
                if (endDate && poDate > endDate) return;
                
                sumTT += po.q_tt;
                sumTB += po.q_tb;
                sumTJ += po.q_tj;
                sumTrays += (po.q_tt + po.q_tb + po.q_tj);
            });
            
            const sumEggs = sumTrays * 30;
            const basicSalaryTotal = salaryDays * salaryRate;
            const overtimeTotal = overtimeCount * overtimeRate;
            
            let deliveryTotal = 0;
            let sortTotal = 0;
            let detailUnit = '';
            
            if (calcMode === 'tray') {
                deliveryTotal = sumTrays * deliveryRate;
                sortTotal = sumTrays * sortRate;
                detailUnit = 'tray';
            } else {
                deliveryTotal = sumEggs * deliveryRate;
                sortTotal = sumEggs * sortRate;
                detailUnit = 'butir';
            }
            
            const grandTotal = basicSalaryTotal + deliveryTotal + sortTotal + overtimeTotal;
            
            const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const sDate = new Date(startVal);
            const eDate = new Date(endVal);
            const periodStr = `${sDate.getDate()} ${months[sDate.getMonth()]} ${sDate.getFullYear()} - ${eDate.getDate()} ${months[eDate.getMonth()]} ${eDate.getFullYear()}`;
            
            const textToCopy = `*SLIP UPAH & OPERASIONAL DRIVER - ANGGER*
Periode: ${periodStr}

*Volume Kerja:*
- Tanggung (TT): ${sumTT} tray
- Besar (TB): ${sumTB} tray
- Jumbo (TJ): ${sumTJ} tray
- Total Volume: ${sumTrays} tray (${sumEggs.toLocaleString('id-ID')} butir)

*Rincian Upah:*
1. Gaji Pokok: ${salaryDays} Hari x ${formatIDR(salaryRate)} = ${formatIDR(basicSalaryTotal)}
2. Biaya Antar: ${calcMode === 'tray' ? sumTrays : sumEggs.toLocaleString('id-ID')} ${detailUnit} x ${formatIDR(deliveryRate)} = ${formatIDR(deliveryTotal)}
3. Biaya Sortir: ${calcMode === 'tray' ? sumTrays : sumEggs.toLocaleString('id-ID')} ${detailUnit} x ${formatIDR(sortRate)} = ${formatIDR(sortTotal)}
4. Lembur: ${overtimeCount} Kali x ${formatIDR(overtimeRate)} = ${formatIDR(overtimeTotal)}

*TOTAL UPAH DITERIMA:* *${formatIDR(grandTotal)}*

_Dicetak otomatis oleh Sistem Mitra Telur Premium pada ${new Date().toLocaleDateString('id-ID', {day: 'numeric', month: 'long', year: 'numeric'})}_`;

            navigator.clipboard.writeText(textToCopy).then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil Disalin!',
                    text: 'Rincian upah telah disalin ke clipboard, siap ditempel ke WhatsApp.',
                    confirmButtonColor: '#4f46e5',
                    timer: 3000
                });
            }).catch(err => {
                console.error('Failed to copy: ', err);
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Menyalin',
                    text: 'Silakan salin rincian secara manual.',
                    confirmButtonColor: '#ef4444'
                });
            });
        }

        // Initial trigger
        updateReport();
    </script>
</body>
</html>
