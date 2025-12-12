# 🚀 Laravel Base Template

A comprehensive Laravel 12 starter template with modern packages, interactive console commands, and automated setup tools. Built with the latest Laravel framework and Tailwind CSS v4, this template provides everything you need to quickly bootstrap a modern web application.

![Laravel Version](https://img.shields.io/badge/Laravel-12.25.0-red.svg)
![Tailwind CSS](https://img.shields.io/badge/Tailwind%20CSS-4.1.12-blue.svg)
![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-purple.svg)
![License](https://img.shields.io/badge/license-MIT-green.svg)

## ✨ Features

### 🎯 Core Framework
- **Laravel 12.25.0** - Latest stable Laravel framework
- **Tailwind CSS 4.1.12** - Modern utility-first CSS framework with native CSS approach
- **PEST 3.0** - Modern PHP testing framework
- **Laravel Sanctum 4.0** - API authentication system
- **Vite 6.x** - Lightning-fast frontend build tool
- **Alpine.js** - Lightweight reactive framework

### 📦 Available via Console Commands
- **Spatie Packages** (Settings, Permissions, Media Library, Activity Log)
- **Livewire 3 + VOLT** - Reactive components with single-file components
- **Filament v4** - Beautiful admin panel
- **Laravel Blueprint** - Interactive model generator
- **Multi-tenancy, Socialite, Cashier, Telescope, Horizon, Octane** - And more!

### �️ Built-in Tools
- **One-Command Setup** - Complete installation with `laravel-base:install`
- **VS Code Integration** - Debug configurations and tasks
- **Interactive Installers** - Guided package installation
- **Test Data Generation** - Complete test suite with users and roles

---

## �🚀 Quick Start

### Method 1: One-Command Installation (Recommended)

```bash
# Clone the repository
git clone https://github.com/Kayrah87/Laravel-Base.git your-project
cd your-project

# Run the interactive installer
php artisan laravel-base:install
```

The interactive installer will:
- 📋 Copy `.env.example` to `.env`
- 🔑 Generate application key
- 📦 Install Composer dependencies
- 📦 Install NPM dependencies
- 🗄️ Run database migrations
- 👤 Create admin user
- 🏷️ Set application name
- 🎨 Build frontend assets

### Method 2: Manual Installation

```bash
# Clone the repository
git clone https://github.com/Kayrah87/Laravel-Base.git your-project
cd your-project

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Create database (SQLite by default)
touch database/database.sqlite

# Run migrations
php artisan migrate

# Build assets
npm run build
```

---

## 🛠️ Console Commands

### 🎯 Laravel Base Commands

#### One-Command Installation
```bash
# Interactive installation (recommended)
php artisan laravel-base:install

# Silent installation with custom options
php artisan laravel-base:install --name="My App" --user-name="John Doe" --user-email="admin@myapp.com" --user-password="secretpassword"

# Skip specific steps
php artisan laravel-base:install --skip-composer --skip-npm --skip-user
```

**Options:**
- `--name` - Application name (default: Laravel-Base)
- `--user-name` - Admin user name (default: Admin)
- `--user-email` - Admin user email (default: admin@example.com)
- `--user-password` - Admin user password (default: password)
- `--skip-composer` - Skip Composer installation
- `--skip-npm` - Skip NPM installation and asset building
- `--skip-user` - Skip creating admin user

### 📦 Package Installers

#### Spatie Packages
```bash
# Interactive installer (choose packages)
php artisan install:spatie-packages

# Install all Spatie packages
php artisan install:spatie-packages --all

# Install specific packages
php artisan install:spatie-packages --settings --permissions --media --logs
```

**Available Spatie Packages:**
- `--settings` - Laravel Settings (store app settings in database)
- `--permissions` - Laravel Permission (roles and permissions)
- `--media` - Laravel Media Library (file attachments)
- `--logs` - Laravel Activity Log (track user activities)

#### Livewire & Frontend
```bash
# Install Livewire 3 with VOLT
php artisan install:livewire --volt

# Setup Tailwind CSS 4 and frontend assets
php artisan install:frontend --tailwind4
```

#### Optional Packages
```bash
# Interactive installer for optional packages
php artisan install:optional
```

**Available Optional Packages:**
- **Spatie Multi Tenancy** - Build multi-tenant applications
- **Filament Admin Panel** - Beautiful admin interface
- **Laravel Socialite** - Social authentication
- **Laravel Cashier (Stripe)** - Subscription billing
- **Laravel Telescope** - Debug and monitoring tool
- **Laravel Horizon** - Queue monitoring dashboard
- **Laravel Octane** - Performance optimization
- **Laravel Blueprint** - Code generation from YAML

### 🏗️ Model & Code Generation

#### Blueprint Model Generator
```bash
# Interactive walkthrough for creating models
php artisan blueprint:walkthrough
```

**Features:**
- Guided model creation with relationships
- Auto-generate controllers, factories, seeders
- Create Livewire components
- Generate Filament resources (if installed)
- Build complete CRUD operations

#### Test Data Generation
```bash
# Create comprehensive test data
php artisan create:test-data

# Fresh migration with custom user count
php artisan create:test-data --fresh --users=50

# Create test data without wiping existing data
php artisan create:test-data --no-fresh
```

**Generates:**
- Admin user (`admin@example.com`, password: `password`)
- Manager users with management roles
- Regular users with basic permissions
- Complete roles and permissions structure
- Sample data for testing

---

## 🏗️ Development Environment

### VS Code Integration

This template includes complete VS Code configuration for Laravel development:

#### Debug Configuration
```bash
# Available debug configurations in VS Code:
# 1. "Launch Laravel Server (Port 8000)" - Basic Laravel server
# 2. "PHP Xdebug Listen" - For debugging with breakpoints
# 3. "Laravel with Xdebug (Port 8000)" - Laravel server with Xdebug enabled
```

#### Tasks
- **Laravel Serve** - Start development server
- **Open in Edge** - Launch browser automatically
- **Stop Laravel Server** - Stop running server

### Frontend Development

#### Tailwind CSS 4.x
```bash
# Development mode (with hot reloading)
npm run dev

# Production build
npm run build

# Watch for changes
npm run dev -- --watch
```

**Tailwind v4 Features:**
- CSS-first configuration with `@theme`
- Native CSS imports with `@import 'tailwindcss'`
- Built-in plugins with `@plugin`
- Better performance with Vite integration
- No PostCSS configuration needed

#### Asset Structure
```
resources/
├── css/
│   └── app.css          # Tailwind directives & custom styles
├── js/
│   ├── app.js           # Main JavaScript entry point
│   └── bootstrap.js     # Axios & CSRF setup
└── views/
    └── welcome.blade.php # Example with Vite integration
```

### Development Commands
```bash
# Start Laravel development server
php artisan serve

# Start Vite development server (hot reloading)
npm run dev

# Run tests
php artisan test

# Run tests with coverage
php artisan test --coverage

# Clear application caches
php artisan optimize:clear
```

---

## 📁 Project Structure

```
├── 📁 app/
│   ├── 📁 Console/Commands/     # ✨ Custom artisan commands
│   │   ├── 📄 LaravelBaseInstallCommand.php
│   │   ├── 📄 InstallSpatiePackages.php
│   │   ├── 📄 InstallLivewire.php
│   │   └── 📄 InstallOptionalPackages.php
│   ├── 📁 Http/
│   │   ├── 📁 Controllers/      # 🎮 Application controllers
│   │   └── 📁 Middleware/       # 🔒 HTTP middleware
│   ├── 📁 Models/               # 📊 Eloquent models
│   └── 📁 Providers/            # 🔧 Service providers
├── 📁 .vscode/                  # 🔧 VS Code configuration
│   ├── 📄 launch.json           # Debug configurations
│   ├── 📄 tasks.json            # Development tasks
│   └── 📄 settings.json         # Editor settings
├── 📁 config/                   # ⚙️ Laravel configuration
├── 📁 database/
│   ├── 📁 factories/            # 🏭 Model factories
│   ├── 📁 migrations/           # 📋 Database migrations
│   └── 📁 seeders/              # 🌱 Database seeders
├── 📁 resources/
│   ├── 📁 css/                  # 🎨 Tailwind CSS assets
│   ├── 📁 js/                   # ⚡ JavaScript assets
│   └── 📁 views/                # 👁️ Blade templates
├── 📁 routes/                   # 🗺️ Application routes
├── 📄 vite.config.js            # ⚡ Vite configuration
├── 📄 tailwind.config.js        # 🎨 Tailwind configuration (legacy)
└── 📄 package.json              # 📦 NPM dependencies
```

---

## 🔐 Authentication & Authorization

### Default Authentication System
- Built-in Laravel authentication with Sanctum
- Role-based access control using Spatie Permission
- Email verification support
- Password reset functionality

### 👥 Roles & Permissions
The template includes a complete roles and permissions system:

| Role | Permissions | Description |
|------|-------------|-------------|
| **Admin** | Full system access | Complete administrative control |
| **Manager** | User & content management | Can manage users and content |
| **Editor** | Content management only | Can create and edit content |
| **User** | Basic view permissions | Standard user access |

### 🔑 Default Users (after running `create:test-data`)
- **Admin**: `admin@example.com` / `password`
- **Manager**: Auto-generated with manager role
- **Users**: Auto-generated with user role

### 🛡️ Security Features
- CSRF protection enabled
- XSS protection via Laravel's built-in features
- SQL injection protection through Eloquent ORM
- Rate limiting on authentication routes
- Secure password hashing with bcrypt

---

## 🧪 Testing Framework

### PEST Testing
The template includes PEST 3.0 for modern PHP testing:

```bash
# Run all tests
php artisan test

# Run tests with coverage
php artisan test --coverage

# Run specific test file
php artisan test tests/Feature/LaravelBaseTest.php

# Run tests in parallel
php artisan test --parallel
```

### Test Structure
```
tests/
├── Feature/           # Feature tests (HTTP requests, database)
│   └── LaravelBaseTest.php
├── Unit/             # Unit tests (individual methods)
├── Pest.php          # PEST configuration
└── TestCase.php      # Base test case
```

### Available Test Helpers
- Database factories for all models
- Test data generation with `create:test-data`
- Authentication helpers
- API testing utilities

---

## � Package Information

### 🎯 Core Packages (Always Included)

| Package | Version | Description |
|---------|---------|-------------|
| **Laravel Framework** | 12.25.0 | Core Laravel framework |
| **Laravel Sanctum** | 4.0 | API authentication |
| **PEST** | 3.0 | Modern PHP testing |
| **Tailwind CSS** | 4.1.12 | Utility-first CSS framework |

### 📦 Spatie Packages (Optional)

| Package | Command Flag | Description |
|---------|--------------|-------------|
| **Laravel Settings** | `--settings` | Store application settings in database |
| **Laravel Permission** | `--permissions` | Associate users with roles and permissions |
| **Laravel Media Library** | `--media` | Attach files to Eloquent models |
| **Laravel Activity Log** | `--logs` | Log user activities and model changes |

### 🔧 Optional Enhancement Packages

| Package | Description | Use Case |
|---------|-------------|----------|
| **Spatie Multi Tenancy** | Multi-tenant applications | SaaS applications |
| **Filament Admin Panel** | Beautiful admin interface | Admin dashboards |
| **Laravel Socialite** | Social authentication | OAuth login |
| **Laravel Cashier** | Stripe subscription billing | Payment processing |
| **Laravel Telescope** | Application debugging | Development debugging |
| **Laravel Horizon** | Queue monitoring | Background job management |
| **Laravel Octane** | Performance optimization | High-performance apps |
| **Laravel Blueprint** | Code generation from YAML | Rapid development |

---

## 🚀 Production Deployment

### Build Process
```bash
# Install production dependencies
composer install --no-dev --optimize-autoloader

# Build frontend assets for production
npm run build

# Optimize Laravel for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Environment Setup
1. Set `APP_ENV=production` in `.env`
2. Set `APP_DEBUG=false` in `.env`
3. Configure your database settings
4. Set up proper file permissions
5. Configure your web server (Apache/Nginx)

### Performance Tips
- Enable OPcache in production
- Use Laravel Octane for enhanced performance
- Implement Redis for caching and sessions
- Use CDN for static assets
- Enable gzip compression

---

## 🤝 Contributing

We welcome contributions to make this Laravel Base template even better!

### How to Contribute
1. **Fork the repository**
   ```bash
   git fork https://github.com/Kayrah87/Laravel-Base.git
   ```

2. **Create your feature branch**
   ```bash
   git checkout -b feature/amazing-feature
   ```

3. **Make your changes**
   - Add new console commands
   - Improve existing functionality
   - Update documentation
   - Add tests for new features

4. **Test your changes**
   ```bash
   php artisan test
   npm run build
   ```

5. **Commit your changes**
   ```bash
   git commit -m 'Add amazing feature: description of what it does'
   ```

6. **Push to your branch**
   ```bash
   git push origin feature/amazing-feature
   ```

7. **Open a Pull Request**
   - Describe what your changes do
   - Include screenshots if applicable
   - Reference any related issues

### 📝 Contribution Guidelines
- Follow PSR-12 coding standards
- Include tests for new functionality
- Update documentation for new commands
- Keep commit messages descriptive
- Ensure backwards compatibility

---

## � Changelog

### v2.0.0 (Current)
- ✨ **Laravel 12.25.0** - Upgraded from Laravel 11
- ✨ **Tailwind CSS 4.1.12** - Major upgrade with CSS-first approach
- ✨ **One-Command Installation** - New `laravel-base:install` command
- ✨ **VS Code Integration** - Complete debug and task configuration
- ✨ **Interactive Installers** - All package installers now interactive
- 🐛 **Bug Fixes** - Various stability improvements

### v1.0.0
- 🎉 Initial release with Laravel 11
- 📦 Spatie package installers
- 🎨 Tailwind CSS 3.x setup
- 🧪 PEST testing framework

---

## �📜 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

### MIT License Summary
- ✅ Commercial use allowed
- ✅ Modification allowed
- ✅ Distribution allowed
- ✅ Private use allowed
- ❌ No warranty provided
- ❌ No liability accepted

---

## 🙏 Acknowledgments

Special thanks to these amazing projects and maintainers:

### 🏗️ Core Framework
- **[Laravel Team](https://laravel.com)** - For the incredible Laravel framework
- **[Tailwind Labs](https://tailwindcss.com)** - For the utility-first CSS framework

### 📦 Package Maintainers
- **[Spatie](https://spatie.be)** - For their excellent Laravel packages
- **[Livewire Team](https://livewire.laravel.com)** - For reactive components
- **[Filament Team](https://filamentphp.com)** - For the beautiful admin panel
- **[PEST Team](https://pestphp.com)** - For modern PHP testing

### 🚀 Build Tools
- **[Vite Team](https://vitejs.dev)** - For lightning-fast build tooling
- **[Alpine.js](https://alpinejs.dev)** - For lightweight reactivity

### 👥 Community
- All contributors who help improve this template
- Laravel community for inspiration and feedback
- Open source maintainers who make this possible

---

<div align="center">

## 🌟 Star this repository if it helped you!

**Built with ❤️ for the Laravel community**

[⭐ Star](https://github.com/Kayrah87/Laravel-Base) • [🐛 Report Bug](https://github.com/Kayrah87/Laravel-Base/issues) • [✨ Request Feature](https://github.com/Kayrah87/Laravel-Base/issues) • [💬 Discussions](https://github.com/Kayrah87/Laravel-Base/discussions)

---

**Happy coding! 🚀**

*Made with Laravel 12 & Tailwind CSS 4*

</div>