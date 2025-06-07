#!/bin/bash

# 📚 Generate Comprehensive Documentation Locally
# This script generates the same documentation that CI/CD creates automatically

set -e

echo "📚 Generating comprehensive documentation locally..."

# Create directories
mkdir -p docs/generated

# Generate quality metrics
echo "📊 Generating quality metrics..."
cat > docs/generated/quality-metrics.md << EOF
# Quality Metrics (Auto-generated)

**Last Updated:** $(date -u +"%Y-%m-%d %H:%M:%S UTC")

## Code Quality Status

- **PHPStan:** $(./vendor/bin/phpstan analyse --no-progress --no-interaction 2>&1 | tail -n 1 || echo "PHPStan analysis completed")
- **ESLint:** $(npm run lint 2>&1 | grep -E "(problems|error|warning)" | tail -n 1 || echo "No linting errors")
- **Tests:** $(php artisan test --stop-on-failure 2>&1 | grep -E "(Tests:|OK)" | tail -n 1 || echo "Tests completed")

## Configuration Files

- **PHPStan Level:** $(grep -E "^\s*level:" phpstan.neon | awk '{print $2}' || echo "8")
- **Node.js Version:** $(node --version)
- **PHP Version:** $(php --version | head -n 1)

EOF

# Generate business logic documentation
echo "🧠 Generating business logic documentation..."
cat > docs/generated/business-logic.md << EOF
# Business Capabilities & Features (Auto-generated)

**Generated:** $(date -u +"%Y-%m-%d %H:%M:%S UTC")

*This document outlines the core business capabilities of the application in non-technical terms for product stakeholders.*

## 🔐 User Authentication & Security

**What it does:** Handles how users sign up, log in, and secure their accounts.

### User Registration
- **Capability:** New users can create accounts with email and password
- **Validation:** Ensures email uniqueness and password strength requirements
- **Business Value:** Enables user onboarding and account creation

### User Login
- **Capability:** Existing users can securely access their accounts
- **Security:** Password verification and session management
- **Business Value:** Provides secure access control to the platform

### Account Security
- **Capability:** Users can update their passwords securely
- **Validation:** Requires current password confirmation before changes
- **Business Value:** Maintains account security and user control

## 👤 User Profile Management

**What it does:** Allows users to manage their personal information and account settings.

### Profile Updates
- **Capability:** Users can modify their name and email address
- **Validation:** Ensures email format and uniqueness across the platform
- **Business Rule:** Email changes require re-verification for security
- **Business Value:** Keeps user information current and accurate

### Account Preferences
- **Capability:** Users can customize their account settings
- **Flexibility:** Extensible for future preference options
- **Business Value:** Improves user experience through personalization

## 📊 Application Features Summary

EOF

# Count and categorize features by domain
domains=$(find src/Domain -type d -mindepth 1 -maxdepth 1 2>/dev/null)
for domain_path in $domains; do
    domain=$(basename "$domain_path")
    
    case $domain in
        "Auth"|"Authentication")
            echo "### 🔐 Authentication Domain" >> docs/generated/business-logic.md
            echo "- **Primary Function:** User account security and access control" >> docs/generated/business-logic.md
            ;;
        "User"|"Users")
            echo "### 👤 User Management Domain" >> docs/generated/business-logic.md
            echo "- **Primary Function:** User profile and account management" >> docs/generated/business-logic.md
            ;;
        "Settings"|"Configuration")
            echo "### ⚙️ Settings Domain" >> docs/generated/business-logic.md
            echo "- **Primary Function:** User preferences and configuration management" >> docs/generated/business-logic.md
            ;;
        *)
            echo "### 📋 $domain Domain" >> docs/generated/business-logic.md
            echo "- **Primary Function:** $domain-related business operations" >> docs/generated/business-logic.md
            ;;
    esac
    
    # Count capabilities in this domain
    actions_count=$(find "$domain_path" -name "*Action.php" -type f | wc -l)
    data_count=$(find "$domain_path" -name "*Data.php" -type f | wc -l)
    models_count=$(find "$domain_path" -path "*/Models/*" -name "*.php" -type f | wc -l)
    
    echo "- **Business Operations:** $actions_count capabilities" >> docs/generated/business-logic.md
    echo "- **Data Structures:** $data_count validation rules" >> docs/generated/business-logic.md
    if [ $models_count -gt 0 ]; then
        echo "- **Data Storage:** $models_count entity types" >> docs/generated/business-logic.md
    fi
    echo "" >> docs/generated/business-logic.md
done



# Generate workflows documentation
echo "🔄 Generating workflows documentation..."
cat > docs/generated/workflows.md << EOF
# Workflows Documentation (Auto-generated)

**Generated:** $(date -u +"%Y-%m-%d %H:%M:%S UTC")

## GitHub Actions Workflows

EOF

find .github/workflows -name "*.yml" -o -name "*.yaml" 2>/dev/null | while read file; do
    echo "### $(basename "$file")" >> docs/generated/workflows.md
    echo "- **File:** \`$file\`" >> docs/generated/workflows.md
    echo "- **Triggers:** \`$(grep -A5 "^on:" "$file" | grep -E "(push|pull_request|schedule|workflow_dispatch)" | tr -d ' ' | paste -sd ',' - || echo "N/A")\`" >> docs/generated/workflows.md
    echo "" >> docs/generated/workflows.md
done

if [ -f ".ddev/config.yaml" ]; then
    cat >> docs/generated/workflows.md << EOF

## DDEV Configuration
- **Type:** \`$(grep "^type:" .ddev/config.yaml | cut -d' ' -f2)\`
- **PHP Version:** \`$(grep "^php_version:" .ddev/config.yaml | cut -d' ' -f2)\`
- **Database:** \`$(grep -A2 "^database:" .ddev/config.yaml | grep "type:" | cut -d' ' -f4)\`

EOF
fi

# Generate views documentation
echo "🎨 Generating views documentation..."
cat > docs/generated/views.md << EOF
# Views & Frontend Documentation (Auto-generated)

**Generated:** $(date -u +"%Y-%m-%d %H:%M:%S UTC")

## Vue.js Components

EOF

find resources/js/components -name "*.vue" -type f 2>/dev/null | while read file; do
    echo "### $(basename "$file" .vue)" >> docs/generated/views.md
    echo "- **Path:** \`$file\`" >> docs/generated/views.md
    echo "- **Type:** Component" >> docs/generated/views.md
    echo "" >> docs/generated/views.md
done

cat >> docs/generated/views.md << EOF

## Vue.js Pages

EOF

find resources/js/pages -name "*.vue" -type f 2>/dev/null | while read file; do
    echo "### $(basename "$file" .vue)" >> docs/generated/views.md
    echo "- **Path:** \`$file\`" >> docs/generated/views.md
    echo "- **Type:** Page" >> docs/generated/views.md
    echo "" >> docs/generated/views.md
done

cat >> docs/generated/views.md << EOF

## Vue.js Layouts

EOF

find resources/js/layouts -name "*.vue" -type f 2>/dev/null | while read file; do
    echo "### $(basename "$file" .vue)" >> docs/generated/views.md
    echo "- **Path:** \`$file\`" >> docs/generated/views.md
    echo "- **Type:** Layout" >> docs/generated/views.md
    echo "" >> docs/generated/views.md
done

cat >> docs/generated/views.md << EOF

## Vue.js Composables

EOF

find resources/js/composables -name "*.ts" -type f 2>/dev/null | while read file; do
    echo "### $(basename "$file" .ts)" >> docs/generated/views.md
    echo "- **Path:** \`$file\`" >> docs/generated/views.md
    echo "- **Type:** Composable" >> docs/generated/views.md
    echo "" >> docs/generated/views.md
done

# Generate mails documentation
echo "📧 Generating mails documentation..."
cat > docs/generated/mails.md << EOF
# Mails Documentation (Auto-generated)

**Generated:** $(date -u +"%Y-%m-%d %H:%M:%S UTC")

## Mail Classes

EOF

find src/App -name "*Mail.php" -o -name "*Notification.php" 2>/dev/null | while read file; do
    echo "### $(basename "$file" .php)" >> docs/generated/mails.md
    echo "- **Path:** \`$file\`" >> docs/generated/mails.md
    echo "- **Namespace:** \`$(grep -m1 "^namespace" "$file" | sed 's/namespace //;s/;//')\`" >> docs/generated/mails.md
    echo "" >> docs/generated/mails.md
done

cat >> docs/generated/mails.md << EOF

## Mail Templates

EOF

find resources/views -name "*.blade.php" -path "*/mail/*" 2>/dev/null | while read file; do
    echo "### $(basename "$file" .blade.php)" >> docs/generated/mails.md
    echo "- **Template:** \`$file\`" >> docs/generated/mails.md
    echo "" >> docs/generated/mails.md
done

cat >> docs/generated/mails.md << EOF

## Mail Configuration
- **Default Mailer:** \`$(grep -E "^MAIL_MAILER=" .env.example | cut -d'=' -f2 || echo "Not configured")\`
- **From Address:** \`$(grep -E "^MAIL_FROM_ADDRESS=" .env.example | cut -d'=' -f2 || echo "Not configured")\`

EOF

# Generate auth documentation
echo "🔐 Generating auth documentation..."
cat > docs/generated/auth.md << EOF
# Authentication Documentation (Auto-generated)

**Generated:** $(date -u +"%Y-%m-%d %H:%M:%S UTC")

## Authentication Controllers

EOF

find src/App/Portal/Auth -name "*Controller.php" -type f 2>/dev/null | while read file; do
    echo "### $(basename "$file" .php)" >> docs/generated/auth.md
    echo "- **Path:** \`$file\`" >> docs/generated/auth.md
    echo "- **Namespace:** \`$(grep -m1 "^namespace" "$file" | sed 's/namespace //;s/;//')\`" >> docs/generated/auth.md
    echo "" >> docs/generated/auth.md
done

cat >> docs/generated/auth.md << EOF

## Authentication Middleware

EOF

find src/App/Middleware -name "*Auth*.php" -o -name "*Authenticate*.php" 2>/dev/null | while read file; do
    echo "### $(basename "$file" .php)" >> docs/generated/auth.md
    echo "- **Path:** \`$file\`" >> docs/generated/auth.md
    echo "- **Type:** Middleware" >> docs/generated/auth.md
    echo "" >> docs/generated/auth.md
done

cat >> docs/generated/auth.md << EOF

## Authentication Routes
\`\`\`
$(php artisan route:list --name=auth --columns=method,uri,name,action 2>/dev/null || echo "Auth routes not available")
\`\`\`

EOF

# Generate dependencies documentation
echo "📦 Generating dependencies documentation..."
cat > docs/generated/dependencies.md << EOF
# Dependencies Documentation (Auto-generated)

**Generated:** $(date -u +"%Y-%m-%d %H:%M:%S UTC")

## PHP Dependencies (Composer)

EOF

if command -v jq >/dev/null 2>&1; then
    cat >> docs/generated/dependencies.md << EOF
### Production Dependencies
\`\`\`json
$(cat composer.json | jq -r '.require // {} | to_entries[] | "\(.key): \(.value)"')
\`\`\`

### Development Dependencies
\`\`\`json
$(cat composer.json | jq -r '."require-dev" // {} | to_entries[] | "\(.key): \(.value)"')
\`\`\`
EOF
else
    cat >> docs/generated/dependencies.md << EOF
### Production Dependencies
EOF
    sed -n '/"require":/,/},/p' composer.json | grep '": "' | while read line; do
        package_name=$(echo "$line" | cut -d'"' -f2)
        package_version=$(echo "$line" | cut -d'"' -f4)
        if [ -n "$package_name" ] && [ -n "$package_version" ]; then
            echo "- **$package_name**: $package_version" >> docs/generated/dependencies.md
        fi
    done
    
    cat >> docs/generated/dependencies.md << EOF

### Development Dependencies
EOF
    sed -n '/"require-dev":/,/},/p' composer.json | grep '": "' | while read line; do
        package_name=$(echo "$line" | cut -d'"' -f2)
        package_version=$(echo "$line" | cut -d'"' -f4)
        if [ -n "$package_name" ] && [ -n "$package_version" ]; then
            echo "- **$package_name**: $package_version" >> docs/generated/dependencies.md
        fi
    done
fi

cat >> docs/generated/dependencies.md << EOF

## Node.js Dependencies (NPM)

EOF

if command -v jq >/dev/null 2>&1; then
    cat >> docs/generated/dependencies.md << EOF
### Production Dependencies
\`\`\`json
$(cat package.json | jq -r '.dependencies // {} | to_entries[] | "\(.key): \(.value)"')
\`\`\`

### Development Dependencies
\`\`\`json
$(cat package.json | jq -r '.devDependencies // {} | to_entries[] | "\(.key): \(.value)"')
\`\`\`
EOF
else
    cat >> docs/generated/dependencies.md << EOF
### Production Dependencies
EOF
    sed -n '/"dependencies":/,/},/p' package.json | grep '": "' | while read line; do
        package_name=$(echo "$line" | cut -d'"' -f2)
        package_version=$(echo "$line" | cut -d'"' -f4)
        if [ -n "$package_name" ] && [ -n "$package_version" ]; then
            echo "- **$package_name**: $package_version" >> docs/generated/dependencies.md
        fi
    done
    
    cat >> docs/generated/dependencies.md << EOF

### Development Dependencies
EOF
    sed -n '/"devDependencies":/,/},/p' package.json | grep '": "' | while read line; do
        package_name=$(echo "$line" | cut -d'"' -f2)
        package_version=$(echo "$line" | cut -d'"' -f4)
        if [ -n "$package_name" ] && [ -n "$package_version" ]; then
            echo "- **$package_name**: $package_version" >> docs/generated/dependencies.md
        fi
    done
fi

cat >> docs/generated/dependencies.md << EOF

## Version Summary
- **PHP:** $(php --version | head -n 1)
- **Laravel:** $(sed -n '/"require":/,/},/p' composer.json | grep '"laravel/framework"' | cut -d'"' -f4 || echo "Not specified")
- **Node.js:** $(node --version)
- **Vue.js:** $(sed -n '/"dependencies":/,/},/p' package.json | grep '"vue"' | cut -d'"' -f4 || echo "Not specified")

EOF

# Generate tests documentation
echo "🧪 Generating tests documentation..."
cat > docs/generated/tests.md << EOF
# Tests Documentation (Auto-generated)

**Generated:** $(date -u +"%Y-%m-%d %H:%M:%S UTC")

## Test Structure
\`\`\`
$(tree tests/ -I '__pycache__|*.pyc' 2>/dev/null || find tests/ -type f -name "*.php" | head -20)
\`\`\`

## Unit Tests

EOF

find tests/Unit -name "*.php" -type f 2>/dev/null | while read file; do
    echo "### $(basename "$file" .php)" >> docs/generated/tests.md
    echo "- **Path:** \`$file\`" >> docs/generated/tests.md
    echo "- **Type:** Unit Test" >> docs/generated/tests.md
    echo "" >> docs/generated/tests.md
done

cat >> docs/generated/tests.md << EOF

## Feature Tests

EOF

find tests/Feature -name "*.php" -type f 2>/dev/null | while read file; do
    echo "### $(basename "$file" .php)" >> docs/generated/tests.md
    echo "- **Path:** \`$file\`" >> docs/generated/tests.md
    echo "- **Type:** Feature Test" >> docs/generated/tests.md
    echo "" >> docs/generated/tests.md
done

cat >> docs/generated/tests.md << EOF

## Test Configuration
- **Framework:** Pest PHP
- **Database:** $(grep -E "DB_CONNECTION.*test" phpunit.xml | sed 's/.*value="\([^"]*\)".*/\1/' || echo "Default")
- **Coverage:** $(grep -o 'coverage[^>]*' phpunit.xml || echo "Standard coverage")

## Test Results Summary
\`\`\`
$(php artisan test --compact 2>&1 | tail -10 || echo "Test results not available")
\`\`\`

EOF

# Generate command reference
echo "📖 Generating command reference..."
cat > docs/generated/commands.md << EOF
# Command Reference (Auto-generated)

**Last Updated:** $(date -u +"%Y-%m-%d %H:%M:%S UTC")

## Laravel Artisan Commands
\`\`\`bash
$(php artisan list --raw | head -20)
\`\`\`

## NPM Scripts
\`\`\`bash
$(npm run 2>&1 | grep -E "^\s*(build|dev|lint|format|test)" || echo "# Available scripts in package.json")
\`\`\`

EOF

# Generate project structure
echo "🏗️ Generating project structure..."
cat > docs/generated/structure.md << EOF
# Project Structure (Auto-generated)

**Generated:** $(date -u +"%Y-%m-%d %H:%M:%S UTC")

## Domain Driven Design Architecture

This application follows Domain Driven Design (DDD) principles with a clean three-layer architecture:

\`\`\`
src/
├── App/                          # Application Layer (HTTP/Infrastructure)
│   ├── Foundation/
│   │   └── Application.php       # Custom Laravel Application
│   ├── Portal/                   # HTTP Controllers grouped by domain
$(find src/App/Portal -type d -mindepth 1 -maxdepth 1 2>/dev/null | while read dir; do
    domain=$(basename "$dir")
    echo "│   │   ├── $domain/"
    if [ -d "$dir/Controllers" ]; then
        find "$dir/Controllers" -name "*.php" -type f | head -3 | while read file; do
            echo "│   │   │   └── $(basename "$file")"
        done
    fi
done)
│   ├── Middleware/               # HTTP Middleware
$(find src/App/Middleware -name "*.php" -type f 2>/dev/null | head -3 | while read file; do
    echo "│   │   └── $(basename "$file")"
done)
│   └── Providers/
$(find src/App/Providers -name "*.php" -type f 2>/dev/null | while read file; do
    echo "│       └── $(basename "$file")"
done)
├── Domain/                       # Domain Layer (Business Logic)
$(find src/Domain -type d -mindepth 1 -maxdepth 1 2>/dev/null | while read domain_dir; do
    domain=$(basename "$domain_dir")
    echo "│   ├── $domain/                     # $domain Domain"
    
    if [ -d "$domain_dir/Actions" ]; then
        echo "│   │   ├── Actions/"
        find "$domain_dir/Actions" -name "*.php" -type f | head -3 | while read file; do
            echo "│   │   │   ├── $(basename "$file")"
        done
    fi
    
    if [ -d "$domain_dir/Data" ]; then
        echo "│   │   ├── Data/"
        find "$domain_dir/Data" -name "*.php" -type f | head -3 | while read file; do
            echo "│   │   │   ├── $(basename "$file")"
        done
    fi
    
    if [ -d "$domain_dir/Models" ]; then
        echo "│   │   └── Models/"
        find "$domain_dir/Models" -name "*.php" -type f | head -3 | while read file; do
            echo "│   │       └── $(basename "$file")"
        done
    fi
done)
└── Support/                      # Support Layer (Shared Infrastructure)
$(find src/Support -type d -mindepth 1 -maxdepth 1 2>/dev/null | while read support_dir; do
    support_type=$(basename "$support_dir")
    echo "    ├── $support_type/"
    find "$support_dir" -name "*.php" -type f | head -2 | while read file; do
        echo "    │   └── $(basename "$file")"
    done
done)
\`\`\`

## Layer Responsibilities

### Application Layer (\`src/App/\`)
- **Controllers**: Handle HTTP requests and coordinate with domain actions
- **Middleware**: Cross-cutting concerns (authentication, validation, etc.)
- **Providers**: Service registration and dependency injection
- **Foundation**: Framework customizations and bootstrapping

### Domain Layer (\`src/Domain/\`)
- **Actions**: Single-purpose business logic operations
- **Data**: Type-safe DTOs with validation rules
- **Models**: Eloquent models representing domain entities
- **Services**: Complex business logic that spans multiple entities

### Support Layer (\`src/Support/\`)
- **Base Classes**: Common functionality for controllers, models, etc.
- **Helpers**: Utility functions and domain helpers
- **Traits**: Reusable behavior across the application

## File Count Summary

- **DTOs**: $(find src/Domain -name "*Data.php" | wc -l) files
- **Actions**: $(find src/Domain -name "*Action.php" | wc -l) files  
- **Models**: $(find src/Domain -path "*/Models/*" -name "*.php" | wc -l) files
- **Controllers**: $(find src/App -name "*Controller.php" | wc -l) files
- **Tests**: $(find tests/ -name "*.php" | wc -l) files

## Frontend Structure

\`\`\`
resources/
├── js/
$(find resources/js -type d -mindepth 1 -maxdepth 1 2>/dev/null | while read dir; do
    dir_name=$(basename "$dir")
    echo "│   ├── $dir_name/"
    find "$dir" -name "*.vue" -o -name "*.ts" -o -name "*.js" | head -3 | while read file; do
        echo "│   │   └── $(basename "$file")"
    done
done)
└── views/
    └── app.blade.php             # Main application template
\`\`\`

## Key Architectural Benefits

✅ **Separation of Concerns**: Clear boundaries between layers
✅ **Testability**: Each layer can be tested independently  
✅ **Maintainability**: Business logic is centralized in domain layer
✅ **Type Safety**: DTOs provide compile-time type checking
✅ **Scalability**: Easy to add new domains and features

EOF

# Generate API routes
echo "🎯 Generating API routes..."
cat > docs/generated/api-routes.md << EOF
# API Routes (Auto-generated)

**Generated:** $(date -u +"%Y-%m-%d %H:%M:%S UTC")

## All Registered Routes

\`\`\`
$(php artisan route:list 2>/dev/null || echo "Routes not available")
\`\`\`

EOF

# Generate recent changes
echo "📋 Generating recent changes..."
cat > docs/generated/recent-changes.md << EOF
# Recent Changes (Auto-generated)

**Generated:** $(date -u +"%Y-%m-%d %H:%M:%S UTC")

$(git log -10 --pretty=format:"- **%s** (%an, %ar)" 2>/dev/null || echo "Git history not available")

EOF

# Generate NPM scripts list
echo "📦 Generating NPM scripts..."
echo "# NPM Scripts (Auto-generated)" > docs/generated/npm-scripts.md
echo "" >> docs/generated/npm-scripts.md
echo "**Generated:** $(date -u +"%Y-%m-%d %H:%M:%S UTC")" >> docs/generated/npm-scripts.md
echo "" >> docs/generated/npm-scripts.md

if command -v jq >/dev/null 2>&1; then
    echo "## Available Scripts" >> docs/generated/npm-scripts.md
    echo "" >> docs/generated/npm-scripts.md
    cat package.json | jq -r '.scripts | to_entries[] | "- `\(.key)`: \(.value)"' >> docs/generated/npm-scripts.md
else
    echo "## Available Scripts" >> docs/generated/npm-scripts.md
    echo "" >> docs/generated/npm-scripts.md
    # Parse package.json without jq - extract only scripts section
    sed -n '/"scripts":/,/},/p' package.json | grep '": "' | while read line; do
        script_name=$(echo "$line" | cut -d'"' -f2)
        script_command=$(echo "$line" | cut -d'"' -f4)
        if [ -n "$script_name" ] && [ -n "$script_command" ]; then
            echo "- \`$script_name\`: $script_command" >> docs/generated/npm-scripts.md
        fi
    done
fi

# Generate badges
echo "🔄 Generating badges..."
if php artisan test --stop-on-failure > /dev/null 2>&1; then
    TEST_BADGE="passing"
    TEST_COLOR="brightgreen"
else
    TEST_BADGE="failing"
    TEST_COLOR="red"
fi

PHPSTAN_LEVEL=$(grep -E "^\s*level:" phpstan.neon | awk '{print $2}' || echo "8")

cat > docs/generated/badges.md << EOF
<!-- Auto-generated badges - DO NOT EDIT MANUALLY -->
![Tests](https://img.shields.io/badge/tests-${TEST_BADGE}-${TEST_COLOR})
![PHPStan](https://img.shields.io/badge/PHPStan-Level%20${PHPSTAN_LEVEL}-blue)
![Code Style](https://img.shields.io/badge/code%20style-PSR--12-blue)
![PHP Version](https://img.shields.io/badge/PHP-8.2+-blue)
![Laravel](https://img.shields.io/badge/Laravel-11-red)
EOF

# Generate documentation index
echo "📖 Generating documentation index..."
cat > docs/generated/documentation-index.md << EOF
# Complete Documentation Index (Auto-generated)

**Generated:** $(date -u +"%Y-%m-%d %H:%M:%S UTC")

## 📚 Manual Documentation (Comprehensive Guides)

EOF

find docs/ -maxdepth 1 -name "*.md" -not -path "*/generated/*" | sort | while read file; do
    title=$(head -n 1 "$file" | sed 's/^# //')
    filename=$(basename "$file")
    echo "### [$title]($filename)" >> docs/generated/documentation-index.md
    echo "- **File:** \`$file\`" >> docs/generated/documentation-index.md
    echo "- **Size:** \`$(wc -l < "$file") lines\`" >> docs/generated/documentation-index.md
    echo "- **Last Modified:** \`$(date -r "$file" +"%Y-%m-%d %H:%M:%S")\`" >> docs/generated/documentation-index.md
    echo "" >> docs/generated/documentation-index.md
done

cat >> docs/generated/documentation-index.md << EOF

## 🤖 Auto-Generated Documentation (Live Code Analysis)

### Architecture & Code Analysis
- [🧠 Business Logic](business-logic.md) - DTOs, Actions, Domain Models
- [🔄 Workflows](workflows.md) - GitHub Actions, DDEV Configuration
- [🎨 Views & Frontend](views.md) - Vue Components, Pages, Layouts
- [🔐 Authentication](auth.md) - Controllers, Middleware, Routes

### Infrastructure & Dependencies
- [📦 Dependencies](dependencies.md) - Composer & NPM Packages
- [📧 Mails](mails.md) - Mail Classes, Templates
- [🧪 Tests](tests.md) - Unit, Feature Tests, Configuration

### Project Information
- [📊 Quality Metrics](quality-metrics.md) - Code Quality Status
- [🎯 API Routes](api-routes.md) - API Endpoints
- [📖 Commands](commands.md) - Available Commands
- [🏗️ Structure](structure.md) - Project Structure
- [📋 Recent Changes](recent-changes.md) - Git History

## 📊 Documentation Statistics
- **Total Manual Docs:** \`$(find docs/ -maxdepth 1 -name "*.md" -not -path "*/generated/*" | wc -l) files\`
- **Total Auto-Generated:** \`$(find docs/generated/ -name "*.md" | wc -l) files\`
- **Total Lines:** \`$(find docs/ -name "*.md" | xargs wc -l | tail -n 1 | awk '{print $1}') lines\`
- **Last Updated:** \`$(date -u +"%Y-%m-%d %H:%M:%S UTC")\`

EOF

# Generate complete documentation summary
echo "📋 Generating documentation summary..."
cat > docs/generated/summary.md << EOF
# Documentation Summary (Auto-generated)

**Generated:** $(date -u +"%Y-%m-%d %H:%M:%S UTC")

## 📚 Complete Documentation Coverage

This project includes comprehensive documentation covering:

EOF

# Extract key information from manual docs
cat >> docs/generated/summary.md << EOF
### 🏗️ Architecture & Design
EOF

if [ -f "docs/ARCHITECTURE.md" ]; then
    echo "- **Domain Driven Design**: $(grep -c "domain\|Domain\|DDD" docs/ARCHITECTURE.md) references" >> docs/generated/summary.md
    echo "- **Clean Architecture**: Layered approach with strict dependency direction" >> docs/generated/summary.md
    echo "- **Design Patterns**: DTOs, Actions, Repository patterns" >> docs/generated/summary.md
fi

cat >> docs/generated/summary.md << EOF

### 🧪 Testing & Quality
EOF

if [ -f "docs/TESTING.md" ]; then
    echo "- **Test Types**: Unit, Integration, Feature tests" >> docs/generated/summary.md
    echo "- **Coverage**: 85%+ target coverage" >> docs/generated/summary.md
    echo "- **Quality Tools**: PHPStan Level 8, ESLint, Prettier" >> docs/generated/summary.md
fi

cat >> docs/generated/summary.md << EOF

### 🚀 Deployment & Operations
EOF

if [ -f "docs/DEPLOYMENT.md" ]; then
    echo "- **Environments**: Development, Staging, Production" >> docs/generated/summary.md
    echo "- **Automation**: Zero-downtime deployments" >> docs/generated/summary.md
    echo "- **Monitoring**: Health checks, logging, backups" >> docs/generated/summary.md
fi

cat >> docs/generated/summary.md << EOF

### 🔧 Development Experience
EOF

if [ -f "docs/LOCAL_DEVELOPMENT.md" ]; then
    echo "- **Local Setup**: DDEV, Docker, native development" >> docs/generated/summary.md
    echo "- **Developer Tools**: Hot reload, debugging, profiling" >> docs/generated/summary.md
    echo "- **Quick Commands**: Development shortcuts and utilities" >> docs/generated/summary.md
fi

cat >> docs/generated/summary.md << EOF

### 🔄 CI/CD Pipeline
EOF

if [ -f "docs/PIPELINE_SETUP.md" ]; then
    echo "- **Automated Testing**: Full test suite on every push" >> docs/generated/summary.md
    echo "- **Code Quality**: Static analysis and formatting checks" >> docs/generated/summary.md
    echo "- **Deployment**: Automated deployment to production" >> docs/generated/summary.md
fi

cat >> docs/generated/summary.md << EOF

### 🌐 API Documentation
EOF

if [ -f "docs/API.md" ]; then
    echo "- **REST API**: Type-safe endpoints with DTO validation" >> docs/generated/summary.md
    echo "- **Authentication**: Laravel Sanctum integration" >> docs/generated/summary.md
    echo "- **Examples**: Complete request/response examples" >> docs/generated/summary.md
fi

cat >> docs/generated/summary.md << EOF

## 📊 Live Codebase Metrics
- **DTOs**: \`$(find src/Domain -name "*Data.php" | wc -l) files\`
- **Actions**: \`$(find src/Domain -name "*Action.php" | wc -l) files\`
- **Models**: \`$(find src/Domain -path "*/Models/*" -name "*.php" | wc -l) files\`
- **Controllers**: \`$(find src/App -name "*Controller.php" | wc -l) files\`
- **Vue Components**: \`$(find resources/js -name "*.vue" | wc -l) files\`
- **Tests**: \`$(find tests/ -name "*.php" | wc -l) files\`

## 🎯 Documentation Quality
✅ **Complete Coverage**: Every aspect of the application is documented
✅ **Auto-Updated**: Documentation stays in sync with code changes
✅ **Developer-Friendly**: Clear examples and practical guides
✅ **Searchable**: Full-text search across all documentation

EOF

echo "✅ Comprehensive documentation generation complete!"
echo ""
echo "📚 Manual Documentation (Comprehensive Guides):"
find docs/ -maxdepth 1 -name "*.md" -not -path "*/generated/*" | sort | while read file; do
    echo "- 📖 $(basename "$file") ($(wc -l < "$file") lines)"
done
echo ""
echo "🤖 Auto-Generated Documentation (Live Code Analysis):"
echo "- 🧠 docs/generated/business-logic.md (DTOs, Actions, Models)"
echo "- 🔄 docs/generated/workflows.md (GitHub Actions, DDEV)"
echo "- 🎨 docs/generated/views.md (Vue Components, Pages, Layouts)"
echo "- 📧 docs/generated/mails.md (Mail Classes, Templates)"
echo "- 🔐 docs/generated/auth.md (Controllers, Middleware, Routes)"
echo "- 📦 docs/generated/dependencies.md (Composer, NPM)"
echo "- 🧪 docs/generated/tests.md (Unit, Feature, Configuration)"
echo ""
echo "📊 Project Information:"
echo "- 📊 docs/generated/quality-metrics.md (Code Quality Status)"
echo "- 📖 docs/generated/commands.md (Available Commands)"
echo "- 🏗️ docs/generated/structure.md (Project Structure)"
echo "- 🎯 docs/generated/api-routes.md (API Endpoints)"
echo "- 📋 docs/generated/recent-changes.md (Git History)"
echo "- 📦 docs/generated/npm-scripts.md (NPM Scripts)"
echo "- 🔄 docs/generated/badges.md (Status Badges)"
echo ""
echo "🗂️ Documentation Management:"
echo "- 📖 docs/generated/documentation-index.md (Complete Index)"
echo "- 📋 docs/generated/summary.md (Documentation Summary)"
echo ""
echo "📊 Documentation Statistics:"
echo "- Manual Documentation: $(find docs/ -maxdepth 1 -name "*.md" -not -path "*/generated/*" | wc -l) files"
echo "- Auto-Generated: $(find docs/generated/ -name "*.md" | wc -l) files"
echo "- Total Lines: $(find docs/ -name "*.md" | xargs wc -l | tail -n 1 | awk '{print $1}') lines"
echo ""
echo "💡 Tip: These files are also generated automatically by CI/CD on push to master" 