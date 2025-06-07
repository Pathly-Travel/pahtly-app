# Deployment Guide

## Overview

This guide covers deployment strategies for the Laravel DTO application across different environments: development, staging, and production.

## Environment Setup

### Development Environment

#### Prerequisites

- PHP 8.2+
- Composer
- Node.js 18+ & npm
- Docker & Docker Compose (optional)

#### Local Setup

```bash
# Clone repository
git clone <repository-url>
cd LaravelDTO

# Install dependencies
composer install
npm install

# Environment configuration
cp .env.example .env
php artisan key:generate

# Database setup
touch database/database.sqlite
php artisan migrate
php artisan db:seed

# Start development server
composer dev
```

#### Docker Development

```bash
# Start all services
docker-compose up -d

# Install dependencies
docker-compose exec app composer install
docker-compose exec app npm install

# Setup application
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed

# Access application
http://localhost:8000
```

### Staging Environment

Staging should mirror production as closely as possible.

#### Server Requirements

- Ubuntu 22.04 LTS
- PHP 8.2 with required extensions
- Nginx
- MySQL 8.0
- Redis
- Supervisor (for queue workers)

#### Environment Configuration

```env
APP_NAME="Laravel DTO - Staging"
APP_ENV=staging
APP_DEBUG=false
APP_URL=https://staging.yourapp.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_dto_staging
DB_USERNAME=staging_user
DB_PASSWORD=secure_password

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

### Production Environment

#### Infrastructure Requirements

- **Application Server**: Ubuntu 22.04 LTS (minimum 2GB RAM, 2 CPU cores)
- **Database**: MySQL 8.0 (minimum 4GB RAM, 2 CPU cores)
- **Cache/Queue**: Redis 6.0+
- **Web Server**: Nginx 1.18+
- **SSL**: Let's Encrypt or commercial certificate
- **Monitoring**: Application and server monitoring
- **Backup**: Automated database and file backups

#### Server Configuration

**PHP Configuration (php.ini)**
```ini
memory_limit = 256M
max_execution_time = 30
upload_max_filesize = 10M
post_max_size = 10M
opcache.enable = 1
opcache.memory_consumption = 128
opcache.interned_strings_buffer = 8
opcache.max_accelerated_files = 4000
opcache.revalidate_freq = 60
opcache.fast_shutdown = 1
```

**Nginx Configuration**
```nginx
server {
    listen 80;
    server_name yourapp.com www.yourapp.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    server_name yourapp.com www.yourapp.com;
    root /var/www/laravel-dto/public;
    index index.php;

    # SSL Configuration
    ssl_certificate /etc/letsencrypt/live/yourapp.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/yourapp.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE-RSA-AES256-GCM-SHA384;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    # Static Files Caching
    location ~* \.(jpg|jpeg|png|gif|ico|css|js)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
    }

    # PHP-FPM Configuration
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Laravel Configuration
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Security
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Supervisor Configuration**
```ini
[program:laravel-queue-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/laravel-dto/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/laravel-dto/storage/logs/worker.log
stopwaitsecs=3600
```

## Deployment Strategies

### Manual Deployment

#### Production Deployment Script

```bash
#!/bin/bash

# deployment/deploy.sh
set -e

echo "Starting deployment..."

# Variables
DEPLOY_PATH="/var/www/laravel-dto"
BACKUP_PATH="/var/backups/laravel-dto"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Create backup
echo "Creating backup..."
mkdir -p $BACKUP_PATH
cp -r $DEPLOY_PATH $BACKUP_PATH/backup_$TIMESTAMP

# Pull latest code
echo "Pulling latest code..."
cd $DEPLOY_PATH
git pull origin main

# Install/update dependencies
echo "Installing dependencies..."
composer install --optimize-autoloader --no-dev
npm ci --production

# Build assets
echo "Building assets..."
npm run build

# Run Laravel optimizations
echo "Optimizing Laravel..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Clear application cache
echo "Clearing cache..."
php artisan cache:clear
php artisan queue:restart

# Set permissions
echo "Setting permissions..."
chown -R www-data:www-data $DEPLOY_PATH
chmod -R 755 $DEPLOY_PATH/storage
chmod -R 755 $DEPLOY_PATH/bootstrap/cache

# Restart services
echo "Restarting services..."
systemctl reload nginx
systemctl restart php8.2-fpm
supervisorctl restart laravel-queue-worker:*

echo "Deployment completed successfully!"
```

### Zero-Downtime Deployment

#### Blue-Green Deployment

```bash
#!/bin/bash

# deployment/blue-green-deploy.sh
set -e

CURRENT_PATH="/var/www/laravel-dto"
NEW_PATH="/var/www/laravel-dto-new"
BACKUP_PATH="/var/www/laravel-dto-backup"

echo "Starting blue-green deployment..."

# Clone current deployment
cp -r $CURRENT_PATH $NEW_PATH

# Update new deployment
cd $NEW_PATH
git pull origin main
composer install --optimize-autoloader --no-dev
npm ci --production
npm run build

# Laravel optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Run database migrations
php artisan migrate --force

# Health check
echo "Performing health check..."
if curl -f http://localhost:8081/health-check; then
    echo "Health check passed"
else
    echo "Health check failed, rolling back..."
    rm -rf $NEW_PATH
    exit 1
fi

# Atomic swap
echo "Swapping deployments..."
mv $CURRENT_PATH $BACKUP_PATH
mv $NEW_PATH $CURRENT_PATH

# Restart services
supervisorctl restart laravel-queue-worker:*
systemctl reload nginx

echo "Blue-green deployment completed!"
```

### Automated Deployment with GitHub Actions

#### CI/CD Pipeline

```yaml
# .github/workflows/deploy.yml
name: Deploy to Production

on:
  push:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: password
          MYSQL_DATABASE: testing
        ports:
          - 3306:3306
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
          extensions: dom, curl, libxml, mbstring, zip, pcntl, pdo, sqlite, pdo_sqlite, bcmath, soap, intl, gd, exif, iconv
          
      - name: Install Composer dependencies
        run: composer install --prefer-dist --no-interaction
        
      - name: Install NPM dependencies
        run: npm ci
        
      - name: Build assets
        run: npm run build
        
      - name: Copy environment file
        run: cp .env.example .env
        
      - name: Generate application key
        run: php artisan key:generate
        
      - name: Run database migrations
        run: php artisan migrate
        
      - name: Run tests
        run: php artisan test

  deploy:
    needs: test
    runs-on: ubuntu-latest
    if: github.ref == 'refs/heads/main'
    
    steps:
      - name: Deploy to server
        uses: appleboy/ssh-action@v0.1.5
        with:
          host: ${{ secrets.HOST }}
          username: ${{ secrets.USERNAME }}
          key: ${{ secrets.SSH_PRIVATE_KEY }}
          script: |
            cd /var/www/laravel-dto
            git pull origin main
            composer install --optimize-autoloader --no-dev
            npm ci --production
            npm run build
            php artisan migrate --force
            php artisan config:cache
            php artisan route:cache
            php artisan view:cache
            php artisan queue:restart
            sudo systemctl reload nginx
```

## Database Migrations

### Production Migration Strategy

```bash
# Before deployment
php artisan migrate:status
php artisan migrate --dry-run

# During deployment
php artisan migrate --force

# Rollback if needed
php artisan migrate:rollback --step=1
```

### Migration Best Practices

1. **Always backup before migrations**
2. **Test migrations in staging first**
3. **Use transactions in migrations**
4. **Avoid destructive operations**
5. **Plan rollback strategies**

```php
<?php

// Example safe migration
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['phone']);
            $table->dropColumn('phone');
        });
    }
};
```

## Performance Optimization

### Laravel Optimizations

```bash
# Production optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Queue optimization
php artisan queue:restart

# Clear development caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Database Optimization

```sql
-- Add indexes for frequently queried columns
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_created_at ON users(created_at);

-- Optimize tables
OPTIMIZE TABLE users;
ANALYZE TABLE users;
```

### Caching Strategy

```php
<?php

// Cache configuration
// config/cache.php
return [
    'default' => env('CACHE_DRIVER', 'redis'),
    
    'stores' => [
        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
        ],
        
        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],
    ],
];

// Example caching in actions
class GetUserProfileAction
{
    public function __invoke(int $userId): UserData
    {
        return Cache::remember(
            "user_profile_{$userId}",
            3600, // 1 hour
            fn() => UserData::from(User::findOrFail($userId))
        );
    }
}
```

## Monitoring and Logging

### Application Monitoring

```php
<?php

// config/logging.php
return [
    'default' => env('LOG_CHANNEL', 'stack'),
    
    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single', 'slack'],
        ],
        
        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
        ],
        
        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Log',
            'emoji' => ':boom:',
            'level' => 'error',
        ],
    ],
];
```

### Health Check Endpoint

```php
<?php

// routes/web.php
Route::get('/health-check', function () {
    $checks = [
        'database' => DB::connection()->getPdo() !== null,
        'cache' => Cache::store()->getRedis()->ping(),
        'queue' => true, // Add queue health check
    ];
    
    $healthy = array_reduce($checks, fn($carry, $check) => $carry && $check, true);
    
    return response()->json([
        'status' => $healthy ? 'healthy' : 'unhealthy',
        'checks' => $checks,
        'timestamp' => now()->toISOString(),
    ], $healthy ? 200 : 503);
});
```

### Server Monitoring

```bash
# Install monitoring tools
sudo apt install htop iotop nethogs

# Setup log rotation
sudo nano /etc/logrotate.d/laravel-dto

# Monitor disk space
df -h
du -sh /var/www/laravel-dto

# Monitor processes
ps aux | grep php
ps aux | grep nginx
```

## Backup Strategy

### Database Backups

```bash
#!/bin/bash

# backup/db-backup.sh
BACKUP_DIR="/var/backups/mysql"
DATE=$(date +%Y%m%d_%H%M%S)
DB_NAME="laravel_dto"
DB_USER="backup_user"
DB_PASS="backup_password"

mkdir -p $BACKUP_DIR

# Create backup
mysqldump -u$DB_USER -p$DB_PASS $DB_NAME > $BACKUP_DIR/backup_$DATE.sql

# Compress backup
gzip $BACKUP_DIR/backup_$DATE.sql

# Remove backups older than 30 days
find $BACKUP_DIR -name "*.sql.gz" -mtime +30 -delete

echo "Database backup completed: backup_$DATE.sql.gz"
```

### File Backups

```bash
#!/bin/bash

# backup/file-backup.sh
SOURCE_DIR="/var/www/laravel-dto"
BACKUP_DIR="/var/backups/files"
DATE=$(date +%Y%m%d_%H%M%S)

mkdir -p $BACKUP_DIR

# Backup storage directory
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz $SOURCE_DIR/storage

# Backup configuration files
tar -czf $BACKUP_DIR/config_$DATE.tar.gz $SOURCE_DIR/.env

# Remove old backups
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "File backup completed"
```

### Automated Backup with Cron

```bash
# Add to crontab (crontab -e)
# Database backup every 6 hours
0 */6 * * * /var/backups/scripts/db-backup.sh

# File backup daily at 2 AM
0 2 * * * /var/backups/scripts/file-backup.sh

# Log cleanup weekly
0 0 * * 0 find /var/www/laravel-dto/storage/logs -name "*.log" -mtime +7 -delete
```

## Security Considerations

### Server Security

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Configure firewall
sudo ufw allow ssh
sudo ufw allow 80
sudo ufw allow 443
sudo ufw enable

# Secure SSH
sudo nano /etc/ssh/sshd_config
# Disable root login: PermitRootLogin no
# Change default port: Port 2222
sudo systemctl restart ssh

# Install fail2ban
sudo apt install fail2ban
sudo systemctl enable fail2ban
```

### Application Security

```env
# Production environment variables
APP_DEBUG=false
APP_ENV=production

# Strong session configuration
SESSION_LIFETIME=120
SESSION_ENCRYPT=true
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=strict

# HTTPS enforcement
FORCE_HTTPS=true
```

### SSL/TLS Configuration

```bash
# Install Let's Encrypt
sudo apt install certbot python3-certbot-nginx

# Obtain SSL certificate
sudo certbot --nginx -d yourapp.com -d www.yourapp.com

# Auto-renewal
sudo crontab -e
# Add: 0 12 * * * /usr/bin/certbot renew --quiet
```

## Troubleshooting

### Common Issues

1. **Permission Errors**
```bash
sudo chown -R www-data:www-data /var/www/laravel-dto
sudo chmod -R 755 /var/www/laravel-dto/storage
sudo chmod -R 755 /var/www/laravel-dto/bootstrap/cache
```

2. **Queue Worker Issues**
```bash
# Check queue status
php artisan queue:failed

# Restart queue workers
supervisorctl restart laravel-queue-worker:*

# Clear failed jobs
php artisan queue:flush
```

3. **Cache Issues**
```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

4. **Database Connection Issues**
```bash
# Test database connection
php artisan tinker
>>> DB::connection()->getPdo();

# Check MySQL status
sudo systemctl status mysql
```

### Log Analysis

```bash
# View Laravel logs
tail -f /var/www/laravel-dto/storage/logs/laravel.log

# View Nginx logs
tail -f /var/log/nginx/access.log
tail -f /var/log/nginx/error.log

# View PHP-FPM logs
tail -f /var/log/php8.2-fpm.log
```

This deployment guide ensures your Laravel DTO application is deployed securely, efficiently, and reliably across all environments. 