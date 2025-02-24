# 🚀 Panduan Instalasi Aplikasi dari GitHub

## 📌 Persyaratan Instalasi
### 🖥 Windows
- PHP 8.3 atau lebih tinggi ([Unduh PHP](https://windows.php.net/download/))
- Composer ([Unduh Composer](https://getcomposer.org/))
- Node.js & NPM ([Unduh Node.js](https://nodejs.org/))
- MySQL atau PostgreSQL
- Git ([Unduh Git](https://git-scm.com/downloads))

### 🍏 macOS
- Homebrew ([Instalasi Homebrew](https://brew.sh/))
- PHP 8.3 atau lebih tinggi (`brew install php`)
- Composer (`brew install composer`)
- Node.js & NPM (`brew install node`)
- MySQL atau PostgreSQL
- Git (`brew install git`)

### 🐧 Linux (Ubuntu/Debian)
- PHP 8.3 atau lebih tinggi (`sudo apt install php8.3 php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip unzip -y`)
- Composer (`curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer`)
- Node.js & NPM (`sudo apt install nodejs npm -y`)
- MySQL atau PostgreSQL (`sudo apt install mysql-server` atau `sudo apt install postgresql`)
- Git (`sudo apt install git -y`)

---

## 📌 Bagian 1: Menjalankan Aplikasi dari GitHub (Tanpa Docker)

### 1️⃣ Clone Repository dari GitHub
```sh
git clone https://github.com/kahfi117/decissionsupportsystem.git dss
cd dss
```
*(Ganti `username/repository` dengan repositori GitHub Anda.)*

---

### 2️⃣ Instal Dependensi Aplikasi
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

### 5️⃣ Jalankan Aplikasi
```sh
php artisan serve
```
Akses Aplikasi di: **[http://127.0.0.1:8000](http://127.0.0.1:8000)** 🚀

---

## 📌 Bagian 2: Menjalankan Aplikasi dari GitHub (Dengan Docker Sail)
> **Syarat**: Docker & Docker Compose harus sudah terinstal

### 1️⃣ Clone Repository
```sh
git clone https://github.com/kahfi117/decissionsupportsystem.git dss
cd dss
```

---

### 2️⃣ Jalankan Aplikasi Sail
1. **Aktifkan Aplikasi Sail (Jika belum ada)**
   ```sh
   composer install
   php artisan sail:install
   ```
   Pilih database yang akan digunakan (contoh: MySQL).

2. **Jalankan Aplikasi dengan Docker Sail**

   Buar Alias Sail
   ```sh
   alias sail='sh $([ -f sail ] && echo sail || echo vendor/bin/sail)'
   ```
   ```sh
   sail up -d
   ```
   *(Gunakan `sail up -d` untuk menjalankan di latar belakang.)*

4. **Akses Aplikasi**
   ```sh
   http://localhost
   ```

---

### 3️⃣ Jalankan Migrasi Database
```sh
sail artisan migrate
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
