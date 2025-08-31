# AFMS - Attendance & Fingerprint Management System

Sistem manajemen kehadiran dan fingerprint yang modern dengan backend Laravel dan frontend Next.js.

## 🏗️ Struktur Project

```
afms-clean/
├── laravel-api/          # Backend Laravel API
├── components/           # Komponen React UI
├── pages/               # Halaman Next.js
├── styles/              # CSS dan TailwindCSS
├── lib/                 # Utility functions
└── types/               # TypeScript types
```

## 🚀 Setup Development

### 1. Backend Laravel

```bash
cd laravel-api

# Install dependencies
composer install

# Copy .env.example ke .env dan sesuaikan konfigurasi database
cp .env.example .env

# Generate application key
php artisan key:generate

# Konfigurasi database PostgreSQL di .env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=afms_db
DB_USERNAME=postgres
DB_PASSWORD=password

# Jalankan migration dan seeder
php artisan migrate:fresh --seed

# Jalankan server
php artisan serve
```

### 2. Frontend Next.js

```bash
# Install dependencies
npm install

# Jalankan development server
npm run dev
```

## 🗄️ Database PostgreSQL

Pastikan PostgreSQL sudah terinstall dan running:

```bash
# Buat database
createdb afms_db

# Atau via psql
psql -U postgres
CREATE DATABASE afms_db;
```

## 🔧 Konfigurasi

### Laravel (.env)
```env
APP_NAME="AFMS - Attendance & Fingerprint Management System"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=afms_db
DB_USERNAME=postgres
DB_PASSWORD=password

SANCTUM_STATEFUL_DOMAINS=localhost:3000
SESSION_DOMAIN=localhost
```

### Next.js
- API endpoint: `http://localhost:8000/api`
- Port: `3000`

## 📱 Fitur

- ✅ Dashboard modern dengan TailwindCSS
- ✅ Autentikasi Laravel Sanctum
- ✅ Manajemen karyawan
- ✅ Monitoring perangkat fingerprint
- ✅ Sistem payroll
- ✅ Laporan kehadiran
- ✅ Responsive design

## 🛠️ Tech Stack

### Backend
- Laravel 10
- PostgreSQL
- Laravel Sanctum
- PHP 8.1+

### Frontend
- Next.js 14
- React 18
- TypeScript
- TailwindCSS
- Heroicons

## 🚀 Deployment

### Laravel
```bash
# Production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Next.js
```bash
# Build production
npm run build

# Start production
npm start
```

## 📝 Catatan

- Pastikan PostgreSQL extension `pgsql` terinstall di PHP
- Gunakan Laravel Sanctum untuk autentikasi API
- Frontend menggunakan proxy ke backend Laravel
- Semua komponen UI menggunakan TailwindCSS

## 🤝 Kontribusi

1. Fork project
2. Buat feature branch
3. Commit changes
4. Push ke branch
5. Buat Pull Request

## 📄 License

MIT License
