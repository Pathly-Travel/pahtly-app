# Local Development with DDEV

Complete guide for setting up and running the Laravel DTO application locally using DDEV.

## 🐳 What is DDEV?

DDEV provides a consistent Docker-based development environment with:
- Same PHP, database versions for all developers
- Easy one-command setup
- Pre-configured for Laravel development
- Cross-platform support (macOS, Windows, Linux)

## 📋 Prerequisites

### Install Docker Runtime

You can use either Docker Desktop or OrbStack (recommended for macOS):

#### Option 1: Docker Desktop (Cross-platform)
- **macOS/Windows**: [Download Docker Desktop](https://www.docker.com/products/docker-desktop)
- **Linux**: [Install Docker Engine](https://docs.docker.com/engine/install/)

#### Option 2: OrbStack (macOS recommended)
- **macOS only**: [Download OrbStack](https://orbstack.dev/)
- **Benefits**: Faster startup, lower resource usage, better performance
- **Installation**: Download `.dmg` and drag to Applications

```bash
# Install via Homebrew
brew install orbstack

# Or download directly from https://orbstack.dev/
```

**Why OrbStack?** (macOS users)
- ⚡ **Faster**: 2-3x faster container startup
- 🔋 **Efficient**: Uses 50% less CPU and memory
- 🚀 **Performance**: Better file system performance
- 🔧 **Compatible**: Drop-in replacement for Docker Desktop

#### Comparison: Docker Desktop vs OrbStack

| Feature | Docker Desktop | OrbStack |
|---------|----------------|----------|
| **Platforms** | macOS, Windows, Linux | macOS only |
| **Resource Usage** | Higher CPU/Memory | 50% less resources |
| **Startup Time** | 30-60 seconds | 5-10 seconds |
| **File Sync** | Can be slow | Optimized |
| **Apple Silicon** | Good support | Native optimization |
| **Price** | Free for personal use | Free |
| **DDEV Compatibility** | ✅ Full support | ✅ Full support |

**Recommendation**: Use OrbStack on macOS for better performance, Docker Desktop for other platforms.

### Install DDEV

**macOS:**
```bash
brew install ddev/ddev/ddev
```

**Windows:**
```powershell
choco install ddev
```

**Linux:**
```bash
curl -fsSL https://raw.githubusercontent.com/ddev/ddev/master/scripts/install_ddev.sh | bash
```

## 🚀 Quick Start

### 1. Setup Project
```bash
# Clone and enter project
git clone <repository-url>
cd LaravelDTO

# Initialize DDEV
ddev config

# Start services
ddev start
```

### 2. Install Dependencies
```bash
# PHP dependencies
ddev composer install

# Node.js dependencies
ddev npm install

# Generate app key
ddev artisan key:generate
```

### 3. Database Setup
```bash
# Run migrations and seed
ddev artisan migrate:fresh --seed
```

### 4. Build Assets
```bash
# Development build
ddev npm run dev

# Or watch for changes
ddev npm run watch
```

### 5. Access Application
- **Main Site**: https://laraveldto.ddev.site
- **MailHog**: https://laraveldto.ddev.site:8026
- **Database**: localhost:3306 (from host)

## ⚡ DDEV Shortcuts

### Project Management
```bash
ddev start              # Start project
ddev stop               # Stop project
ddev restart            # Restart services
ddev describe           # Show URLs and info
ddev poweroff           # Stop all DDEV projects
```

### Laravel Artisan Shortcuts
```bash
# Database
ddev artisan migrate                   # Run migrations
ddev artisan migrate:fresh --seed     # Fresh DB with data
ddev artisan db:seed                  # Run seeders

# Cache Management
ddev artisan cache:clear              # Clear app cache
ddev artisan config:clear             # Clear config cache
ddev artisan route:clear              # Clear route cache
ddev artisan optimize:clear           # Clear all caches

# Development
ddev artisan tinker                   # Laravel REPL
ddev artisan route:list               # Show all routes
ddev artisan queue:work               # Start queue worker

# Testing
ddev artisan test                     # Run all tests
ddev artisan test --filter=UserTest   # Run specific test

# Code Generation
ddev artisan make:controller UserController
ddev artisan make:model User
ddev artisan make:migration create_users_table
```

### Composer Commands
```bash
ddev composer install                 # Install dependencies
ddev composer require package-name    # Add package
ddev composer update                  # Update packages
ddev composer dump-autoload           # Regenerate autoload
```

### NPM Commands
```bash
ddev npm install                      # Install Node dependencies
ddev npm run dev                      # Build for development
ddev npm run build                    # Build for production
ddev npm run watch                    # Watch and rebuild
ddev npm run lint                     # Run ESLint
ddev npm run format                   # Format with Prettier
```

## 🔧 Custom DDEV Commands

Create custom shortcuts in `.ddev/commands/web/`:

### Fresh Setup Command
```bash
# .ddev/commands/web/fresh
#!/bin/bash
## Description: Fresh Laravel installation
## Usage: fresh

php artisan migrate:fresh --seed
npm run build
echo "✅ Fresh installation complete!"
```

### Quality Check Command
```bash
# .ddev/commands/web/quality
#!/bin/bash
## Description: Run all quality checks
## Usage: quality

php artisan test
./vendor/bin/phpstan analyse
npm run lint
npm run format:check
echo "✅ Quality checks passed!"
```

### Setup Command
```bash
# .ddev/commands/web/setup
#!/bin/bash
## Description: Complete project setup
## Usage: setup

composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh --seed
npm run build
echo "🌐 Visit: https://laraveldto.ddev.site"
```

Make executable:
```bash
chmod +x .ddev/commands/web/*
```

## 🐛 Troubleshooting

### Common Issues

**DDEV won't start:**
```bash
# Check Docker is running
docker --version

# Check Docker/OrbStack status
docker info

# Restart DDEV
ddev poweroff && ddev start

# If using OrbStack, restart it
# OrbStack menu → Restart
```

**Port conflicts:**
```bash
# Check conflicts
ddev debug port-conflicts

# Stop conflicting services
sudo systemctl stop apache2  # Linux
sudo brew services stop nginx # macOS
```

**Database connection issues:**
```bash
# Check status
ddev describe

# Restart
ddev restart
```

**Permission issues (Linux/macOS):**
```bash
sudo chown -R $USER:$USER .
```

## 💡 Development Tips

### Daily Workflow
```bash
ddev start                    # Start your day
ddev npm run watch           # Watch assets (separate terminal)
ddev artisan queue:work      # Queue worker (if needed)
# ... develop ...
ddev stop                    # End of day
```

### Performance Optimization

#### For Docker Desktop Users
- Increase Docker resources (4GB+ RAM, 2+ CPU cores)
- Docker Desktop → Settings → Resources
- Enable Mutagen on macOS: `ddev config --mutagen-enabled`
- Use NFS mounts for better file sync performance

#### For OrbStack Users (macOS)
- OrbStack automatically optimizes resources
- No manual resource allocation needed
- File sync is already optimized
- Consider enabling Rosetta 2 if using Apple Silicon:
  ```bash
  # Check if Rosetta 2 is needed
  ddev debug capabilities
  
  # OrbStack automatically handles architecture translation
  ```

#### Additional Tips
- Use `ddev config --mutagen-enabled` for even better file sync on macOS
- Enable NFS mounts in `.ddev/config.yaml`:
  ```yaml
  nfs_mount_enabled: true
  ```

### Quality Assurance
```bash
# Run before committing
ddev quality                 # Custom quality command
# or manually:
ddev artisan test
ddev exec ./vendor/bin/phpstan analyse
ddev npm run lint
ddev npm run format:check
```

## 📊 Environment Details

### Database Connection (from host)
```
Host: 127.0.0.1
Port: 3306
Database: db
Username: db
Password: db
```

### Services
- **Web Server**: Nginx with PHP 8.2
- **Database**: MySQL 8.0
- **Node.js**: Version 20
- **Mail**: MailHog for email testing
- **Container Runtime**: Docker Desktop or OrbStack

### Container Performance
- **With Docker Desktop**: Standard Docker performance
- **With OrbStack**: Enhanced performance on macOS with faster startup and lower resource usage

## 📚 Additional Resources

- [DDEV Documentation](https://ddev.readthedocs.io/)
- [Laravel Documentation](https://laravel.com/docs)
- [Project README](../README.md) - Architecture overview
- [Testing Guide](./TESTING.md) - Testing strategies
- [Code Quality](./CODE_QUALITY.md) - Linting standards 