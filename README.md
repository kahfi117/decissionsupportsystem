# 🚀 Panduan Instalasi Laravel dari GitHub

## 📌 Bagian 1: Menjalankan Laravel dari GitHub (Tanpa Docker)

### 1️⃣ Clone Repository dari GitHub
```sh
git clone https://github.com/username/repository.git myproject
cd myproject
```
*(Ganti `username/repository` dengan repositori GitHub Anda.)*

---

### 2️⃣ Instal Dependensi Laravel
```sh
composer install
```

Jika ada kesalahan terkait `php8.3`, pastikan versi PHP yang aktif adalah **8.3+** dengan:
```sh
php -v
```

---

### 3️⃣ Konfigurasi File `.env`
1. Duplikat file `.env.example` menjadi `.env`
   ```sh
   cp .env.example .env  # Mac/Linux
   copy .env.example .env  # Windows (CMD)
   ```
2. **Buka `.env` dan konfigurasi database**  
   *(Ganti sesuai database Anda)*
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=mydatabase
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. **Generate Key**
   ```sh
   php artisan key:generate
   ```

---

### 4️⃣ Jalankan Migrasi Database (Opsional)
```sh
php artisan migrate
```
Jika ada error saat migrasi, periksa konfigurasi database di `.env`.

---

### 5️⃣ Jalankan Laravel
```sh
php artisan serve
```
Akses Laravel di: **[http://127.0.0.1:8000](http://127.0.0.1:8000)** 🚀

---

## 📌 Bagian 2: Menjalankan Laravel dari GitHub (Dengan Docker Sail)
> **Syarat**: Docker & Docker Compose harus sudah terinstal

### 1️⃣ Clone Repository
```sh
git clone https://github.com/username/repository.git myproject
cd myproject
```

---

### 2️⃣ Jalankan Laravel Sail
1. **Aktifkan Laravel Sail (Jika belum ada)**
   ```sh
   composer install
   php artisan sail:install
   ```
   Pilih database yang akan digunakan (contoh: MySQL).

2. **Jalankan Laravel dengan Docker Sail**
   ```sh
   ./vendor/bin/sail up -d
   ```
   *(Gunakan `sail up -d` untuk menjalankan di latar belakang.)*

3. **Akses Laravel**
   ```sh
   http://localhost
   ```

---

### 3️⃣ Jalankan Migrasi Database
```sh
./vendor/bin/sail artisan migrate
```

---

## 📌 Troubleshooting

**1️⃣ Jika `php` tidak terdeteksi saat menjalankan `composer install`**
```sh
export PATH="/usr/local/bin/php:$PATH"
```

**2️⃣ Jika `sail` tidak dikenali**
```sh
alias sail='bash vendor/bin/sail'
```

---

## 📌 Kesimpulan
| Metode | Perintah Utama |
|--------|---------------|
| **Tanpa Docker** | `php artisan serve` |
| **Dengan Docker** | `./vendor/bin/sail up -d` |

Silakan pilih metode yang sesuai dengan kebutuhan Anda! 🚀
