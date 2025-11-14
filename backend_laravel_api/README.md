# ByCrousty Backend API

A comprehensive Laravel-based REST API for the ByCrousty application featuring user authentication, admin panel, QR code generation, and comprehensive user management system.

## 🚀 Overview

This is a full-featured backend API built with Laravel 12.x that provides:
- Complete user authentication system with email verification
- Admin panel with user management capabilities
- QR code generation and management for each user
- Login tracking and audit logs
- RESTful API endpoints with Sanctum authentication
- Comprehensive testing suite

## ✨ Features

### Authentication & Security
- **User Registration & Login**: Complete registration with validation and secure login
- **Email Verification**: Email verification system with signed URLs
- **Password Reset**: Secure password reset via email tokens
- **API Token Authentication**: Laravel Sanctum for secure API access
- **Role-Based Access**: User and admin role system
- **Login Logging**: Track and audit all login attempts

### QR Code System
- **Unique QR Codes**: Automatically generated for each user upon registration
- **Immutable Codes**: QR codes cannot be changed once generated for security
- **Encrypted Data**: QR codes contain encrypted user information
- **API Access**: Retrieve QR codes as PNG images via API endpoints
- **Admin Management**: Admin panel to view and manage all QR codes

### Admin Panel
- **Web-Based Dashboard**: Complete admin interface with Bootstrap UI
- **User Management**: View, edit, update, and delete users
- **QR Code Display**: View and download QR codes for any user
- **Login History**: Track user login patterns and history
- **Responsive Design**: Mobile-friendly admin interface

### API Features
- **RESTful Design**: Clean, consistent API endpoints
- **JSON Responses**: Structured JSON responses with proper HTTP status codes
- **Rate Limiting**: Built-in rate limiting for security
- **CORS Support**: Configured for cross-origin requests
- **Error Handling**: Comprehensive error handling and validation

## 🏗️ Project Structure

```
backend_laravel_api/
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/v1/           # API controllers
│   │   │   ├── AuthController.php
│   │   │   └── LogController.php
│   │   └── Admin/            # Admin panel controllers
│   │       ├── AdminController.php
│   │       └── AdminAuthController.php
│   ├── Models/
│   │   ├── User.php          # User model with QR code integration
│   │   ├── LoginLog.php      # Login tracking model
│   │   └── PasswordResetToken.php
│   ├── Services/
│   │   └── QrCodeService.php # QR code generation and management
│   └── Http/Middleware/
│       └── AdminMiddleware.php # Admin authentication middleware
├── database/
│   ├── migrations/           # Database schema
│   └── seeders/             # Sample data generators
├── resources/views/admin/    # Admin panel Blade templates
├── routes/
│   ├── api.php              # API routes
│   └── web.php              # Web routes (admin panel)
└── tests/                   # Comprehensive test suite
```

## 🛠️ Installation

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js and npm
- MySQL/PostgreSQL/SQLite database

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd backend_laravel_api
   ```

2. **Install Dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Configuration**
   
   Update your `.env` file with database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=bycrousty_db
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

5. **Run Migrations**
   ```bash
   php artisan migrate
   ```

6. **Seed Database (Optional)**
   ```bash
   php artisan db:seed
   ```

7. **Build Assets**
   ```bash
   npm run build
   ```

### Quick Setup (One Command)
```bash
composer run setup
```

## 🚦 Running the Application

### Development Mode
```bash
# Start all services (API, Queue, Logs, Frontend)
composer run dev

# Or start individually:
php artisan serve              # API server
npm run dev                   # Frontend assets
php artisan queue:listen      # Background jobs
```

### Production Mode
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## 📚 API Documentation

### Base URL
```
http://localhost:8000/api/v1
```

### Authentication Endpoints

#### Register User
```http
POST /api/v1/register
Content-Type: application/json

{
    "full_name": "John Doe",
    "username": "johndoe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response:**
```json
{
    "user": {
        "id": 1,
        "full_name": "John Doe",
        "username": "johndoe",
        "email": "john@example.com",
        "qr_code_url": "http://localhost:8000/api/v1/users/my-qr-code"
    },
    "token": "1|abc123...",
    "verification_url": "http://localhost:8000/api/v1/verify/1/hash123"
}
```

#### Login
```http
POST /api/v1/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

#### Password Reset Request
```http
POST /api/v1/forgot
Content-Type: application/json

{
    "email": "john@example.com"
}
```

#### Password Reset
```http
POST /api/v1/reset
Content-Type: application/json

{
    "token": "reset_token_here",
    "email": "john@example.com",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

### Protected Endpoints (Require Authentication)

#### Get User Profile
```http
GET /api/v1/profile
Authorization: Bearer {token}
```

#### Get User's QR Code
```http
GET /api/v1/users/my-qr-code
Authorization: Bearer {token}
```

#### Get Login History
```http
GET /api/v1/users/{id}/login-logs
Authorization: Bearer {token}
```

#### Logout
```http
POST /api/v1/logout
Authorization: Bearer {token}
```

## 🔧 Admin Panel

Access the admin panel at: `http://localhost:8000/admin`

### Admin Features
- **Dashboard**: Overview of system statistics
- **User Management**: Complete CRUD operations for users
- **QR Code Management**: View and download QR codes
- **Login Monitoring**: Track user login patterns
- **User Profile Views**: Detailed user information with QR codes

### Admin Routes
- `/admin` - Login page
- `/admin/dashboard` - Main dashboard
- `/admin/users` - User management
- `/admin/users/{id}` - User details with QR code
- `/admin/users/{id}/edit` - Edit user information

## 🎯 QR Code System

### Features
- **Automatic Generation**: QR codes generated on user registration
- **Unique & Immutable**: Each user gets a unique, unchangeable QR code
- **Encrypted Content**: Contains encrypted user information
- **Multiple Formats**: Available as PNG images
- **Admin Access**: Viewable and downloadable from admin panel

### QR Code Data Structure
```json
{
    "user_id": 1,
    "email": "john@example.com",
    "username": "johndoe",
    "unique_id": "uuid-string",
    "generated_at": "2025-11-07T10:30:00.000000Z",
    "app": "ByCrousty"
}
```

### Usage
```php
// Generate QR code
$qrCodeService = new QrCodeService();
$qrData = $qrCodeService->generateQrCodeData($userId, $email, $username);
$qrImage = $qrCodeService->generateQrCodeImage($qrData);

// Decode QR code
$decodedData = $qrCodeService->decodeQrCodeData($qrData);
```

## 🧪 Testing

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suites
```bash
# Authentication tests
php artisan test tests/Feature/AuthControllerTest.php

# QR Code tests  
php artisan test tests/Feature/QrCodeTest.php

# Run with coverage
php artisan test --coverage
```

### Test Structure
- **Feature Tests**: End-to-end API testing
- **Unit Tests**: Individual component testing
- **Database Tests**: Migration and seeder testing

## 🔧 Configuration

### Environment Variables
```env
# Application
APP_NAME=ByCrousty
APP_ENV=local
APP_KEY=base64:...
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bycrousty_db

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password

# Sanctum
SANCTUM_STATEFUL_DOMAINS=localhost,127.0.0.1
```

## 📦 Dependencies

### Production Dependencies
- **Laravel Framework 12.x**: Core framework
- **Laravel Sanctum 4.x**: API authentication
- **Endroid QR Code 6.x**: QR code generation
- **Laravel Tinker**: REPL for Laravel

### Development Dependencies
- **PHPUnit**: Testing framework
- **Laravel Pint**: Code formatting
- **Faker**: Test data generation
- **Laravel Sail**: Docker development environment
- **Vite**: Frontend build tool
- **TailwindCSS**: CSS framework for admin panel

## 🚀 Deployment

### Production Checklist
1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false`
3. Configure production database
4. Set up mail server configuration
5. Configure proper file permissions
6. Set up SSL certificate
7. Configure web server (Apache/Nginx)

### Deployment Commands
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

## 🛡️ Security Features

- **Password Hashing**: Bcrypt encryption for passwords
- **API Rate Limiting**: Prevent brute force attacks
- **CSRF Protection**: Cross-Site Request Forgery protection
- **SQL Injection Prevention**: Eloquent ORM protection
- **XSS Protection**: Input sanitization and validation
- **Email Verification**: Prevent fake account creation
- **Secure Password Reset**: Time-limited, secure tokens

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/new-feature`)
3. Commit your changes (`git commit -am 'Add new feature'`)
4. Push to the branch (`git push origin feature/new-feature`)
5. Create a Pull Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 📞 Support

For support, email support@bycrousty.com or create an issue in the repository.

---

**Built with ❤️ using Laravel**
