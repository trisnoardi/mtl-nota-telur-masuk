---
description: 'Session Manager — Buat, edit, hapus PO (Purchase Order) telur masuk di app-nota-telur-masuk'
---

# Skill: Session Manager (PO File-Based)

## 📋 Deskripsi
Skill untuk mengelola file **PO (Purchase Order) telur masuk** di `app-nota-telur-masuk`.
Semua operasi dilakukan dengan mengedit file JSON langsung — **tidak ada database**.

## 📂 Path
- **Direktori**: `G:\www\Ongoing\mitra-telur-premium\app-nota-telur-masuk`
- **Data lunas**: `.../paid/` → folder untuk PO yang sudah dibayar
- **Data belum lunas**: `.../unpaid/` → folder untuk PO yang belum dibayar
- **Template format**: Lihat file JSON yang sudah ada

## 🔍 Operasi yang Didukung

### 1. Cari / Lihat Semua PO
```powershell
# Lihat semua file PO
Get-ChildItem -Path "G:\www\Ongoing\mitra-telur-premium\app-nota-telur-masuk\*\*.json"
```
Atau buka `http://localhost:8080/nota-telur-masuk.php` di browser.

### 2. Buat PO Baru
1. **Tentukan ID** = `[System.DateTimeOffset]::UtcNow.ToUnixTimeMilliseconds()`
2. **Generate REF** = `PO-` + random 6 karakter uppercase
3. **Buat object JSON** dengan format:
```json
{
    "id": 1778112625983,
    "date": "2026-06-11T23:30",
    "ref": "PO-A1B2C3",
    "source": "Peternakan UD Mitra Ilahi",
    "desc": "Diantarkan kak Indra",
    "q_tt": 3, "p_tt": 50000,
    "q_tb": 1, "p_tb": 55000,
    "q_tj": 1, "p_tj": 52000,
    "pay_date": "-",
    "is_lunas": false
}
```
> **⚠️ Satuan:** `q_tt/q_tb/q_tj` = **tray** (1 tray = 30 butir). `p_tt/p_tb/p_tj` = harga **per tray**. Rumus total = `q * p` (tidak ada *6).

4. **Simpan** ke folder `paid/` (jika lunas) atau `unpaid/` (jika belum)
5. **Verifikasi** di browser

### 3. Edit PO Existing
1. **Cari file** di `paid/` atau `unpaid/`:
   ```powershell
   Get-ChildItem -Path "G:\www\Ongoing\mitra-telur-premium\app-nota-telur-masuk\*\*{id}.json"
   ```
2. **Baca** file JSON
3. **Edit** field yang perlu diubah
4. **Jika `is_lunas` berubah** → pindahkan file antar folder
5. **Simpan** file
6. **Hapus file JPG** jika ada (akan regenerate otomatis via html2canvas)

### 4. Lunasi PO
1. Cari file di `unpaid/`
2. Edit: `is_lunas: true`, `pay_date: "11 Juni 2026"`
3. Pindahkan file JSON + JPG ke `paid/`
4. Verifikasi di browser

### 5. Hapus PO
```powershell
Remove-Item -Path "G:\www\Ongoing\mitra-telur-premium\app-nota-telur-masuk\{folder}\{id}.json"
Remove-Item -Path "G:\www\Ongoing\mitra-telur-premium\app-nota-telur-masuk\{folder}\{id}.jpg"
```
⚠️ WAJIB konfirmasi ke user sebelum hapus.

## ✅ Format Data PO
> **Satuan:** `q_tt/q_tb/q_tj` = **tray** (1 tray = 30 butir). Harga `p_tt/p_tb/p_tj` = **per tray**.

```json
{
    "id": 1778112625983,
    "date": "2026-06-11T23:30",
    "ref": "PO-A1B2C3",
    "source": "Nama Supplier",
    "desc": "Keterangan",
    "q_tt": 3, "p_tt": 50000,
    "q_tb": 1, "p_tb": 55000,
    "q_tj": 1, "p_tj": 52000,
    "pay_date": "11 Juni 2026",
    "is_lunas": false
}
```

## ⚠️ Aturan Penting
1. ✅ **EDIT file JSON langsung**
2. ✅ **Ikuti format persis** seperti contoh file yang sudah ada
3. ✅ **Jika pindah status lunas**, pindahkan file antar folder
4. ✅ **Jangan hapus field** — biarkan null/"" jika tidak ada data
5. ✅ **WAJIB verifikasi di browser** setelah buat/edit
6. ✅ **WAJIB commit + push** setelah perubahan
