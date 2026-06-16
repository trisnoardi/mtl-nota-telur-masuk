<?php
$dataDir = __DIR__;
$paidDir = $dataDir . '/paid';
$unpaidDir = $dataDir . '/unpaid';

if (!is_dir($paidDir)) mkdir($paidDir, 0777, true);
if (!is_dir($unpaidDir)) mkdir($unpaidDir, 0777, true);

if (isset($_GET['api'])) {
    header('Content-Type: application/json');
    $api = $_GET['api'];
    
    if ($api === 'save') {
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input && isset($input['po']) && isset($input['po']['id'])) {
            $po = $input['po'];
            $id = $po['id'];
            $is_lunas = isset($po['is_lunas']) ? $po['is_lunas'] : false;
            
            $jsonFilename = $id . '.json';
            $imgFilename = $id . '.jpg';
            
            if (file_exists($paidDir . '/' . $jsonFilename)) {
                unlink($paidDir . '/' . $jsonFilename);
                if (file_exists($paidDir . '/' . $imgFilename)) unlink($paidDir . '/' . $imgFilename);
            }
            if (file_exists($unpaidDir . '/' . $jsonFilename)) {
                unlink($unpaidDir . '/' . $jsonFilename);
                if (file_exists($unpaidDir . '/' . $imgFilename)) unlink($unpaidDir . '/' . $imgFilename);
            }
            
            $targetDir = $is_lunas ? $paidDir : $unpaidDir;
            file_put_contents($targetDir . '/' . $jsonFilename, json_encode($po, JSON_PRETTY_PRINT));
            
            if (isset($input['image']) && !empty($input['image'])) {
                $imgData = $input['image'];
                $imgData = str_replace('data:image/jpeg;base64,', '', $imgData);
                $imgData = str_replace(' ', '+', $imgData);
                $imgData = base64_decode($imgData);
                file_put_contents($targetDir . '/' . $imgFilename, $imgData);
            }
            echo json_encode(['success' => true]);
            exit;
        }
    }
    
    if ($api === 'delete') {
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input && isset($input['id'])) {
            $id = $input['id'];
            $jsonFilename = $id . '.json';
            $imgFilename = $id . '.jpg';
            
            if (file_exists($paidDir . '/' . $jsonFilename)) unlink($paidDir . '/' . $jsonFilename);
            if (file_exists($paidDir . '/' . $imgFilename)) unlink($paidDir . '/' . $imgFilename);
            if (file_exists($unpaidDir . '/' . $jsonFilename)) unlink($unpaidDir . '/' . $jsonFilename);
            if (file_exists($unpaidDir . '/' . $imgFilename)) unlink($unpaidDir . '/' . $imgFilename);
            
            echo json_encode(['success' => true]);
            exit;
        }
    }
    exit;
}

$initialPos = [];
foreach ([$paidDir, $unpaidDir] as $dir) {
    foreach (glob($dir . '/*.json') as $file) {
        $content = file_get_contents($file);
        $data = json_decode($content, true);
        if ($data) {
            $imgFile = str_replace('.json', '.jpg', $file);
            $data['needs_image'] = !file_exists($imgFile);
            $initialPos[] = $data;
        }
    }
}
$initialPosJson = json_encode($initialPos);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PO Pro - Mitra Telur Premium</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Outfit:wght@600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg);
            color: var(--text-main);
            padding: 40px 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            overflow-x: hidden;
            min-height: 100vh;
        }

        .container { width: 95%; max-width: 1400px; display: flex; flex-direction: column; align-items: center; }

        /* Top Bar */
        .top-bar {
            width: 100%;
            max-width: 1200px;
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        /* Modal / Popup */
        #edit-modal {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(8px);
            display: none; /* Controlled by JS */
            justify-content: center;
            align-items: center;
            z-index: 1000;
            padding: 20px;
        }

        .modal-content {
            background: white;
            width: 100%;
            max-width: 800px;
            max-height: 90vh;
            border-radius: 24px;
            padding: 40px;
            overflow-y: auto;
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            animation: modalIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes modalIn {
            from { opacity: 0; transform: scale(0.95) translateY(10px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .close-modal {
            position: absolute;
            top: 25px; right: 25px;
            background: #f1f5f9;
            border: none;
            width: 36px; height: 36px;
            border-radius: 50%;
            cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s;
        }
        .close-modal:hover { background: #e2e8f0; transform: rotate(90deg); }

        /* Form Grid */
        .form-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .form-group { display: flex; flex-direction: column; gap: 8px; }
        .form-group.full { grid-column: 1 / -1; }
        label { font-size: 11px; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.5px; }
        input, select, textarea { padding: 12px 16px; border-radius: 12px; border: 1px solid var(--border); font-family: inherit; font-size: 14px; transition: all 0.2s; }
        input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1); outline: none; }
        .section-title { grid-column: 1 / -1; font-size: 15px; font-weight: 700; color: var(--primary); margin-top: 15px; border-bottom: 2px solid #f1f5f9; padding-bottom: 8px; }

        /* Toggle Switch */
        .switch { position: relative; display: inline-block; width: 50px; height: 26px; }
        .switch input { opacity: 0; width: 0; height: 0; }
        .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #cbd5e1; transition: .4s; border-radius: 34px; }
        .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
        input:checked + .slider { background-color: #22c55e; }
        input:checked + .slider:before { transform: translateX(24px); }

        /* Multi-Nota Area */
        .scroll-wrapper {
            width: 100%;
            padding: 20px 20px 80px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 60px; /* Space between invoices */
        }


        .nota-container {
            flex: 0 0 auto;
            scroll-snap-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .nota-actions {
            display: flex;
            gap: 12px;
            width: 100%;
            max-width: 500px;
            justify-content: center;
        }

        /* Invoice Card */
        .invoice-card {
            background: white;
            width: 500px;
            padding: 45px;
            border-radius: 28px;
            box-shadow: 0 25px 30px -10px rgba(0, 0, 0, 0.08);
            position: relative;
            overflow: hidden;
            border: 1px solid var(--border);
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .invoice-card.bg-lunas { background-color: #f0fdf4; border-color: #bbf7d0; }
        .invoice-card.bg-pending { background-color: #fff1f2; border-color: #fecdd3; }

        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { font-family: 'Outfit', sans-serif; font-size: 28px; font-weight: 800; color: var(--primary); letter-spacing: -0.5px; }
        .header h2 { font-family: 'Outfit', sans-serif; font-size: 18px; color: var(--accent); margin-top: -2px; }

        .info-grid { display: grid; gap: 12px; margin-bottom: 25px; padding: 20px; background: rgba(0,0,0,0.03); border-radius: 16px; }
        .info-item { display: flex; justify-content: space-between; font-size: 14px; }
        .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 10px; font-weight: 800; text-transform: uppercase; }
        .status-lunas { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .status-pending { background: #fef3c7; color: #92400e; border: 1px solid #fde68a; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 25px; }
        thead th { text-align: left; font-size: 11px; font-weight: 700; color: var(--text-muted); border-bottom: 2px solid rgba(0,0,0,0.04); padding-bottom: 10px; }
        tbody td { padding: 14px 0; border-bottom: 1px solid rgba(0,0,0,0.04); font-size: 14px; }

        .footer { border-top: 2px dashed rgba(0,0,0,0.08); padding-top: 20px; display: flex; justify-content: space-between; align-items: center; }
        .total-value { font-family: 'Outfit', sans-serif; font-size: 26px; font-weight: 800; color: var(--primary); }

        /* Buttons */
        .btn { cursor: pointer; padding: 12px 24px; border-radius: 14px; border: none; font-weight: 700; font-size: 14px; display: flex; align-items: center; gap: 10px; transition: all 0.2s; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-hover); transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3); }
        .btn-outline { background: white; color: var(--text-main); border: 1px solid var(--border); }
        .btn-outline:hover { background: #f8fafc; }
        .btn-danger { background: #fff1f2; color: #e11d48; }
        .btn-sm { padding: 8px 16px; font-size: 12px; border-radius: 10px; }

        @media (max-width: 600px) {
            .invoice-card { width: 380px; padding: 25px; }
            .scroll-wrapper { padding: 20px 20px; }
        }
    </style>
</head>
<body>

    <div class="top-bar">
        <button class="btn btn-primary" style="padding: 16px 32px; font-size: 16px;" onclick="addNewPO()">
            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Buat Purchase Order Baru
        </button>
    </div>

    <!-- Edit Modal -->
    <div id="edit-modal">
        <div class="modal-content">
            <button class="close-modal" onclick="closeModal()">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
            </button>
            
            <h2 style="font-family: 'Outfit', sans-serif; margin-bottom: 25px; font-size: 22px; color: var(--primary);">Edit Detail PO #<span id="modal-po-ref"></span></h2>

            <div class="form-grid">
                <div class="section-title">Informasi Dasar</div>
                <div class="form-group">
                    <label>Tanggal & Jam Nota</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="datetime-local" id="input-date" style="flex:1">
                        <button class="btn btn-outline btn-sm" onclick="setToday('input-date')">Sekarang</button>
                    </div>
                </div>

                <div class="form-group">
                    <label>Sumber / Supplier</label>
                    <input type="text" id="input-source">
                </div>
                <div class="form-group full">
                    <label>Keterangan</label>
                    <textarea id="input-desc" rows="2"></textarea>
                </div>

                <div class="section-title">Kuantitas & Harga</div>
                <div class="form-group">
                    <label>Tanggung (Qty | Harga)</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="number" id="input-q-tt" style="width: 80px">
                        <input type="number" id="input-p-tt" style="flex:1">
                    </div>
                </div>
                <div class="form-group">
                    <label>Besar (Qty | Harga)</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="number" id="input-q-tb" style="width: 80px">
                        <input type="number" id="input-p-tb" style="flex:1">
                    </div>
                </div>
                <div class="form-group">
                    <label>Jumbo (Qty | Harga)</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="number" id="input-q-tj" style="width: 80px">
                        <input type="number" id="input-p-tj" style="flex:1">
                    </div>
                </div>

                <div class="section-title">Pembayaran</div>
                <div class="form-group">
                    <label>Tanggal Pembayaran</label>
                    <div style="display: flex; gap: 8px;">
                        <input type="text" id="input-pay-date" style="flex:1">
                        <button class="btn btn-outline btn-sm" onclick="setTodayPay()">Hari Ini</button>
                    </div>
                </div>
                <div class="form-group">
                    <label>Status Lunas</label>
                    <div style="display: flex; align-items: center; gap: 12px; margin-top: 5px;">
                        <label class="switch">
                            <input type="checkbox" id="input-status-toggle">
                            <span class="slider"></span>
                        </label>
                        <span id="toggle-text" style="font-weight: 600; font-size: 14px;">Belum Lunas</span>
                    </div>
                </div>
            </div>

            <div style="margin-top: 35px; display: flex; justify-content: space-between;">
                <button class="btn btn-danger" onclick="deleteActivePO()">Hapus PO Ini</button>
                <button class="btn btn-primary" onclick="closeModal()">Selesai & Simpan</button>
            </div>
        </div>
    </div>

    <!-- Dashboard Report -->
    <div id="ui-report-container" style="display: flex; justify-content: center; width: 100%; margin-bottom: 20px;"></div>

    <!-- Copy Report Button -->
    <div style="display: flex; justify-content: center; width: 100%; margin-bottom: 30px;">
        <button class="btn btn-primary btn-sm" onclick="copyReportScreenshot()" style="background: #166534;">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
            Copy Screenshot Mutasi
        </button>
    </div>

    <!-- Scrollable Gallery -->
    <div class="scroll-wrapper" id="po-list-wrapper">
        <!-- Injected by JS -->
    </div>

    <script>
        let pos = <?php echo $initialPosJson; ?>;
        let activeIdx = 0;

        const defaultPO = (lastPO = null) => {
            const base = lastPO || {
                source: 'Peternakan UD Mitra Ilahi',
                desc: 'Diantarkan kak Indra',
                q_tt: 3, q_tb: 1, q_tj: 1,
                p_tt: 54000, p_tb: 56000, p_tj: 58000,
                pay_date: '-',
                is_lunas: false
            };
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            const dateStr = now.toISOString().slice(0, 16); 
            
            return {
                ...base,
                id: Date.now(),
                date: dateStr,
                ref: 'PO-' + Math.random().toString(36).substr(2, 6).toUpperCase(),
                sort: new Date(dateStr).getTime()
            };
        };

        const inputs = {
            date: document.getElementById('input-date'),
            source: document.getElementById('input-source'),
            desc: document.getElementById('input-desc'),
            q_tt: document.getElementById('input-q-tt'),
            q_tb: document.getElementById('input-q-tb'),
            q_tj: document.getElementById('input-q-tj'),
            p_tt: document.getElementById('input-p-tt'),
            p_tb: document.getElementById('input-p-tb'),
            p_tj: document.getElementById('input-p-tj'),
            pay_date: document.getElementById('input-pay-date'),
            status: document.getElementById('input-status-toggle')
        };

        function formatIDR(val) { return new Intl.NumberFormat('id-ID').format(val); }
        function formatShortDate(val) {
            if(!val) return '-';
            const d = new Date(val);
            const m = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            return `${d.getDate()} ${m[d.getMonth()]} ${d.getFullYear()}`;
        }
        function formatDate(val) { 
            if(!val) return '-';
            const d = new Date(val);
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const m = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const time = d.getHours().toString().padStart(2, '0') + ':' + d.getMinutes().toString().padStart(2, '0');
            return `${days[d.getDay()]}, ${d.getDate()} ${m[d.getMonth()]} ${d.getFullYear()} | ${time}`;
        }

        async function generateCardCanvas(card) {
            try {
                const canvas = await html2canvas(card, { scale: 3, backgroundColor: null });
                return canvas;
            } catch (err) {
                throw err;
            }
        }

        async function generateReportCanvas() {
            const report = document.getElementById('dashboard-report');
            if (!report) return null;
            return await html2canvas(report, { scale: 3, backgroundColor: null });
        }

        async function copyReportScreenshot() {
            try {
                const canvas = await generateReportCanvas();
                if (!canvas) return Swal.fire('Error', 'Tidak ada data mutasi', 'error');
                canvas.toBlob(async blob => {
                    try {
                        await navigator.clipboard.write([new ClipboardItem({ [blob.type]: blob })]);
                        Swal.fire({ icon: 'success', title: 'Mutasi disalin!', timer: 1000, showConfirmButton: false, toast: true, position: 'top-end' });
                    } catch(e) {
                        Swal.fire('Error', 'Gagal copy ke clipboard', 'error');
                    }
                });
            } catch (err) {
                Swal.fire('Error', 'Gagal generate gambar mutasi', 'error');
            }
        }

        async function savePOToBackend(po) {
            try {
                let imageBase64 = null;
                const card = document.querySelector(`[data-id="${po.id}"]`);
                if (card) {
                    const canvas = await generateCardCanvas(card);
                    imageBase64 = canvas.toDataURL('image/jpeg', 0.9);
                }
                await fetch('?api=save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ po: po, image: imageBase64 })
                });
            } catch(e) {
                console.error("Failed to save to backend", e);
            }
        }

        async function deletePOFromBackend(poId) {
            try {
                await fetch('?api=delete', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: poId })
                });
            } catch(e) {
                console.error("Failed to delete from backend", e);
            }
        }

        let saveTimeout = null;
        function savePOToBackendDebounced(po) {
            if(saveTimeout) clearTimeout(saveTimeout);
            saveTimeout = setTimeout(() => {
                savePOToBackend(po);
            }, 500);
        }

        function formatQty(q) {
            const ikat = Math.floor(q / 6);
            const sisa = q % 6;
            if (ikat > 0 && sisa > 0) return q + ' tray (' + ikat + ' ikat ' + sisa + ' tray)';
            if (ikat > 0) return q + ' tray (' + ikat + ' ikat)';
            if (sisa > 0) return q + ' tray';
            return q + ' tray';
        }

        function renderPOs() {
            const uiReportContainer = document.getElementById('ui-report-container');
            if (uiReportContainer) {
                uiReportContainer.innerHTML = generateReportHTML('dashboard-report', true);
            }

            const wrapper = document.getElementById('po-list-wrapper');
            wrapper.innerHTML = '';

            pos.sort((a, b) => (b.sort || new Date(b.date).getTime()) - (a.sort || new Date(a.date).getTime()));

            pos.forEach((po, idx) => {
                const total = (po.q_tt*po.p_tt) + (po.q_tb*po.p_tb) + (po.q_tj*po.p_tj);
                
                const container = document.createElement('div');
                container.className = 'nota-container';
                
                const styleTt = po.q_tt === 0 ? 'text-decoration: line-through; opacity: 0.4;' : '';
                const styleTb = po.q_tb === 0 ? 'text-decoration: line-through; opacity: 0.4;' : '';
                const styleTj = po.q_tj === 0 ? 'text-decoration: line-through; opacity: 0.4;' : '';

                container.innerHTML = `
                    <div class="nota-actions">
                        <button class="btn btn-outline btn-sm" onclick="openEditModal(${idx})">
                             <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                             Edit Data
                        </button>
                        ${!po.is_lunas ? `
                        <button class="btn btn-sm" style="background: #16a34a; color: white; border: none; font-weight: 600;" onclick="lunasiPO(${idx})">
                             <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin-right:4px"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                             Lunasi
                        </button>` : ''}
                        <button class="btn btn-primary btn-sm" onclick="copyScreenshot(${idx})">
                             <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
                             Copy Screenshot
                        </button>
                    </div>
                    <div class="invoice-card ${po.is_lunas ? 'bg-lunas' : 'bg-pending'}" id="card-${idx}" data-id="${po.id}">
                        <div class="header">
                            <h1>Invoice: Pembelian Telur</h1>
                            <h2>Mitra Telur Premium</h2>
                            <p style="font-weight:600">Ref: ${po.ref}</p>
                        </div>
                        <div class="info-grid">
                            <div class="info-item"><span style="color:#64748b">Supplier</span><b>${po.source}</b></div>
                            <div class="info-item"><span style="color:#64748b">Tanggal</span><b>${formatDate(po.date)}</b></div>
                            <div class="info-item"><span style="color:#64748b">Keterangan</span><b>${po.desc}</b></div>
                            <div class="info-item"><span style="color:#64748b">Bayar</span><b>${po.pay_date}</b></div>
                            <div class="info-item"><span style="color:#64748b">Status</span>
                                <span class="status-badge ${po.is_lunas ? 'status-lunas' : 'status-pending'}">
                                    ${po.is_lunas ? 'LUNAS' : 'PENDING'}
                                </span>
                            </div>
                        </div>
                        <table>
                            <thead><tr><th>PRODUK</th><th style="text-align:center">QTY</th><th style="text-align:center">HARGA</th><th style="text-align:right">TOTAL</th></tr></thead>
                            <tbody>
                                <tr style="${styleTt}"><td>Tanggung</td><td style="text-align:center">${formatQty(po.q_tt)}</td><td style="text-align:center">${formatIDR(po.p_tt)}</td><td style="text-align:right">${formatIDR(po.q_tt*po.p_tt)}</td></tr>
                                <tr style="${styleTb}"><td>Besar</td><td style="text-align:center">${formatQty(po.q_tb)}</td><td style="text-align:center">${formatIDR(po.p_tb)}</td><td style="text-align:right">${formatIDR(po.q_tb*po.p_tb)}</td></tr>
                                <tr style="${styleTj}"><td>Jumbo</td><td style="text-align:center">${formatQty(po.q_tj)}</td><td style="text-align:center">${formatIDR(po.p_tj)}</td><td style="text-align:right">${formatIDR(po.q_tj*po.p_tj)}</td></tr>
                            </tbody>
                        </table>
                        <div class="footer">
                            <span style="font-size:16px; font-weight:700">TOTAL AKHIR</span>
                            <span class="total-value">Rp ${formatIDR(total)}</span>
                        </div>
                    </div>
                `;
                wrapper.appendChild(container);
            });
        }

        function openEditModal(idx) {
            activeIdx = idx;
            const po = pos[idx];
            document.getElementById('modal-po-ref').innerText = po.ref;
            inputs.date.value = po.date;
            inputs.source.value = po.source;
            inputs.desc.value = po.desc;
            inputs.q_tt.value = po.q_tt;
            inputs.q_tb.value = po.q_tb;
            inputs.q_tj.value = po.q_tj;
            inputs.p_tt.value = po.p_tt;
            inputs.p_tb.value = po.p_tb;
            inputs.p_tj.value = po.p_tj;
            inputs.pay_date.value = po.pay_date;
            inputs.status.checked = po.is_lunas;
            document.getElementById('toggle-text').innerText = po.is_lunas ? 'Lunas' : 'Belum Lunas';
            
            document.getElementById('edit-modal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('edit-modal').style.display = 'none';
        }

        function updateData() {
            const dateVal = inputs.date.value;
            const sortVal = dateVal ? new Date(dateVal).getTime() : Date.now();
            const po = {
                ...pos[activeIdx],
                date: dateVal,
                sort: sortVal,
                source: inputs.source.value,
                desc: inputs.desc.value,
                q_tt: parseInt(inputs.q_tt.value) || 0,
                q_tb: parseInt(inputs.q_tb.value) || 0,
                q_tj: parseInt(inputs.q_tj.value) || 0,
                p_tt: parseInt(inputs.p_tt.value) || 0,
                p_tb: parseInt(inputs.p_tb.value) || 0,
                p_tj: parseInt(inputs.p_tj.value) || 0,
                pay_date: inputs.pay_date.value,
                is_lunas: inputs.status.checked
            };
            pos[activeIdx] = po;
            savePOToBackendDebounced(po);
            document.getElementById('toggle-text').innerText = po.is_lunas ? 'Lunas' : 'Belum Lunas';
            renderPOs();
        }

        function addNewPO() {
            const lastPO = pos.length > 0 ? pos[0] : null; 
            const newPO = defaultPO(lastPO);
            pos.unshift(newPO); 
            renderPOs();
            savePOToBackend(newPO);
            openEditModal(0);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function deleteActivePO() {
            if(pos.length <= 1) return Swal.fire('Error', 'Minimal harus ada 1 PO', 'error');
            Swal.fire({ title: 'Hapus PO?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Ya, Hapus' }).then(res => {
                if(res.isConfirmed) {
                    const idToDelete = pos[activeIdx].id;
                    pos.splice(activeIdx, 1);
                    deletePOFromBackend(idToDelete);
                    closeModal();
                    renderPOs();
                }
            });
        }

        function setToday(id) { 
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById(id).value = now.toISOString().slice(0, 16); 
            updateData(); 
        }

        function setTodayPay() { 
            const d = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const m = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            inputs.pay_date.value = `${days[d.getDay()]}, ${d.getDate()} ${m[d.getMonth()]} ${d.getFullYear()}`;
            updateData();
        }

        function getMonthNum(mon) {
            const m = {'Jan':0,'Januari':0,'Feb':1,'Februari':1,'Mar':2,'Maret':2,'Apr':3,'April':3,'Mei':4,'Jun':5,'Juni':5,'Jul':6,'Juli':6,'Agu':7,'Agustus':7,'Sep':8,'September':8,'Okt':9,'Oktober':9,'Nov':10,'November':10,'Des':11,'Desember':11};
            return m[mon] !== undefined ? m[mon] : 0;
        }

        function parsePayDate(str) {
            let dateStr = str.includes(', ') ? str.split(', ')[1] : str;
            let parts = dateStr.split(' ');
            return new Date(parseInt(parts[2]), getMonthNum(parts[1]), parseInt(parts[0]), 23, 59, 59).getTime();
        }

        function generateReportHTML(reportId = 'temp-report', forUI = false) {
            // Step 1: Build all events — each nota (creation) + each payment (grouped by pay_date)
            let events = [];

            // Add nota creation events — always sort by transaction date (sort or date)
            pos.forEach(p => {
                let val = p.q_tt*p.p_tt + p.q_tb*p.p_tb + p.q_tj*p.p_tj;
                let sortKey = p.sort || new Date(p.date).getTime();
                
                let details = [];
                if (p.q_tt > 0) details.push(`<strong>${p.q_tt}t</strong> x ${p.p_tt >= 1000 ? (p.p_tt/1000) + 'rb' : formatIDR(p.p_tt)}`);
                if (p.q_tb > 0) details.push(`<strong>${p.q_tb}b</strong> x ${p.p_tb >= 1000 ? (p.p_tb/1000) + 'rb' : formatIDR(p.p_tb)}`);
                if (p.q_tj > 0) details.push(`<strong>${p.q_tj}j</strong> x ${p.p_tj >= 1000 ? (p.p_tj/1000) + 'rb' : formatIDR(p.p_tj)}`);
                let detailsStr = details.join(' | ');

                events.push({
                    date: p.date,
                    sortKey: sortKey,
                    ref: p.ref,
                    val: val,
                    type: 'nota',
                    is_lunas: p.is_lunas,
                    details: detailsStr
                });
            });

            // Group paid POs by pay_date → each payment date = 1 event
            let paidGroups = {};
            pos.filter(p => p.is_lunas && p.pay_date && p.pay_date !== '-').forEach(p => {
                let key = p.pay_date;
                if (!paidGroups[key]) paidGroups[key] = { total: 0, refs: [], items: [], maxPOTimestamp: 0, pay_desc: '' };
                let val = p.q_tt*p.p_tt + p.q_tb*p.p_tb + p.q_tj*p.p_tj;
                paidGroups[key].total += val;
                paidGroups[key].refs.push(p.ref);
                if (p.pay_desc) paidGroups[key].pay_desc = p.pay_desc;
                
                let poTimestamp = p.sort || new Date(p.date).getTime();
                if (poTimestamp > paidGroups[key].maxPOTimestamp) {
                    paidGroups[key].maxPOTimestamp = poTimestamp;
                }

                paidGroups[key].items.push({
                    date: p.date,
                    val: val
                });
            });

            // Add payment events — sort right after the latest PO in that payment group
            Object.keys(paidGroups).forEach(payDate => {
                // Sort items chronologically by transaction date
                paidGroups[payDate].items.sort((a, b) => new Date(a.date).getTime() - new Date(b.date).getTime());
                
                // Sort immediately after its latest paid PO (maxPOTimestamp + 1 second)
                let sortKey = paidGroups[payDate].maxPOTimestamp + 1000;

                events.push({
                    date: payDate,
                    sortKey: sortKey,
                    ref: '',
                    val: paidGroups[payDate].total,
                    noteCount: paidGroups[payDate].refs.length,
                    refs: paidGroups[payDate].refs,
                    items: paidGroups[payDate].items,
                    pay_desc: paidGroups[payDate].pay_desc,
                    type: 'bayar'
                });
            });

            // Sort events by date (oldest first)
            events.sort((a, b) => a.sortKey - b.sortKey);

            // Step 2: Calculate running balance on the entire sorted array
            let runningBalance = 0;
            events.forEach(e => {
                if (e.type === 'bayar') {
                    runningBalance -= e.val;
                } else {
                    runningBalance += e.val;
                }
                e.runningBalance = runningBalance;
            });

            // Take last 30 events for rendering
            events = events.slice(-30);

            let containerStyle = forUI
                ? `background: white; padding: 25px; border-radius: 20px; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05); width: 100%; max-width: 600px; font-size: 14px; text-align: left; font-family: 'Inter', sans-serif; border: 1px solid var(--border);`
                : `margin-top: 25px; padding-top: 20px; border-top: 2px dashed rgba(0,0,0,0.08); font-size: 13px; text-align: left; font-family: 'Inter', sans-serif;`;

            let html = `<div id="${reportId}" style="${containerStyle}">`;

            if (forUI) {
                html += `<h3 style="margin-bottom: 15px; font-family: 'Outfit', sans-serif; color: var(--primary); text-align:center;">Mutasi Hutang</h3>`;
            }

            // Summary
            let unpaidCount = pos.filter(p => !p.is_lunas).length;
            let unpaidTotal = pos.filter(p => !p.is_lunas).reduce((s, p) => s + (p.q_tt*p.p_tt + p.q_tb*p.p_tb + p.q_tj*p.p_tj), 0);
            html += `<div style="font-weight: 800; margin-bottom: 12px; color: #e11d48; font-size: 15px;">Sisa hutang: Rp ${formatIDR(unpaidTotal)} (${unpaidCount} nota belum dibayar)</div>`;

            // Table header
            html += `<div style="display: flex; justify-content: space-between; font-size: 10px; color: #94a3b8; font-weight: 700; text-transform: uppercase; padding: 4px 0; border-bottom: 2px solid #e2e8f0; margin-bottom: 4px;">
                        <span style="flex:2">Tanggal</span>
                        <span style="flex:2">Keterangan</span>
                        <span style="flex:1; text-align:right">Jumlah</span>
                        <span style="flex:1.5; text-align:right">Sisa Hutang</span>
                     </div>`;

            // Mutation rows
            events.forEach(e => {
                let formattedDate = e.type === 'bayar' ? e.date : formatDate(e.date).split(' | ')[0];

                if (e.type === 'bayar') {
                    let itemsHtml = '';
                    if (e.items && e.items.length > 0) {
                        itemsHtml = `<ul style="list-style: none; margin-top: 4px; padding-left: 0; font-size: 10px; color: #15803d; font-weight: 400;">`;
                        e.items.forEach(item => {
                            itemsHtml += `<li style="margin-top: 1px;">- ${formatShortDate(item.date)} - Rp ${formatIDR(item.val)}</li>`;
                        });
                        itemsHtml += `</ul>`;
                    }
                    html += `<div style="display: flex; justify-content: space-between; align-items: center; padding: 6px 0; border-bottom: 1px dashed #e2e8f0; font-size: 12px; background: #f0fdf4;">
                                <span style="flex:2"><span style="color: #166534; font-size: 10px; font-weight:700;">${formattedDate}</span></span>
                                <span style="flex:2; display: flex; flex-direction: column; color:#166534;">
                                    <span style="font-weight:700;">${e.pay_desc || `Bayar ${e.noteCount} nota`}</span>
                                    ${itemsHtml}
                                </span>
                                <span style="flex:1; text-align:right; color:#166534; font-weight:700;">-Rp${formatIDR(e.val)}</span>
                                <span style="flex:1.5; text-align:right; font-weight:700;color:#166534;">Rp${formatIDR(Math.max(0, e.runningBalance))}</span>
                             </div>`;
                } else {
                    let statusBadge = e.is_lunas
                        ? `<span style="font-size:9px; color:#166534; background:#dcfce7; padding:1px 5px; border-radius:4px; margin-left:5px; font-weight:700;">✓ LUNAS</span>`
                        : '';
                    let rowColor = e.is_lunas ? 'color:#64748b;' : 'color:#e11d48;';
                    let refStyle = e.is_lunas ? 'color:#94a3b8;' : 'color:#1e293b;';
                    html += `<div style="display: flex; justify-content: space-between; align-items: center; padding: 3px 0; border-bottom: 1px solid rgba(0,0,0,0.03); font-size: 12px;">
                                <span style="flex:2"><span style="color: #94a3b8; font-size: 10px;">${formattedDate}</span></span>
                                <span style="flex:2; display: flex; flex-direction: column; ${refStyle}">
                                    <span style="font-weight:600;">${e.ref}${statusBadge}</span>
                                    ${e.details ? `<span style="font-size:10px; color:#64748b; font-weight:400; margin-top:2px; text-decoration:none; display:inline-block;">${e.details}</span>` : ''}
                                </span>
                                <span style="flex:1; text-align:right; font-weight:600;${rowColor}">+Rp${formatIDR(e.val)}</span>
                                <span style="flex:1.5; text-align:right; font-weight:600;color:#1e293b;">Rp${formatIDR(Math.max(0, e.runningBalance))}</span>
                             </div>`;
                }
            });

            // Footer
            html += `<div style="display: flex; justify-content: space-between; padding: 8px 0; margin-top: 8px; border-top: 2px solid #e2e8f0; font-weight: 800; font-size: 14px;">
                        <span>Sisa hutang akhir</span>
                        <span style="color: #e11d48;">Rp ${formatIDR(unpaidTotal)}</span>
                     </div>`;

            html += `</div>`;
            return html;
        }

        async function copyScreenshot(idx) {
            const card = document.getElementById(`card-${idx}`);
            try {
                const canvas = await generateCardCanvas(card);
                
                canvas.toBlob(async blob => {
                    try {
                        await navigator.clipboard.write([new ClipboardItem({ [blob.type]: blob })]);
                        Swal.fire({ icon: 'success', title: 'Berhasil Disalin!', timer: 1000, showConfirmButton: false, toast: true, position: 'top-end' });
                    } catch(e) {
                        Swal.fire('Error', 'Gagal copy ke clipboard', 'error');
                    }
                });

                const base64 = canvas.toDataURL('image/jpeg', 0.9);
                const po = pos[idx];
                await fetch('?api=save', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ po: po, image: base64 })
                });
            } catch (err) { 
                Swal.fire('Error', 'Gagal menyalin gambar', 'error'); 
            }
        }

        function lunasiPO(idx) {
            const po = pos[idx];
            po.is_lunas = true;
            const d = new Date();
            const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            const m = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            po.pay_date = `${days[d.getDay()]}, ${d.getDate()} ${m[d.getMonth()]} ${d.getFullYear()}`;
            
            savePOToBackendDebounced(po);
            renderPOs();
            
            setTimeout(() => {
                copyScreenshot(idx);
            }, 100);
        }

        const STORAGE_KEY = 'mitra_telur_multi_po_v2';
        if (pos.length === 0) {
            const saved = localStorage.getItem(STORAGE_KEY);
            if (saved) {
                try {
                    const localPos = JSON.parse(saved);
                    if (localPos && localPos.length > 0) {
                        pos = localPos;
                        pos.forEach(p => savePOToBackend(p));
                    }
                } catch(e) {}
            }
            
            if (pos.length === 0) {
                const initPO = defaultPO();
                pos.push(initPO);
                renderPOs();
                savePOToBackend(initPO);
            } else {
                renderPOs();
            }
        } else {
            renderPOs();
        }
        
        pos.forEach(po => {
            if (po.needs_image) {
                delete po.needs_image;
                savePOToBackend(po);
            }
        });

        Object.values(inputs).forEach(input => {
            input.addEventListener('input', updateData);
            if(input.type === 'checkbox') {
                input.addEventListener('change', (e) => {
                    if(e.target.checked) setTodayPay();
                    updateData();
                });
            }
        });

        window.onclick = function(event) {
            const modal = document.getElementById('edit-modal');
            if (event.target == modal) closeModal();
        }
    </script>
</body>
</html>