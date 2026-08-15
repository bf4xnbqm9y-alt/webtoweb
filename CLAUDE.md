# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Proyek & Status

**Web-to-Web Builder** — platform no-code website builder: pengguna menyusun landing page secara visual, preview real-time, lalu mengunduh source code project dalam stack pilihan (Laravel+Blade, React+Vite, Vue+Vite, atau HTML statis).

Status: **Phase 1 (Proof of Concept - Development)** 
- **Landing Page Selesai & Modular:** Halaman `Welcome.vue` telah dipecah menjadi 5 sub-komponen utama di folder `resources/js/Components/Landing/`:
  1. `HeroPlayground.vue` (Simulator mockup playground interaktif, flat & stabil).
  2. `FeatureCarousel.vue` (Carousel bento cards horizontal dengan efek infinite looping scroll & focal blur highlight).
  3. `TemplateShowcase.vue` (Filter kategori tabs & grid template preview).
  4. `PricingSection.vue` (Tabel paket lisensi Free & Pro).
  5. `ContactSection.vue` (Detail kontak Email, WA, Telegram, serta formulir Kritik & Saran reaktif dengan success state transition).
- **Langkah Berikutnya:** Membangun antarmuka Workspace Visual Builder 3-kolom di halaman `/demo` (berkas [`Demo.vue`](file:///d:/laragon/www/webtoweb/resources/js/Pages/Builder/Demo.vue)).

Bahasa kerja proyek: **Indonesia** (komentar, UI, dan komunikasi).

## Commands

```bash
composer setup        # install, .env, key, migrate, npm install + build (setup awal)
composer dev          # php artisan dev (server + pail tailing)
npm run dev           # Vite dev server
npm run build         # build asset produksi
composer test         # config:clear + php artisan test
php artisan test --filter=BuilderController   # test tunggal
vendor/bin/pint       # format kode (Laravel Pint)
```

- DB default **SQLite** (`database/database.sqlite` / `db_webtoweb`); test memakai `:memory:`.
- Dev server Laragon: jalankan via `php artisan serve` atau `composer dev`, lalu `npm run dev` untuk hot reload Vite.

## Arsitektur

**Stack:** Laravel 13 · PHP 8.3 · Inertia.js + Vue 3 · Vite · Tailwind CSS v3 · SQLite · Breeze (auth) · Ziggy (route helper JS) · GSAP (`package.json`).

- **Frontend (Inertia SPA):** halaman di `resources/js/Pages/`, komponen reusable di `resources/js/Components/` (subfolder `Landing/` untuk komponen landing page). Semua route Inertia dirender lewat `app.js` → `HandleInertiaRequests`.
- **Backend:** controller ramping di `app/Http/Controllers/`; logic berat dipindah ke service (per `acuan.md`, generator zip di `app/Services/ZipGeneratorService.php`).
- **Route saat ini:** `/` (landing), `/demo` (builder PoC), `/dashboard`, `/profile`, dan auth Breeze.

### Peta Route yang Disepakati (`blueprint.md`)
`/` → `/register` (onboarding) → `/onboarding/step-1` & `/step-2` (pilih template) → `/builder/{project_slug}` (editor) → `/preview/{project_slug}` (live preview tab baru) → `/dashboard` (kelola + download ZIP).

### Skema JSON Layout (Single Source of Truth)
Format JSON desain user **harus konsisten** di editor, preview, dan generator ZIP:

```json
{
  "project_name": "Toko Kopi Apis",
  "theme": { "primary_color": "hsl(25, 60%, 45%)", "font_family": "Outfit" },
  "sections": [
    { "id": "sec-1", "type": "Navbar", "content": { "brand_name": "Kopi Apis", "links": [] } }
  ]
}
```

### Generator Engine (Strategy Pattern — belum diimplementasikan)
`ProjectGeneratorInterface` dengan implementasi `LaravelGenerator`, `ReactViteGenerator`, `VueViteGenerator`, `StaticHtmlGenerator`. Backend menyalin boilerplate dari `storage/app/boilerplates/{stack_name}`, menyisipkan HTML/CSS/JS hasil desain, lalu membungkus jadi `.zip`. **Jangan pernah** menduplikasi template HTML antara kanvas preview dan generator ZIP — keduanya harus memakai template file yang sama (render via iframe).

## Aturan Desain Wajib (`acuan.md`)

Anti look "AI generik" — jangan pakai warna Tailwind mentah (`bg-slate-900`, `indigo-600` polos).

- **Palet:** background off-white hangat `#FAF9F5` / `#F5F4F0`; teks utama `#111317`, sekunder `#57585F`; aksen obsidian `#0E1015` dan terracotta `#C2410C` / `#B45309`; border tipis `#E8E6E0` / `#E2E0D8`.
- **Detail:** `rounded-2xl`/`rounded-3xl`; latar putih solid `#FDFDFD` + shadow sangat halus (mis. `shadow-[0_4px_20px_rgba(0,0,0,0.02)]`); hover lift `-translate-y-0.5`; transisi `cubic-bezier(0.16, 1, 0.3, 1)`.
- **Animasi:** GSAP untuk entrance stagger (`y:20`, `opacity:0`, `power2.out`) dan smooth scroll (`power3.out`). **Jangan pakai WebGL/Three.js di landing page** — gunakan grid CSS/SVG + spotlight kursor radial-gradient (performa perangkat rendah).
- **Vue SFC:** dilarang tag `<style>`/`<script>` di dalam `<template>`; akses DOM ref setelah `onMounted`.
- **Performa:** playground mini-builder di hero harus berfungsi penuh client-side (state Vue `ref`, tanpa request server).

## Workflow Konvensi

- **CRITICAL RULE:** **JANGAN PERNAH menjalankan perintah terminal (seperti `npm run dev` atau `npm run build`)** karena user sudah menjalankan Vite dev server secara aktif di terminal mereka.
- **Sebelum mengubah berkas, konfirmasi/dapatkan persetujuan user dulu** — dan tanyakan kembali di setiap langkah besar.
- Di akhir setiap langkah, perbarui `task.md` dan `walkthrough.md`.
- Periksa integrasi menyeluruh: setiap fitur/ubah kode harus diperiksa dampaknya ke seluruh sistem (editor, preview, generator) agar tidak ada breaking change atau desinkronisasi.
