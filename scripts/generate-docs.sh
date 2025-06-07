#!/bin/bash

# 📚 Generate Documentation Locally
# This script generates the same documentation that CI/CD creates automatically

set -e

echo "📚 Generating documentation locally..."

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

\`\`\`
$(tree src/ -I 'vendor|node_modules|storage|bootstrap/cache' -L 3 2>/dev/null || echo "src/ directory structure")
\`\`\`

EOF

# Generate API routes
echo "🎯 Generating API routes..."
cat > docs/generated/api-routes.md << EOF
# API Routes (Auto-generated)

**Generated:** $(date -u +"%Y-%m-%d %H:%M:%S UTC")

$(php artisan route:list --columns=method,uri,name,action 2>/dev/null || echo "Routes not available")

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
if command -v jq > /dev/null; then
    cat package.json | jq -r '.scripts | to_entries[] | "- `\(.key)`: \(.value)"' > docs/generated/npm-scripts.md
else
    echo "# NPM Scripts" > docs/generated/npm-scripts.md
    echo "jq not available - install jq to generate NPM scripts documentation" >> docs/generated/npm-scripts.md
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

echo "✅ Documentation generation complete!"
echo ""
echo "Generated files:"
echo "- docs/generated/quality-metrics.md"
echo "- docs/generated/commands.md"
echo "- docs/generated/structure.md"
echo "- docs/generated/api-routes.md"
echo "- docs/generated/recent-changes.md"
echo "- docs/generated/npm-scripts.md"
echo "- docs/generated/badges.md"
echo ""
echo "💡 Tip: These files are also generated automatically by CI/CD on push to master" 