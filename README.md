<div align="center">
  <h1 align="center">ToDoList Web & Mobile</h1>
  <h3>Aplikasi Manajemen Tugas Modern, Responsive, dan Open Source</h3>
</div>

<div align="center">
  <a href="https://github.com/username/ToDoList/stargazers"><img alt="GitHub Repo stars" src="https://img.shields.io/github/stars/username/ToDoList"></a>
  <a href="https://github.com/username/ToDoList/blob/main/LICENSE"><img alt="License" src="https://img.shields.io/badge/license-MIT-green"></a>
</div>

<br/>

ToDoList adalah aplikasi manajemen tugas berbasis web dan mobile yang memudahkan kamu mengatur, menambah, mengedit, dan memantau progres tugas harian secara modern dan responsif.  
Aplikasi ini cocok untuk pelajar, mahasiswa, maupun pekerja yang ingin produktif dan terorganisir.

---

## ✨ Fitur Utama

- **Manajemen Tugas:** Tambah, edit, hapus, dan tandai tugas sebagai selesai.
- **Progress Ring & Statistik:** Lihat progres tugas dengan visual yang menarik.
- **Dark Mode:** Tampilan nyaman di mata, bisa diaktifkan/dimatikan.
- **Notifikasi Toast:** Feedback instan setiap aksi berhasil.
- **Custom Scrollbar & Animasi:** UI lebih modern dan interaktif.
- **Multi Platform:** Tersedia versi web (Laravel Blade) & mobile (Flutter).
- **Autentikasi:** Register, login, dan logout user.
- **Responsive:** Nyaman diakses dari HP maupun desktop.

---

## 🗂️ Struktur Halaman

- **Login & Register:** Autentikasi user.
- **Halaman Utama (Dashboard):** Daftar tugas, progress, statistik, dan quote motivasi.
- **Tambah Tugas:** Form tambah tugas baru.
- **Edit Tugas:** Form edit tugas yang sudah ada.
- **API:** Tersedia endpoint untuk mobile app (lihat folder `/api`).

---

## 🗄️ Database

- **MySQL**  
  Struktur tabel utama: `users`, `todos`  
  (Relasi: 1 user bisa punya banyak todo)

---

## ⚙️ Software & Teknologi

- **Backend:** Laravel 10 (PHP)
- **Frontend Web:** Blade, TailwindCSS, FontAwesome, Poppins Font
- **Frontend Mobile:** Flutter (Dart)
- **Database:** MySQL
- **Library lain:** SharedPreferences (mobile), HTTP (mobile)

---

## 🚀 Cara Instalasi

### 1. Clone Repository

```sh
git clone https://github.com/fitrahnurivanto/ToDoList.git
cd ToDoList
```

### 2. Instalasi Web (Laravel)

```sh
cd website
composer install
cp .env.example .env
# Edit .env untuk koneksi database MySQL
php artisan key:generate
php artisan migrate
php artisan serve
```

### 3. Instalasi Mobile (Flutter)

```sh
cd mobile
flutter pub get
flutter run
```

---

## ▶️ Cara Menjalankan

- **Web:**  
  Jalankan `php artisan serve`, lalu buka [http://localhost:8000](http://localhost:8000)
- **Mobile:**  
  Jalankan `flutter run` di folder mobile, atau build APK untuk Android.

---

## 🎬 Demo

### Web Demo
bisa diliat di youtube ( https://youtu.be/TkUwPNdnANU )

---

## 👤 Identitas Pembuat

- **Nama:** Fitrah Nur Ivanto
- **No:** 15
- **Kelas:** XI RPL 1
- **Kontak:** fitrahnurivanto@gmail.com

---

> Aplikasi ini dibuat untuk pembelajaran dan tugas sekolah
> Silakan gunakan, modifikasi, dan kembangkan sesuai kebutuhan!
