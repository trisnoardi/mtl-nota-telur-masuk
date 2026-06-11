# Tasks.md — App Nota Telur Masuk

> **Session ID:** 20260611-2336-mtl-nota-telur-masuk
> **Last Updated:** 11 Juni 2026 23:36

---

## ✅ SELESAI

### [✅ SELESAI] Task 1: Inisialisasi Git & Struktur Project
- ✅ Init git repo dengan remote `https://github.com/trisnoardi/mtl-nota-telur-masuk.git`
- ✅ Parent gitignore diupdate (exclude sub-project)
- ✅ Branch `main` dengan initial commit
- ✅ `.gitignore` untuk project sendiri
- ✅ `.opencode/agents/` — project_expert.md & nota-telur-masuk-admin.md
- ✅ `.opencode/skills/` — session-manager, open-browser, print-nota
- ✅ `ARCHITECTURE.md`, `AGENTS_RULES.md`, `AGENTS.md`
- ✅ `Tasks.md` — file ini

### [✅ SELESAI] Task 2: Update URL Browser ke localhost:8000
- ✅ Semua skill & agent menggunakan URL `http://localhost:8000/mitra-telur-premium/app-nota-telur-masuk/nota-telur-masuk.php`
- ✅ Aturan: WAJIB buka otomatis di Chrome setiap kali CREATE/UPDATE/DELETE

### [✅ SELESAI] Task 3: Always Push
- ✅ Semua aturan git di semua file: WAJIB commit + WAJIB push setelahnya
- ✅ Setiap alur CRUD di admin agent: `COMMIT & PUSH`
- ✅ Command template: `git add -A && git commit -m "..." && git push`

### [✅ SELESAI] Task 4: Custom Global Agent @PurchaseAdmin
- ✅ Agent `@PurchaseAdmin` dibuat di global opencode agents (`C:\Users\trisn\.config\opencode\agents\`)
- ✅ Terdaftar di `opencode.json` dengan model `opencode-go/deepseek-v4-flash`
- ✅ Bisa dipanggil dari project mana pun
- ✅ Referensi ditambahkan di `AGENTS.md` project ini

### [✅ SELESAI] Task 3: Create PO Jumat 5 Juni 2026
- ✅ Data: Jumbo 5 tray, Tanggung 14 tray, Besar 10 tray
- ✅ Source: Peternakan UD Mitra Ilahi (ikuti data terakhir)
- ✅ Harga: Tanggung Rp52.000, Besar Rp54.000, Jumbo Rp57.000/tray
- ✅ Status: Belum Lunas (unpaid)
- ✅ REF: PO-YSRHDD
- ✅ File: `unpaid/1781193312456.json`
- ✅ Commit: `bf45a26`

### [✅ SELESAI] Task 4: Konversi Semua Data ke Tray & Perbaiki PHP
- ✅ Semua data lama (ikat) dikonversi ke tray (×6, karena 1 ikat = 6 tray)
- ✅ PHP formula `q*6*p` dihapus → jadi `q*p` (q dalam tray, p per tray)
- ✅ Display: `X tray (Y ikat)` atau `X tray (Y ikat Z tray)`
- ✅ Knowledge: 1 tray = 30 butir, 1 ikat = 6 tray
- ✅ Dokumen: project_expert, admin agent, session-manager skill diupdate
- ✅ Commit: `bf45a26`

### [✅ SELESAI] Task 5: Create PO Kamis 4 Juni 2026
- ✅ Data: Jumbo 12 tray
- ✅ Source: Peternakan UD Mitra Ilahi
- ✅ Harga: Jumbo Rp57.000/tray
- ✅ Status: Belum Lunas (unpaid)
- ✅ REF: PO-HI1M5T
- ✅ File: `unpaid/1781194809832.json`
- ✅ Total: Rp684.000
- ✅ Commit: `d580101`

### [✅ SELESAI] Task 6: Create PO Minggu 7 Juni 2026
- ✅ Data: Besar 12 tray, Tanggung 13 tray, Jumbo 5 tray
- ✅ Source: Peternakan UD Mitra Ilahi
- ✅ Harga lama: Tanggung Rp52k, Besar Rp54k, Jumbo Rp57k/tray
- ✅ Status: Belum Lunas (unpaid)
- ✅ REF: PO-OD78LZ
- ✅ Total: Rp1.609.000
- ✅ Commit: `eb120a4`

### [✅ SELESAI] Task 7: Kenaikan Harga per 9 Juni 2026

### [✅ SELESAI] Task 8: Urut PO by Tanggal + Format Hari pada Tanggal
- ✅ Sorting PO: `pos.sort((a, b) => new Date(b.date) - new Date(a.date))` — urut berdasarkan tanggal transaksi
- ✅ `formatDate()`: output jadi `"Senin, 6 Jun 2026 | 23:30"` (nama hari + bulan singkat 3 huruf)
- ✅ `setTodayPay()` & `lunasiPO()`: pakai format hari + tanggal + bulan singkat
- ✅ Commit: `7166e7a`

### [✅ SELESAI] Task 9: PO Senin-Rabu + Kenaikan Harga
- ✅ **Harga baru mulai 8 Juni 2026:** Tanggung Rp54.000, Besar Rp56.000, Jumbo Rp58.000/tray
- ✅ **Senin, 8 Juni** — PO-2D561C: Besar 25, Tanggung 15 = **Rp2.210.000**
- ✅ **Selasa, 9 Juni** — PO-7SA8ZV: Tanggung 7, Besar 18, Jumbo 5 = **Rp1.676.000**
- ✅ **Rabu, 10 Juni** — PO-T24JZY: Tanggung 14, Besar 12, Jumbo 6 = **Rp1.860.000**
- ✅ Commit: `3eb91da`

---

## PRIORITAS TERTINGGI

Belum ada task PENDING. Project siap digunakan.

---

## NEXT

Tunggu instruksi dari @ceo atau user untuk task selanjutnya.
