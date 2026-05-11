<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

---

# 🚀 Laravel 13 Base Project

Base project Laravel 13 dengan sistem manajemen user, role, permission, menu, dan object storage (MinIO) terintegrasi.

## 📋 Fitur Utama

- ✅ **User Management** - Manajemen user, roles, dan permissions
- ✅ **Role & Permission** - Access control berbasis role dan permission
- ✅ **Menu Management** - Dynamic menu system
- ✅ **MinIO Integration** - Object storage untuk file uploads
- ✅ **Redis Support** - Session dan cache management
- ✅ **MySQL Database** - Dengan migration dan seeding
- ✅ **Eloquent ORM** - Modern database interaction

## 🛠️ Tech Stack

- **Framework**: Laravel 13
- **Database**: MySQL/MariaDB
- **Cache & Session**: Redis
- **Storage**: MinIO (S3-compatible)
- **Frontend**: Blade Templates + Vite
- **Package Manager**: Composer & NPM

## 🚀 Quick Start

### Instalasi Cepat

```bash
# Clone repository
git clone <repository-url>
cd laravel13-base-project

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed
```

### Run Application

```bash
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Start asset compilation
npm run dev
```

Akses aplikasi di `http://localhost:8000`

## 📖 Dokumentasi Lengkap

Untuk panduan instalasi lengkap termasuk Redis dan MinIO, lihat: **[INSTALLATION.md](INSTALLATION.md)**

Panduan ini mencakup:
- ✅ Setup project dari awal
- ✅ Konfigurasi database
- ✅ Setup Redis
- ✅ Setup MinIO
- ✅ File upload dengan MinIO
- ✅ Troubleshooting

## 📁 Project Structure

```
laravel13-base-project/
├── app/
│   ├── Http/Controllers/       # Controllers
│   ├── Models/                 # Eloquent Models (User, Role, Permission, Menu)
│   ├── Services/
│   │   └── MinioService.php   # MinIO integration
│   └── Providers/              # Service providers
├── config/                     # Configuration files
├── database/
│   ├── migrations/            # Database migrations
│   ├── seeders/               # Database seeders
│   └── factories/             # Model factories
├── resources/
│   ├── views/                 # Blade templates
│   ├── js/                    # JavaScript files
│   └── css/                   # CSS files
├── routes/                     # Route definitions
├── storage/                    # Application storage
├── .env.example               # Environment template
├── composer.json              # PHP dependencies
├── package.json               # Node dependencies
└── INSTALLATION.md            # Detailed setup guide
```

## 🗄️ Database Schema

### Models
- **User** - Pengguna sistem
- **Role** - Role/jabatan
- **Permission** - Izin akses
- **Menu** - Menu navigation

### Relations
- User → has one Role
- Role ↔ has many Permissions
- Menu → can be assigned to Roles

## 🔧 Configuration

### Environment Variables

Konfigurasi utama di `.env`:

```env
# Database
DB_HOST=127.0.0.1
DB_DATABASE=l13_base_pro

# Redis (Session & Cache)
REDIS_HOST=127.0.0.1
SESSION_DRIVER=redis
CACHE_STORE=redis

# MinIO (Object Storage)
FILESYSTEM_DISK=minio
AWS_ENDPOINT=http://127.0.0.1:9000
AWS_BUCKET=dev
```

Lihat [INSTALLATION.md](INSTALLATION.md) untuk konfigurasi lengkap.

## 📤 File Upload dengan MinIO

Sistem sudah siap untuk file upload:

```php
use App\Services\MinioService;

class ProfileController extends Controller
{
    public function updateAvatar(Request $request, MinioService $minio)
    {
        $path = $minio->upload($request->file('avatar'), 'avatars');
        // Save to database...
    }
}
```

Tampilkan file:

```blade
<img src="{{ Storage::disk('minio')->url($user->avatar_path) }}" alt="Avatar">
```

## 🧪 Testing

```bash
# Run tests
php artisan test

# Run with code coverage
php artisan test --coverage
```

## 📚 Helpful Links

- [Laravel Documentation](https://laravel.com/docs)
- [MinIO Documentation](https://min.io/docs/minio/)
- [Redis Documentation](https://redis.io/docs/)
- [Installation Guide](INSTALLATION.md)

## 🤝 Contributing

Silakan berkontribusi dengan membuat pull request atau membuka issue untuk bug reports.

## 📝 License

Project ini menggunakan lisensi MIT. Lihat file LICENSE untuk detail.

## 📞 Support

Untuk bantuan atau pertanyaan, silakan hubungi tim development.

---

**Last Updated**: May 11, 2026  
**Version**: 1.0.0
