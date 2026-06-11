# Agent Initialization: App Nota Telur Masuk

> **Project:** App Nota Telur Masuk (PO Pro)
> **Root Path:** `G:\www\Ongoing\mitra-telur-premium\app-nota-telur-masuk`
> **Git Remote:** `https://github.com/trisnoardi/mtl-nota-telur-masuk.git`
> **Updated:** 2026-06-11

---

## 1. Project Identity

| Atribut | Nilai |
|---------|-------|
| **Nama** | App Nota Telur Masuk |
| **Deskripsi** | Single-file PHP app untuk mencatat Purchase Order telur masuk dari supplier |
| **Path** | `G:\www\Ongoing\mitra-telur-premium\app-nota-telur-masuk` |
| **Main Project** | `G:\www\Ongoing\mitra-telur-premium` (Mitra Telur Premium) |
| **Server** | `http://localhost:8000/` (XAMPP / server lokal) |
| **App URL** | `http://localhost:8000/mitra-telur-premium/app-nota-telur-masuk/nota-telur-masuk.php` |

## 2. Agents & Skills

### 2.1. Agent Khusus Project

| Agent | File | Fungsi |
|-------|------|--------|
| **`@project_expert`** | `.opencode/agents/project_expert.md` | Informasi project, arsitektur, path, format data |
| **`@nota-telur-masuk-admin`** | `.opencode/agents/nota-telur-masuk-admin.md` | Agent utama pengelola PO (spesifik project) |
| **`@PurchaseAdmin`** | Global bawaan opencode | Agent global admin purchase — bisa dipakai di project mana pun |

### 2.2. Skill yang Tersedia

| Skill | Fungsi |
|-------|--------|
| **`@session-manager`** | CRUD PO (Purchase Order) dari file JSON |
| **`@open-browser`** | Buka app di Chrome |
| **`@print-nota`** | Verifikasi hasil edit di browser |

## 3. Wajib Dibaca Sebelum Bekerja

| Dokumen | Isi | Lokasi |
|---------|-----|--------|
| **`ARCHITECTURE.md`** | Arsitektur project & struktur direktori | Root |
| **`AGENTS_RULES.md`** | Aturan kerja agent | Root |
| **`AGENTS.md`** | File ini — inisialisasi | Root |
| **`.opencode/agents/project_expert.md`** | Informasi path & format data | `.opencode/agents/` |
| **`.opencode/agents/nota-telur-masuk-admin.md`** | Agent admin utama | `.opencode/agents/` |

## 4. Cara Kerja

1. **BACA** data PO dari user (source, desc, qty, harga, status lunas)
2. **BUAT/EDIT** file JSON di `paid/` atau `unpaid/`
3. **VERIFIKASI** di browser (`http://localhost:8000/mitra-telur-premium/app-nota-telur-masuk/nota-telur-masuk.php`) — WAJIB buka otomatis setiap kali ada perubahan
4. **COMMIT & PUSH** ke git (`git add -A && git commit -m "..." && git push`)

## 5. Alur CRUD

### CREATE
- Generate `id` = `Date.now()` (timestamp ms)
- Generate `ref` = `PO-XXXXXX` (random 6 char)
- Simpan JSON ke folder sesuai status lunas

### READ
- Baca file JSON dari `paid/` atau `unpaid/`
- Tampilkan data ke user

### UPDATE
- Edit field JSON
- Jika status lunas berubah, pindahkan file antar folder
- Hapus file JPG (akan regenerate otomatis)

### DELETE
- Konfirmasi ke user dulu
- Hapus file JSON + JPG

## 6. Penting

- **Jangan edit `nota-telur-masuk.php`** tanpa instruksi eksplisit
- **Semua data adalah file JSON** — edit langsung, jangan via API
- **Selalu commit + push** setelah satu pekerjaan selesai
- Jika ragu tentang aturan, baca `AGENTS_RULES.md` atau konsultasi dengan `@project_expert`
