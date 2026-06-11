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
Start-Process -FilePath "C:\Program Files\Google\Chrome\Application\chrome.exe" -ArgumentList "http://localhost:8080/nota-telur-masuk.php"
```

### Mulai Server (Jika Belum Berjalan)
```powershell
Start-Process -FilePath "powershell" -ArgumentList "-NoExit -Command php -S localhost:8080" -WindowStyle Hidden
```

## ⚠️ Aturan
1. ✅ Panggil SETELAH setiap CREATE/UPDATE/DELETE data PO
2. ✅ Biarkan Chrome tetap terbuka agar user bisa melihat hasilnya
3. ✅ Gunakan URL `http://localhost:8080/nota-telur-masuk.php`
