# Local Development Guide

This guide provides comprehensive instructions for setting up and running the Laravel DTO application locally using DDEV (Docker Development Environment).

## 🐳 DDEV Overview

DDEV is a Docker-based local development environment that provides:
- ✅ **Consistent Environment**: Same PHP, database, and web server versions for all developers
- ✅ **Easy Setup**: One-command project initialization
- ✅ **Multiple Services**: Web server, database, mailhog, and more
- ✅ **Laravel Optimized**: Pre-configured for Laravel development
- ✅ **Cross-Platform**: Works on macOS, Windows, and Linux

## 📋 Prerequisites

### Required Software

1. **Docker Desktop**
   - **macOS**: [Download Docker Desktop](https://www.docker.com/products/docker-desktop)
   - **Windows**: [Download Docker Desktop](https://www.docker.com/products/docker-desktop)
   - **Linux**: [Install Docker Engine](https://docs.docker.com/engine/install/)

2. **DDEV**
   - We'll install this in the next section

### System Requirements

- **RAM**: Minimum 4GB, recommended 8GB+
- **Storage**: At least 2GB free space for Docker images
- **OS**: macOS 10.14+, Windows 10+, or modern Linux distribution

## 🚀 DDEV Installation

### macOS Installation

```bash
# Install using Homebrew (recommended)
brew install ddev/ddev/ddev

# Alternative: Install using curl
curl -fsSL https://raw.githubusercontent.com/ddev/ddev/master/scripts/install_ddev.sh | bash
```

### Windows Installation

```powershell
# Install using Chocolatey (recommended)
choco install ddev

# Alternative: Install using Scoop
scoop install ddev
```

### Linux Installation

```bash
# Install using the install script
curl -fsSL https://raw.githubusercontent.com/ddev/ddev/master/scripts/install_ddev.sh | bash

# Alternative: Download binary manually
# Visit: https://github.com/ddev/ddev/releases
```

### Verify Installation

```bash
# Check DDEV version
ddev version

# Expected output:
# DDEV version v1.22.0
```

## 🛠️ Project Setup

### 1. Clone the Repository

```bash
# Clone the project
git clone <repository-url>
cd LaravelDTO

# Or if you already have the project
cd LaravelDTO
```

### 2. Initialize DDEV

```bash
# Initialize DDEV for Laravel
ddev config

# DDEV will auto-detect Laravel and configure:
# - Project name: laraveldto (based on directory name)
# - Project type: laravel
# - PHP version: 8.2
# - Web server: nginx
# - Database: mysql:8.0
```

### 3. Start DDEV Environment

```bash
# Start all services
ddev start

# Expected output:
# Starting laraveldto...
# Successfully started laraveldto
# Project can be reached at https://laraveldto.ddev.site
```

### 4. Install Dependencies

```bash
# Install PHP dependencies
ddev composer install

# Install Node.js dependencies  
ddev npm install

# Generate application key
ddev artisan key:generate
```

### 5. Database Setup

```bash
# Run database migrations
ddev artisan migrate

# Seed the database (optional)
ddev artisan db:seed

# Or run migrations with seeding in one command
ddev artisan migrate:fresh --seed
```

### 6. Build Assets

```bash
# Build frontend assets for development
ddev npm run dev

# Or build and watch for changes
ddev npm run watch
```

## 🌐 Accessing Your Application

### Web Application
- **Main URL**: https://laraveldto.ddev.site
- **HTTP URL**: http://laraveldto.ddev.site

### Development Services
- **MailHog** (Email testing): https://laraveldto.ddev.site:8026
- **Database**: Accessible on localhost:3306 (from host)
- **PHPMyAdmin**: https://laraveldto.ddev.site:8037

### Database Connection

From your host machine (for database clients):
```
Host: 127.0.0.1
Port: 3306 (or check with `ddev describe`)
Database: db
Username: db  
Password: db
```

## ⚡ DDEV Shortcuts & Commands

### Essential DDEV Commands

```bash
# Project Management
ddev start              # Start the project
ddev stop               # Stop the project  
ddev restart            # Restart all services
ddev poweroff           # Stop all DDEV projects

# Project Information
ddev describe           # Show project details and URLs
ddev list               # List all DDEV projects
ddev logs               # Show container logs
ddev logs -f            # Follow logs in real-time
```

### Laravel Artisan Shortcuts

Instead of typing `ddev exec php artisan ...`, use these shortcuts:

```bash
# Basic Artisan Commands
ddev artisan            # Run artisan (shows available commands)
ddev artisan --version  # Check Laravel version

# Database Commands
ddev artisan migrate                    # Run migrations
ddev artisan migrate:fresh             # Drop all tables and re-run migrations
ddev artisan migrate:fresh --seed      # Fresh migrations + seeding
ddev artisan migrate:rollback          # Rollback last migration
ddev artisan migrate:status            # Check migration status

# Seeding Commands
ddev artisan db:seed                   # Run database seeders
ddev artisan db:seed --class=UserSeeder # Run specific seeder

# Cache Commands
ddev artisan cache:clear               # Clear application cache
ddev artisan config:clear              # Clear configuration cache
ddev artisan route:clear               # Clear route cache
ddev artisan view:clear                # Clear compiled views
ddev artisan optimize:clear            # Clear all Laravel caches

# Queue Commands
ddev artisan queue:work                # Start queue worker
ddev artisan queue:listen              # Listen for queue jobs
ddev artisan queue:restart             # Restart queue workers

# # Make Commands
# ddev artisan make:controller UserController    # Create controller
# ddev artisan make:model User                   # Create model
# ddev artisan make:migration create_users_table # Create migration
# ddev artisan make:seeder UserSeeder           # Create seeder
# ddev artisan make:request UserRequest         # Create form request

# Testing Commands
ddev artisan test                      # Run all tests
ddev artisan test --filter=UserTest    # Run specific test
ddev artisan test --coverage          # Run tests with coverage

# Development Commands  
# ddev artisan serve                     # Start development server (not needed with DDEV)
ddev artisan tinker                    # Laravel REPL
ddev artisan route:list                # Show all routes
ddev artisan schedule:run              # Run scheduled tasks
```

### Composer Shortcuts

```bash
# Composer Commands
ddev composer install                  # Install dependencies
ddev composer update                   # Update dependencies
ddev composer require spatie/laravel-data # Install package
ddev composer require --dev pest       # Install dev dependency
ddev composer dump-autoload            # Regenerate autoload files

# Common Composer Tasks
ddev composer install --optimize-autoloader --no-dev  # Production install
ddev composer show                     # List installed packages
ddev composer outdated                 # Check for outdated packages
```

### NPM/Node.js Shortcuts

```bash
# Node.js Commands
ddev npm install                       # Install Node dependencies
ddev npm run dev                       # Build assets for development
ddev npm run build                     # Build assets for production  
ddev npm run watch                     # Watch and rebuild assets
ddev npm run lint                      # Run ESLint
ddev npm run format                    # Format code with Prettier

# Package Management
ddev npm install vue@next              # Install npm package
ddev npm install --save-dev @types/node # Install dev dependency
ddev npm uninstall lodash              # Remove package
ddev npm update                        # Update all packages
```

## 🔧 Custom DDEV Configuration

### Extending DDEV Configuration

Create custom commands in `.ddev/commands/web/`:

#### Laravel Fresh Command
```bash
# .ddev/commands/web/fresh
#!/bin/bash
## Description: Fresh Laravel installation (migrate + seed)
## Usage: fresh
## Example: "ddev fresh"

php artisan migrate:fresh --seed
npm run build
echo "✅ Fresh Laravel installation complete!"
```

#### Quality Check Command
```bash
# .ddev/commands/web/quality
#!/bin/bash
## Description: Run all quality checks
## Usage: quality  
## Example: "ddev quality"

echo "🧪 Running tests..."
php artisan test

echo "🔍 Running PHPStan..."
./vendor/bin/phpstan analyse

echo "🎨 Checking formatting..."
npm run format:check

echo "🔍 Running ESLint..."
npm run lint

echo "✅ All quality checks passed!"
```

#### Setup Command
```bash
# .ddev/commands/web/setup
#!/bin/bash
## Description: Complete project setup
## Usage: setup
## Example: "ddev setup"

echo "🔧 Setting up Laravel DTO project..."

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate:fresh --seed

# Build assets
npm run build

echo "✅ Project setup complete!"
echo "🌐 Visit: https://laraveldto.ddev.site"
```

Make commands executable:
```bash
chmod +x .ddev/commands/web/*
```

### Environment Configuration

Customize `.ddev/config.yaml`:

```yaml
name: laraveldto
type: laravel
docroot: public
php_version: "8.2"
webserver_type: nginx-fpm
database:
  type: mysql
  version: "8.0"

# Additional services
use_dns_when_possible: true
composer_version: "2"
nodejs_version: "20"

# Custom PHP configuration
php_ini:
  max_execution_time: 300
  memory_limit: 512M
  upload_max_filesize: 100M

# Additional hostnames
additional_hostnames:
  - api.laraveldto.ddev.site

# Custom environment variables
web_environment:
  - LARAVEL_SAIL=0
  - DB_CONNECTION=mysql
  - DB_HOST=db
  - DB_PORT=3306
  - DB_DATABASE=db
  - DB_USERNAME=db
  - DB_PASSWORD=db
```

## 🐛 Troubleshooting

### Common Issues & Solutions

#### 1. DDEV Won't Start
```bash
# Check Docker is running
docker --version

# Reset DDEV
ddev poweroff
ddev start
```

#### 2. Port Conflicts
```bash
# Check what's using port 80/443
ddev debug port-conflicts

# Stop conflicting services
sudo systemctl stop apache2  # Linux
sudo brew services stop nginx # macOS
```

#### 3. Permission Issues
```bash
# Fix file permissions (Linux/macOS)
sudo chown -R $USER:$USER .

# For Windows, run Docker Desktop as Administrator
```

#### 4. Database Connection Failed
```bash
# Check database status
ddev describe

# Restart database
ddev restart
```

#### 5. Assets Not Building
```bash
# Clear npm cache
ddev npm cache clean --force

# Remove node_modules and reinstall
ddev exec rm -rf node_modules
ddev npm install
```

### Getting Help

```bash
# DDEV help
ddev help
ddev help start

# Debug information
ddev debug test
ddev debug capabilities

# Community support
# - GitHub: https://github.com/ddev/ddev
# - Discord: https://discord.gg/5wjP76mBJD
# - Documentation: https://ddev.readthedocs.io/
```

## 📊 Performance Tips

### Optimization Settings

1. **Increase Docker Resources**:
   - Docker Desktop → Settings → Resources
   - RAM: 4GB+ recommended
   - CPU: 2+ cores

2. **Enable Mutagen** (macOS performance):
   ```bash
   # Install mutagen
   brew install mutagen-io/mutagen/mutagen
   
   # Enable in DDEV
   ddev config --mutagen-enabled
   ddev restart
   ```

3. **NFS Mount** (better file sync):
   ```yaml
   # .ddev/config.yaml
   nfs_mount_enabled: true
   ```

### Development Workflow

```bash
# Daily development routine
ddev start                    # Start your day
ddev composer install        # Update dependencies if needed
ddev npm run watch           # Start asset watching
ddev artisan queue:work      # Start queue worker (separate terminal)

# End of day
ddev stop                    # Stop the project (saves resources)
```

## 🚀 Quick Start Checklist

- [ ] Install Docker Desktop
- [ ] Install DDEV
- [ ] Clone repository: `git clone <url>`
- [ ] Initialize DDEV: `ddev config`
- [ ] Start services: `ddev start`
- [ ] Install dependencies: `ddev composer install && ddev npm install`
- [ ] Setup environment: `ddev artisan key:generate`
- [ ] Run migrations: `ddev artisan migrate:fresh --seed`
- [ ] Build assets: `ddev npm run dev`
- [ ] Visit application: https://laraveldto.ddev.site

## 📚 Additional Resources

### DDEV Documentation
- [Official DDEV Documentation](https://ddev.readthedocs.io/)
- [Laravel with DDEV](https://ddev.readthedocs.io/en/stable/users/quickstart/#laravel)
- [DDEV Commands](https://ddev.readthedocs.io/en/stable/users/usage/commands/)

### Laravel Documentation
- [Laravel Installation](https://laravel.com/docs/installation)
- [Laravel Artisan Console](https://laravel.com/docs/artisan)
- [Laravel Database](https://laravel.com/docs/database)

### Project-Specific Resources
- [Main README](../README.md) - Project overview and architecture
- [Testing Guide](./TESTING.md) - Testing strategies and examples
- [Code Quality Guide](./CODE_QUALITY.md) - Linting and quality standards
- [API Documentation](./API.md) - API endpoints and usage 