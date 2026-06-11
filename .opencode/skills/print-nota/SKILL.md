---
description: 'Print Nota — Membuka app PO Pro di Chrome untuk verifikasi hasil edit'
---

# Skill: Print Nota (App Nota Telur Masuk)

## 📋 Deskripsi
Skill untuk membuka aplikasi PO Pro di Chrome setelah selesai melakukan perubahan data.
Digunakan untuk verifikasi bahwa perubahan sudah benar secara visual.

## 📂 Path
- **Direktori**: `G:\www\Ongoing\mitra-telur-premium\app-nota-telur-masuk`
- **Chrome Path**: `C:\Program Files\Google\Chrome\Application\chrome.exe`

## 🔍 Cara Pakai

### Buka App Utama
```powershell
Start-Process -FilePath "C:\Program Files\Google\Chrome\Application\chrome.exe" -ArgumentList "http://localhost:8000/mitra-telur-premium/app-nota-telur-masuk/nota-telur-masuk.php"
```

## ⚠️ Aturan
1. ✅ **WAJIB panggil SETIAP kali setelah CREATE/UPDATE/DELETE data PO** — buka otomatis tanpa perlu diminta user
2. ✅ Biarkan Chrome tetap terbuka agar user bisa melihat hasilnya
3. ✅ Gunakan URL `http://localhost:8000/mitra-telur-premium/app-nota-telur-masuk/nota-telur-masuk.php`
