# App Nota Telur Masuk — Arsitektur

> **Path:** `G:\www\Ongoing\mitra-telur-premium\app-nota-telur-masuk`
> **Aplikasi:** PO (Purchase Order) Incoming Invoice untuk Mitra Telur Premium

---

## 1. Gambaran Umum

Single-file PHP application untuk mencatat Purchase Order telur masuk dari supplier. Semua data tersimpan dalam file JSON + JPG — **tidak ada database**.

## 2. Struktur Direktori

```
app-nota-telur-masuk/
├── nota-telur-masuk.php         # [INTI] Single-file app: API + UI + CSS + JS
├── run-server.bat               # Script untuk menjalankan PHP built-in server
├── nota-telur-masuk.url         # Shortcut ke localhost
├── .gitignore                   
├── ARCHITECTURE.md              # File ini — arsitektur direktori
├── AGENTS_RULES.md              # Aturan & konvensi lokal
├── AGENTS.md                    # Instruksi inisialisasi agent
├── .opencode/
│   ├── agents/
│   │   ├── project_expert.md
│   │   └── nota-telur-masuk-admin.md
│   └── skills/
│       ├── session-manager/     # CRUD PO dari file JSON
│       ├── open-browser/        # Buka app di Chrome
│       └── print-nota/          # Verifikasi visual di browser
├── paid/                        # PO yang sudah LUNAS
│   ├── {id}.json                # Data PO
│   └── {id}.jpg                 # Screenshot invoice (auto-generated)
└── unpaid/                      # PO yang BELUM LUNAS
    ├── {id}.json
    └── {id}.jpg
```

## 3. Cara Kerja

### 3.1. Server
- Server lokal: `http://localhost:8000/` (XAMPP / PHP built-in server)
- Akses: `http://localhost:8000/mitra-telur-premium/app-nota-telur-masuk/nota-telur-masuk.php`
- **WAJIB** buka URL ini otomatis di Chrome setiap kali ada perubahan data PO

### 3.2. Data Flow
1. User input data PO via form modal di browser
2. `nota-telur-masuk.php` menyimpan data sebagai file JSON di folder `paid/` atau `unpaid/`
3. Screenshot invoice otomatis di-generate via html2canvas (JS library)
4. Gambar disimpan sebagai JPG di folder yang sama

### 3.3. API
Semua endpoint via `nota-telur-masuk.php?api=`:

| Endpoint | Method | Input | Output |
|----------|--------|-------|--------|
| `?api=save` | POST | `{ po: {...}, image: "base64..." }` | `{ success: true }` |
| `?api=delete` | POST | `{ id: 123 }` | `{ success: true }` |

### 3.4. Format Data PO
```json
{
    "id": 1778112625983,
    "date": "2026-06-11T23:30",
    "ref": "PO-A1B2C3",
    "source": "Peternakan UD Mitra Ilahi",
    "desc": "Diantarkan kak Indra",
    "q_tt": 3,    "p_tt": 50000,
    "q_tb": 1,    "p_tb": 55000,
    "q_tj": 1,    "p_tj": 52000,
    "pay_date": "11 Juni 2026",
    "is_lunas": false
}
```

### 3.5. Perhitungan Total
```
Total = (q_tt * 6 * p_tt) + (q_tb * 6 * p_tb) + (q_tj * 6 * p_tj)
```
Dimana 1 ikat = 6 butir telur.

## 4. Teknologi
- **PHP** murni (tanpa framework) — backend API + HTML rendering
- **Vanilla JS** — frontend interaksi (modal, form, screenshot)
- **html2canvas** (CDN) — generate screenshot invoice
- **SweetAlert2** (CDN) — notifikasi popup
- **Google Fonts** — Inter + Outfit
- **CSS Custom Properties** — theming warna

## 5. Agent & Skill
- **Agent utama:** `@nota-telur-masuk-admin`
- **Project Expert:** `@project_expert` — info path, arsitektur, format data
- **Skills:** `session-manager` (CRUD PO), `open-browser`, `print-nota`
