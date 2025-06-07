# CI/CD Pipeline Setup Guide

## 🚀 Overzicht

Deze handleiding legt uit hoe je een complete CI/CD pipeline opzet die automatisch wordt getriggerd bij een push naar de `master` branch. De pipeline bevat:

- ✅ **Testing**: Unit tests, integration tests, en coverage
- 🔍 **Code Quality**: Code style checks en static analysis
- 🔒 **Security**: Vulnerability scanning
- 🚀 **Deployment**: Automatische deployment naar productie
- 📢 **Notifications**: Slack notificaties

## 📋 Vereisten

### 1. Repository Setup
Zorg ervoor dat je repository de volgende bestanden heeft:
- `.github/workflows/ci-cd.yml` (zojuist aangemaakt)
- `composer.json` met test dependencies
- `package.json` met build scripts
- `.env.example` configuratie

### 2. Required Dependencies
Voeg deze dependencies toe aan je `composer.json`:

```json
{
  "require-dev": {
    "laravel/pint": "^1.0",
    "larastan/larastan": "^2.0",
    "pestphp/pest": "^2.0",
    "pestphp/pest-plugin-laravel": "^2.0"
  }
}
```

## ⚙️ Secrets Configuratie

### 1. GitHub Repository Secrets

Ga naar je GitHub repository → **Settings** → **Secrets and variables** → **Actions** en voeg de volgende secrets toe:

#### Production Server Secrets
```
PRODUCTION_HOST=jouw-server-ip-of-domein.com
PRODUCTION_USERNAME=je-ssh-gebruikersnaam
PRODUCTION_SSH_KEY=-----BEGIN OPENSSH PRIVATE KEY-----
[jouw private SSH key inhoud]
-----END OPENSSH PRIVATE KEY-----
PRODUCTION_SSH_PORT=22
```

#### Notification Secrets (optioneel)
```
SLACK_WEBHOOK_URL=https://hooks.slack.com/services/YOUR/SLACK/WEBHOOK
CODECOV_TOKEN=jouw-codecov-token
```

### 2. SSH Key Genereren

Als je nog geen SSH key hebt:

```bash
# Genereer een nieuwe SSH key voor deployment
ssh-keygen -t ed25519 -C "github-actions@jouw-domein.com" -f ~/.ssh/github_actions

# Kopieer de public key naar je server
ssh-copy-id -i ~/.ssh/github_actions.pub user@jouw-server.com

# Voeg de private key toe als GitHub secret
cat ~/.ssh/github_actions
```

### 3. Server Voorbereiding

Configureer je productie server:

```bash
# Maak deployment directories
sudo mkdir -p /var/www/laravel-dto
sudo mkdir -p /var/backups/laravel-dto

# Stel eigendom in
sudo chown -R www-data:www-data /var/www/laravel-dto
sudo usermod -a -G www-data je-gebruikersnaam

# Configureer sudo voor deployment acties
sudo visudo
# Voeg toe: je-gebruikersnaam ALL=(ALL) NOPASSWD: /bin/systemctl reload nginx, /bin/systemctl restart php8.2-fpm, /usr/bin/supervisorctl restart laravel-queue-worker:*
```

## 🌍 Environment Configuration

### 1. GitHub Environments

Ga naar **Settings** → **Environments** en maak een `production` environment aan met:

- **Environment protection rules**: Require reviewers (optioneel)
- **Environment secrets**: Server-specifieke configuratie
- **Deployment branches**: Alleen `master` branch

### 2. Production Environment Variables

Voeg deze toe aan je production environment:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://jouw-domein.com
```

## 🔧 Pipeline Configuratie

### 1. Branch Protection

Configureer branch protection voor `master`:

- Ga naar **Settings** → **Branches**
- Klik op **Add rule** voor `master`
- Schakel in:
  - ✅ Require status checks to pass before merging
  - ✅ Require up-to-date branches before merging
  - ✅ Include administrators

### 2. Required Status Checks

Selecteer deze checks als vereist:
- `test`
- `code-quality` 
- `security`

## 📝 Workflow Triggers

De pipeline wordt getriggerd bij:

```yaml
on:
  push:
    branches: [master, main]  # Deployment bij push naar master
  pull_request:
    branches: [master, main]  # Testing bij pull requests
```

### Custom Triggers Toevoegen

Je kunt ook handmatige triggers toevoegen:

```yaml
on:
  push:
    branches: [master, main]
  pull_request:
    branches: [master, main]
  workflow_dispatch:  # Handmatige trigger
    inputs:
      environment:
        description: 'Environment to deploy to'
        required: true
        default: 'production'
        type: choice
        options:
        - production
        - staging
```

## 🧪 Testing Configuratie

### 1. PHPUnit/Pest Setup

Zorg ervoor dat je `phpunit.xml` correct is geconfigureerd:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
         bootstrap="vendor/autoload.php"
         colors="true">
    <testsuites>
        <testsuite name="Unit">
            <directory suffix="Test.php">./tests/Unit</directory>
        </testsuite>
        <testsuite name="Feature">
            <directory suffix="Test.php">./tests/Feature</directory>
        </testsuite>
    </testsuites>
    <coverage processUncoveredFiles="true">
        <include>
            <directory suffix=".php">./src</directory>
        </include>
        <exclude>
            <directory suffix=".php">./src/Support</directory>
        </exclude>
    </coverage>
</phpunit>
```

### 2. Laravel Pint Configuration

Maak een `.pint.json` bestand:

```json
{
    "preset": "laravel",
    "rules": {
        "simplified_null_return": true,
        "braces": {
            "position_after_control_structures": "same"
        },
        "new_with_braces": true
    },
    "exclude": [
        "bootstrap",
        "storage",
        "vendor"
    ]
}
```

### 3. PHPStan Configuration

Maak een `phpstan.neon` bestand:

```neon
includes:
    - ./vendor/nunomaduro/larastan/extension.neon

parameters:
    paths:
        - src/
        - tests/

    # Level 8 is very strict - good for high code quality
    level: 8

    # Bootstrap for Laravel-specific features
    bootstrapFiles:
        - vendor/nunomaduro/larastan/bootstrap.php

    ignoreErrors:
        - '#Unsafe usage of new static#'
        - '#PHPDoc tag @var#'

    excludePaths:
        - vendor/
        - node_modules/
        - bootstrap/cache/
        - storage/

    # Enhanced type checking
    checkMissingIterableValueType: true
    checkGenericClassInNonGenericObjectType: true
    reportUnmatchedIgnoredErrors: true
    checkTooWideReturnTypesInProtectedAndPublicMethods: true
```

**Belangrijke verbeteringen in configuratie:**
- ✅ **Level 8**: Strengste analyse niveau voor maximale type safety
- ✅ **Tests included**: Analyseer ook test bestanden voor kwaliteit
- ✅ **Enhanced checking**: Extra type validatie opties ingeschakeld
- ✅ **Laravel support**: Nieuwste Larastan extensie voor Laravel 11+ support

### 4. JavaScript/TypeScript Linting (ESLint)

Voor frontend code quality, configureer ESLint in `eslint.config.js`:

```javascript
import prettier from 'eslint-config-prettier';
import vue from 'eslint-plugin-vue';
import { defineConfigWithVueTs, vueTsConfigs } from '@vue/eslint-config-typescript';

export default defineConfigWithVueTs(
    vue.configs['flat/strongly-recommended'],
    vueTsConfigs.strict,
    {
        ignores: [
            'vendor',
            'node_modules',
            'public',
            'bootstrap/ssr',
            'resources/js/components/ui/*',
        ],
    },
    {
        rules: {
            'vue/multi-word-component-names': 'off',
            '@typescript-eslint/no-explicit-any': 'error',
            '@typescript-eslint/explicit-function-return-type': 'off',
            '@typescript-eslint/no-unused-vars': ['error', { argsIgnorePattern: '^_' }],
        },
    },
    prettier,
);
```

### 5. Code Formatting (Prettier)

Automatische code formatting in `.prettierrc`:

```json
{
    "printWidth": 120,
    "tabWidth": 4,
    "useTabs": false,
    "semi": true,
    "singleQuote": true,
    "trailingComma": "all",
    "plugins": [
        "prettier-plugin-organize-imports",
        "prettier-plugin-tailwindcss"
    ]
}
```

**NPM Scripts voor code quality:**
```json
{
    "scripts": {
        "lint": "eslint . --fix",
        "format": "prettier --write resources/",
        "format:check": "prettier --check resources/"
    }
}
```

## 📊 Monitoring & Notifications

### 1. Slack Integratie

Om Slack notificaties te ontvangen:

1. Maak een Slack app aan: https://api.slack.com/apps
2. Schakel Incoming Webhooks in
3. Genereer een webhook URL
4. Voeg toe als `SLACK_WEBHOOK_URL` secret

### 2. Code Coverage (Codecov)

Voor code coverage tracking:

1. Registreer bij https://codecov.io
2. Koppel je repository
3. Krijg je token en voeg toe als `CODECOV_TOKEN` secret

### 3. Custom Notifications

Je kunt custom notifications toevoegen:

```yaml
- name: 📢 Teams notification
  if: failure()
  uses: aliencube/microsoft-teams-actions@v0.8.0
  with:
    webhook_uri: ${{ secrets.TEAMS_WEBHOOK_URL }}
    title: "Deployment Failed"
    summary: "The deployment to production has failed"
```

## 🚀 Deployment Process

### Automatische Deployment

De deployment gebeurt automatisch bij een push naar `master`:

1. **Tests uitvoeren** - Unit, feature, en integration tests
2. **Code quality check** - PHPStan Level 8, ESLint, en Prettier formatting
3. **Security scan** - Vulnerability checking
4. **Build assets** - Compile frontend assets
5. **Deploy to server** - SSH deployment met zero-downtime
6. **Health check** - Verificatie dat deployment succesvol is
7. **Notifications** - Slack/Teams notificaties

### Handmatige Deployment

Voor handmatige deployment kun je een workflow trigger toevoegen:

```bash
# Via GitHub CLI
gh workflow run ci-cd.yml

# Via GitHub web interface
# Ga naar Actions → CI/CD Pipeline → Run workflow
```

## 🔄 Rollback Procedure

Bij deployment problemen:

### 1. Automatische Rollback

De pipeline heeft ingebouwde health checks:

```bash
# Health check faalt → automatische rollback
curl -f http://localhost/health-check || exit 1
```

### 2. Handmatige Rollback

```bash
# SSH naar server
ssh user@jouw-server.com

# Ga naar backup directory  
cd /var/backups/laravel-dto

# Lijst beschikbare backups
ls -la

# Herstel naar vorige versie
sudo cp -r backup_20240115_143022 /var/www/laravel-dto
sudo systemctl reload nginx
```

## 🛠️ Troubleshooting

### Veel voorkomende problemen:

#### 1. SSH Connection Fails
```
Error: dial tcp: lookup jouw-server.com: no such host
```
**Oplossing**: Controleer `PRODUCTION_HOST` secret

#### 2. Permission Denied
```
Error: Permission denied (publickey)
```
**Oplossing**: Controleer SSH key configuratie

#### 3. Tests Falen
```
Error: Tests failed with exit code 1
```
**Oplossing**: Run tests lokaal eerst: `php artisan test`

#### 4. Deployment Timeout
```
Error: Process exited with status 124
```
**Oplossing**: Verhoog timeout in SSH action

### Debug Commands

```bash
# Test SSH connectie lokaal
ssh -i ~/.ssh/github_actions user@jouw-server.com

# Controleer workflow logs
# GitHub → Actions → Selecteer workflow run → View logs

# Test deployment script lokaal
bash deployment/deploy.sh
```

## 📈 Pipeline Optimalisatie

### 1. Caching Verbeteren

```yaml
- name: 🗄️ Cache vendor directory
  uses: actions/cache@v3
  with:
    path: vendor
    key: ${{ runner.os }}-vendor-${{ hashFiles('**/composer.lock') }}
```

### 2. Parallel Jobs

```yaml
jobs:
  test:
    strategy:
      matrix:
        php-version: [8.2, 8.3]
        dependency-version: [prefer-lowest, prefer-stable]
```

### 3. Conditional Deployment

```yaml
deploy:
  if: |
    github.ref == 'refs/heads/master' && 
    github.event_name == 'push' &&
    !contains(github.event.head_commit.message, '[skip ci]')
```

## ✅ Checklist voor Go-Live

- [ ] Alle secrets zijn geconfigureerd
- [ ] SSH keys zijn opgezet
- [ ] Server is voorbereid voor deployment
- [ ] Branch protection is ingeschakeld  
- [ ] Tests draaien succesvol lokaal
- [ ] Code style checks passeren
- [ ] Health check endpoint werkt
- [ ] Backup strategie is geïmplementeerd
- [ ] Monitoring is opgezet
- [ ] Team is geïnformeerd over deployment proces

## 🎯 Volgende Stappen

1. **Test de pipeline** met een kleine wijziging
2. **Monitor de eerste deployments** nauwkeurig
3. **Documenteer eventuele aanpassingen**
4. **Train het team** in het gebruik van de pipeline
5. **Stel monitoring alerts** in voor kritieke metrics

Je pipeline is nu klaar voor gebruik! Bij elke push naar `master` wordt automatisch getest, gebouwd en gedeployed naar productie. 🚀 