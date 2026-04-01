#!/bin/bash

# RijanPHP Installation Script
# Cross-platform compatible (Linux, macOS, Windows with Git Bash/WSL)

set -e

echo "=========================================="
echo "  RijanPHP Installation Script"
echo "=========================================="
echo ""

# Function to detect OS
detect_os() {
    case "$(uname -s)" in
        Linux*)     echo "linux";;
        Darwin*)    echo "macos";;
        CYGWIN*|MINGW*|MSYS*) echo "windows";;
        *)          echo "unknown";;
    esac
}

# Function to check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Check required commands
echo "Checking requirements..."

if ! command_exists git; then
    echo "Error: Git is not installed. Please install Git first."
    exit 1
fi

if ! command_exists php; then
    echo "Error: PHP is not installed. Please install PHP first."
    exit 1
fi

PHP_VERSION=$(php -r "echo PHP_VERSION;")
PHP_MAJOR=$(php -r "echo PHP_MAJOR_VERSION;")
PHP_MINOR=$(php -r "echo PHP_MINOR_VERSION;")

if [ "$PHP_MAJOR" -lt 8 ]; then
    echo "Error: PHP 8.0 or higher is required. Current version: $PHP_VERSION"
    exit 1
fi

echo "✓ Git: $(git --version)"
echo "✓ PHP: $PHP_VERSION"
echo ""

# Get repository URL
REPO_URL="https://github.com/Rijanara-Teknologi/RijanPHP.git"
INSTALL_DIR="RijanPHP"

# Ask for installation directory
read -p "Installation directory [default: RijanPHP]: " INPUT_DIR
INSTALL_DIR=${INPUT_DIR:-RijanPHP}

echo ""
echo "Cloning repository to '$INSTALL_DIR'..."

# Clone repository
if [ -d "$INSTALL_DIR" ]; then
    read -p "Directory exists. Remove and re-clone? (y/n): " CONFIRM
    if [ "$CONFIRM" = "y" ] || [ "$CONFIRM" = "Y" ]; then
        rm -rf "$INSTALL_DIR"
        git clone "$REPO_URL" "$INSTALL_DIR"
    else
        echo "Using existing directory."
    fi
else
    git clone "$REPO_URL" "$INSTALL_DIR"
fi

cd "$INSTALL_DIR"

echo "Installing dependencies..."
composer install --no-interaction --no-progress

# Create .env from .env.example
echo ""
echo "Creating environment configuration..."

if [ ! -f ".env.example" ]; then
    echo "Error: .env.example not found"
    exit 1
fi

cp .env.example .env

# Update .env for SQLite
echo "Configuring SQLite database..."

# Update database driver to SQLite
sed -i '' 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/g' .env 2>/dev/null || \
sed -i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/g' .env

# Remove MySQL specific settings for SQLite
sed -i '' 's/DB_HOST=127.0.0.1//g' .env 2>/dev/null || \
sed -i 's/DB_HOST=127.0.0.1//g' .env

sed -i '' 's/DB_PORT=3306//g' .env 2>/dev/null || \
sed -i 's/DB_PORT=3306//g' .env

sed -i '' 's/DB_DATABASE=rijanphp//g' .env 2>/dev/null || \
sed -i 's/DB_DATABASE=rijanphp//g' .env

sed -i '' 's/DB_USERNAME=root//g' .env 2>/dev/null || \
sed -i 's/DB_USERNAME=root//g' .env

sed -i '' 's/DB_PASSWORD=/g' .env 2>/dev/null || \
sed -i 's/DB_PASSWORD=//g' .env

# Create SQLite database file
echo "Creating SQLite database..."
touch database/database.sqlite

# Generate application key
echo "Generating application key..."
php rijan key:generate

# Clear cache
echo "Clearing cache..."
php rijan cache:clear

echo ""
echo "=========================================="
echo "  Installation Complete!"
echo "=========================================="
echo ""
echo "Next steps:"
echo "  1. cd $INSTALL_DIR"
echo "  2. php rijan serve"
echo ""
echo "Then visit: http://localhost:8000"
echo ""

# Ask about documentation
read -p "Do you want to read the documentation? (y/n): " READ_DOCS

if [ "$READ_DOCS" = "y" ] || [ "$READ_DOCS" = "Y" ]; then
    echo ""
    echo "Opening documentation..."
    if command_exists xdg-open; then
        xdg-open "https://github.com/Rijanara-Teknologi/RijanPHP/wiki"
    elif command_exists open; then
        open "https://github.com/Rijanara-Teknologi/RijanPHP/wiki"
    elif command_exists start; then
        start "https://github.com/Rijanara-Teknologi/RijanPHP/wiki"
    else
        echo "Please open: https://github.com/Rijanara-Teknologi/RijanPHP/wiki"
    fi
fi

echo ""
echo "Thank you for installing RijanPHP!"