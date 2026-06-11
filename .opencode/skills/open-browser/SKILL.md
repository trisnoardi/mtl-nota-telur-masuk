---
description: 'Open Browser — Membuka app nota-telur-masuk.php di Chrome untuk verifikasi visual'
---

# Skill: Open Browser (App Nota Telur Masuk)

## 📋 Deskripsi
Skill untuk membuka aplikasi PO Pro di browser Chrome. Digunakan untuk verifikasi visual setelah perubahan data.

## 📂 Resource
- **Chrome Path**: `C:\Program Files\Google\Chrome\Application\chrome.exe`
- **App URL**: `http://localhost:8000/mitra-telur-premium/app-nota-telur-masuk/nota-telur-masuk.php`

## 🔍 Cara Pakai

### Buka App (Daftar Semua PO)
```powershell
Start-Process -FilePath "C:\Program Files\Google\Chrome\Application\chrome.exe" -ArgumentList "http://localhost:8000/mitra-telur-premium/app-nota-telur-masuk/nota-telur-masuk.php"
```

## ⚠️ Aturan
1. ✅ Gunakan `Start-Process` dengan full path Chrome
2. ✅ **WAJIB panggil SETIAP kali setelah CREATE/UPDATE/DELETE data PO** — buka otomatis tanpa perlu diminta user
3. ✅ Pastikan server PHP sudah berjalan di `http://localhost:8000/`
