# 💹 DuitTracker - Personal Finance Management System

[![Laravel Version](https://img.shields.io/badge/Laravel-11.x-red.svg)](https://laravel.com)
[![TailwindCSS](https://img.shields.io/badge/Tailwind-3.x-blue.svg)](https://tailwindcss.com)
[![License](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)

**DuitTracker** adalah aplikasi manajemen keuangan pribadi berbasis web yang dirancang dengan fokus pada kesederhanaan, keamanan data, dan pengalaman pengguna yang modern. Dibangun menggunakan **Laravel 11** dengan pendekatan **Modern TALL Stack** (Tailwind CSS, Alpine.js, Laravel).

---

## ✨ Fitur Utama

### 🔐 Autentikasi & Keamanan Data
- **Multi-User Isolation**: Setiap pengguna memiliki ruang lingkup data pribadi. Transaksi antar pengguna dijamin tidak akan saling terlihat.
- **Secure Authentication**: Infrastruktur login dan registrasi yang aman berbasis Laravel Breeze.

### 📊 Visualisasi & Dashboard
- **Interactive Analytics**: Visualisasi perbandingan pemasukan dan pengeluaran menggunakan **Chart.js**.
- **Kalkulasi Saldo Real-time**: Perhitungan otomatis untuk saldo akhir, total pemasukan, dan total pengeluaran.

### 🎨 UI/UX Modern (Glassmorphism)
- **Premium Dark Mode**: Desain antarmuka gelap dengan efek *layered glass* dan tipografi *Plus Jakarta Sans*.
- **Skeleton Loading**: Animasi transisi halus untuk menghilangkan kesan kaku saat memuat data.
- **Toast Notifications**: Sistem notifikasi instan yang informatif untuk setiap interaksi pengguna.
- **Empty State Management**: Tampilan visual yang bersih saat riwayat transaksi masih kosong.

### ⚡ Efisiensi & Produktivitas
- **Quick Add Feature**: Tombol pintas untuk pencatatan transaksi frekuensi tinggi guna mempercepat input data.
- **Excel Export**: Kemampuan mengekspor riwayat transaksi ke format `.xlsx` melalui *Maatwebsite/Excel*.

---

## 🛠️ Stack Teknologi

| Komponen | Teknologi |
| :--- | :--- |
| **Backend** | Laravel 11 (PHP 8.3+) |
| **Database** | MySQL / MariaDB |
| **Frontend** | Tailwind CSS & Alpine.js |
| **Visualisasi** | Chart.js |
| **Export Tool** | Maatwebsite/Excel |

---

## 🚀 Panduan Instalasi

Lakukan langkah-langkah berikut untuk menjalankan proyek di lingkungan lokal:

1. **Clone Repositori**
   ```bash
   git clone [https://github.com/RafiSyaifta/duit-tracker.git](https://github.com/RafiSyaifta/duit-tracker.git)
   cd duit-tracker