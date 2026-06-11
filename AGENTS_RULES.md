# Aturan Agent — App Nota Telur Masuk

> **Path:** `G:\www\Ongoing\mitra-telur-premium\app-nota-telur-masuk`

---

## 1. Aturan Dasar

1. **Jangan edit `nota-telur-masuk.php`** tanpa instruksi eksplisit dari user atau @ceo.
2. **Semua data PO adalah file JSON** — edit langsung file JSON, jangan melalui API.
3. **Format JSON WAJIB konsisten** — ikuti format yang sudah ada.
4. **Jangan hapus field** dari JSON — biarkan null/"" jika tidak ada data.
5. **WAJIB verifikasi di browser** setiap selesai create/update/delete PO.
6. **WAJIB commit** setiap selesai perubahan.
7. **WAJIB `git push`** setelah setiap commit — jangan pernah lupa push.

## 2. Aturan Git

- Remote: `https://github.com/trisnoardi/mtl-nota-telur-masuk.git`
- Branch: `main`
- Format commit: `feat:`, `fix:`, `docs:`, `chore:`, `init:`, `refactor:`
- Contoh: `feat: add PO from UD Mitra Ilahi 11 Juni`
- Contoh: `fix: update payment status PO-A1B2C3 to lunas`
- **Setiap commit WAJIB diikuti `git push origin main`**

## 3. Aturan Data

1. **Folder `paid/`** — untuk PO dengan `is_lunas: true`
2. **Folder `unpaid/`** — untuk PO dengan `is_lunas: false`
3. **Satu file JSON** = satu PO
4. **File JPG** adalah screenshot invoice (auto-generated oleh html2canvas)
5. Jika mengubah status lunas, **PINDAHKAN** file antar folder

## 4. Aturan Kolaborasi

1. **@project_expert** — untuk info path, arsitektur, format data
2. **@nota-telur-masuk-admin** — agent utama untuk CRUD PO
3. **@session-manager** — skill untuk operasi CRUD PO
4. **@print-nota / @open-browser** — skill untuk verifikasi di browser

## 5. dilarang

- ❌ **Jangan** edit `nota-telur-masuk.php` tanpa instruksi
- ❌ **Jangan** backup/hapus file tanpa konfirmasi user
- ❌ **Jangan** tanya "Apa yang harus saya lakukan?" — baca `Tasks.md`
