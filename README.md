# AFMS - Sistem Manajemen Absensi Fingerprint

## 🚀 **Status Project**

Project ini sudah berhasil di-restructure dan dibersihkan dari git merge conflicts. Semua file sudah di-organize dengan baik.

## 📁 **Struktur Project**

```
afms-clean/
├── laravel/          # Backend Laravel API
├── nextjs/           # Frontend Next.js
├── docker/           # Docker configuration
├── nginx/            # Nginx configuration
└── database/         # Database schema
```

## 🔧 **Setup dan Installation**

### **Prerequisites:**
- Docker & Docker Compose
- Node.js 18+ (untuk development)
- PHP 8.1+ (untuk development)

### **1. Clone Repository:**
```bash
git clone https://github.com/Ilbasuttaqwa/afms-clean.git
cd afms-clean
```

### **2. Jalankan dengan Docker:**
```bash
# Development environment
docker-compose -f docker-compose.local.yml up -d

# Production environment
docker-compose up -d
```

### **3. Development Mode (tanpa Docker):**

#### **Laravel Backend:**
```bash
cd laravel
composer install
cp env.local .env
php artisan key:generate
php artisan migrate
php artisan serve
```

#### **Next.js Frontend:**
```bash
cd nextjs
npm install
npm run dev
```

## 🌐 **Access URLs**

- **Frontend:** http://localhost:3000
- **Backend API:** http://localhost:8000
- **Database:** localhost:3306 (MySQL)

## 📋 **Features**

### **✅ Sudah Selesai:**
- [x] Project structure cleanup
- [x] Git merge conflicts resolution
- [x] Docker configuration
- [x] Tailwind CSS setup
- [x] Icon component system
- [x] Prisma database schema
- [x] Authentication middleware
- [x] API routes structure

### **🔄 Dalam Progress:**
- [ ] Laravel backend setup
- [ ] Database migrations
- [ ] API endpoints testing
- [ ] Frontend components

### **📝 TODO:**
- [ ] Install Laravel dependencies
- [ ] Setup database connection
- [ ] Test API endpoints
- [ ] Complete frontend components
- [ ] Add authentication system
- [ ] Implement fingerprint device integration

## 🐛 **Known Issues**

1. **Linter Errors:** Beberapa file memiliki linter errors karena missing dependencies
2. **Dependencies:** Laravel dan Next.js dependencies belum di-install
3. **Database:** Connection dan migrations belum di-setup

## 🔧 **Troubleshooting**

### **Docker Issues:**
```bash
# Restart Docker Desktop
# Clear Docker cache
docker system prune -a

# Check container status
docker-compose ps
```

### **Port Conflicts:**
```bash
# Check port usage
netstat -ano | findstr :3306
netstat -ano | findstr :8000
netstat -ano | findstr :3000

# Kill process using port
taskkill /PID <PID> /F
```

## 📞 **Support**

Untuk bantuan lebih lanjut, silakan buat issue di repository ini.

## 📄 **License**

Project ini menggunakan license proprietary. Semua hak cipta dilindungi.

---

**Last Updated:** December 2024
**Version:** 1.0.0
**Status:** Development
