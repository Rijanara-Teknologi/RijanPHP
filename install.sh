#!/bin/bash

# RijanPHP Installation Script
# Cross-platform compatible: Linux, macOS, Windows (Git Bash/WSL/PowerShell)

set -e

# ============================================
# OS Detection
# ============================================
detect_os() {
    case "$(uname -s)" in
        Linux*)     echo "linux";;
        Darwin*)    echo "macos";;
        CYGWIN*|MINGW*|MSYS*) echo "windows";;
        *)          echo "unknown";;
    esac
}

# Detect package manager
detect_pkg_manager() {
    if command -v apt-get >/dev/null 2>&1; then
        echo "apt"
    elif command -v yum >/dev/null 2>&1; then
        echo "yum"
    elif command -v dnf >/dev/null 2>&1; then
        echo "dnf"
    elif command -v brew >/dev/null 2>&1; then
        echo "brew"
    elif command -v pacman >/dev/null 2>&1; then
        echo "pacman"
    elif command -v choco >/dev/null 2>&1; then
        echo "choco"
    else
        echo "unknown"
    fi
}

# Check if command exists
command_exists() {
    command -v "$1" >/dev/null 2>&1
}

# Cross-platform sed -i
sed_i() {
    if [[ "$(uname -s)" == "Darwin" ]]; then
        sed -i '' "$@"
    else
        sed -i "$@"
    fi
}

# Open URL in browser (cross-platform)
open_browser() {
    local url="$1"
    local os=$(detect_os)
    
    case "$os" in
        linux)
            if command_exists xdg-open; then
                xdg-open "$url" 2>/dev/null || true
            elif command_exists gnome-open; then
                gnome-open "$url" 2>/dev/null || true
            else
                echo "Please open manually: $url"
            fi
            ;;
        macos)
            open "$url"
            ;;
        windows)
            start "$url"
            ;;
    esac
}

# Print colored message
print_msg() {
    local color=$1
    local msg=$2
    case "$color" in
        green)  echo -e "\033[32m✓ $msg\033[0m";;
        red)    echo -e "\033[31m✗ $msg\033[0m";;
        yellow) echo -e "\033[33m⚠ $msg\033[0m";;
        blue)   echo -e "\033[34m➤ $msg\033[0m";;
        *)      echo "$msg";;
    esac
}

# ============================================
# Main Installation
# ============================================

clear
echo "=========================================="
echo "  RijanPHP Installation Script"
echo "=========================================="
echo ""

# Detect and show system info
OS=$(detect_os)
PKG_MGR=$(detect_pkg_manager)

echo "Detected System:"
echo "  • OS: $OS"
echo "  • Package Manager: ${PKG_MGR:-none}"
echo "  • Shell: ${SHELL:-unknown}"
echo ""

# ============================================
# Requirement Checks
# ============================================
print_msg "blue" "Checking requirements..."

MISSING_DEPS=""

if ! command_exists git; then
    MISSING_DEPS="$MISSING_DEPS git"
fi

if ! command_exists php; then
    MISSING_DEPS="$MISSING_DEPS php"
fi

if ! command_exists composer; then
    MISSING_DEPS="$MISSING_DEPS composer"
fi

if [ -n "$MISSING_DEPS" ]; then
    print_msg "red" "Missing dependencies:$MISSING_DEPS"
    echo ""
    echo "Please install the missing dependencies:"
    echo ""
    
    case "$PKG_MGR" in
        apt)
            echo "  sudo apt-get update && sudo apt-get install -y$MISSING_DEPS"
            ;;
        yum)
            echo "  sudo yum install -y$MISSING_DEPS"
            ;;
        dnf)
            echo "  sudo dnf install -y$MISSING_DEPS"
            ;;
        brew)
            echo "  brew install$MISSING_DEPS"
            ;;
        pacman)
            echo "  sudo pacman -S$MISSING_DEPS"
            ;;
        choco)
            echo "  choco install$MISSING_DEPS"
            ;;
        *)
            echo "  Please install:$MISSING_DEPS"
            ;;
    esac
    
    echo ""
    echo "For Composer, visit: https://getcomposer.org/download/"
    exit 1
fi

# Check PHP version
PHP_VERSION=$(php -r "echo PHP_VERSION;")
PHP_MAJOR=$(php -r "echo PHP_MAJOR_VERSION;")

if [ "$PHP_MAJOR" -lt 8 ]; then
    print_msg "red" "PHP 8.0+ is required. Current: $PHP_VERSION"
    exit 1
fi

# Check PHP extensions
echo ""
print_msg "blue" "Checking PHP extensions..."
MISSING_EXT=""

if ! php -m | grep -q "^PDO$"; then
    MISSING_EXT="$MISSING_EXT pdo"
fi

if ! php -m | grep -q "^pdo_sqlite$"; then
    MISSING_EXT="$MISSING_EXT pdo_sqlite"
fi

if ! php -m | grep -q "^mbstring$"; then
    MISSING_EXT="$MISSING_EXT mbstring"
fi

if ! php -m | grep -q "^openssl$"; then
    MISSING_EXT="$MISSING_EXT openssl"
fi

if [ -n "$MISSING_EXT" ]; then
    print_msg "yellow" "Recommended extensions not found:$MISSING_EXT"
    print_msg "yellow" "These may be required for full functionality."
fi

print_msg "green" "Git: $(git --version | head -1)"
print_msg "green" "PHP: $PHP_VERSION"
print_msg "green" "Composer: $(composer --version | head -1)"
echo ""

# ============================================
# Installation Directory
# ============================================
REPO_URL="https://github.com/Rijanara-Teknologi/RijanPHP.git"
INSTALL_DIR="RijanPHP"

read -p "Installation directory [default: RijanPHP]: " INPUT_DIR
INSTALL_DIR=${INPUT_DIR:-RijanPHP}

echo ""

# ============================================
# Clone Repository
# ============================================
if [ -d "$INSTALL_DIR" ]; then
    print_msg "yellow" "Directory '$INSTALL_DIR' already exists."
    read -p "Remove and re-clone? (y/n): " CONFIRM
    if [ "$CONFIRM" = "y" ] || [ "$CONFIRM" = "Y" ]; then
        print_msg "blue" "Removing existing directory..."
        rm -rf "$INSTALL_DIR"
        git clone "$REPO_URL" "$INSTALL_DIR"
        print_msg "green" "Repository cloned successfully."
    else
        print_msg "blue" "Using existing directory."
        cd "$INSTALL_DIR"
    fi
else
    print_msg "blue" "Cloning repository to '$INSTALL_DIR'..."
    git clone "$REPO_URL" "$INSTALL_DIR"
    print_msg "green" "Repository cloned successfully."
    cd "$INSTALL_DIR"
fi

# ============================================
# Install Dependencies
# ============================================
echo ""
print_msg "blue" "Installing dependencies with Composer..."
composer install --no-interaction --no-progress
print_msg "green" "Dependencies installed."

# ============================================
# Environment Configuration
# ============================================
echo ""
print_msg "blue" "Configuring environment..."

if [ ! -f ".env.example" ]; then
    print_msg "red" ".env.example not found"
    exit 1
fi

cp .env.example .env
print_msg "green" "Created .env from .env.example"

# ============================================
# SQLite Configuration
# ============================================
echo ""
print_msg "blue" "Configuring SQLite database..."

sed_i 's/DB_CONNECTION=mysql/DB_CONNECTION=sqlite/g' .env
sed_i 's/DB_HOST=127.0.0.1//g' .env
sed_i 's/DB_PORT=3306//g' .env
sed_i 's/DB_DATABASE=rijanphp//g' .env
sed_i 's/DB_USERNAME=root//g' .env
sed_i 's/DB_PASSWORD=.*//g' .env

mkdir -p database
touch database/database.sqlite
print_msg "green" "SQLite database created at database/database.sqlite"

# ============================================
# Application Key
# ============================================
echo ""
print_msg "blue" "Generating application key..."
php rijan key:generate
print_msg "green" "Application key generated."

# ============================================
# Clear Cache
# ============================================
print_msg "blue" "Clearing cache..."
php rijan cache:clear 2>/dev/null || true
print_msg "green" "Cache cleared."

# ============================================
# Completion
# ============================================
echo ""
echo "=========================================="
echo "  ✓ Installation Complete!"
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
    print_msg "blue" "Opening documentation..."
    open_browser "https://github.com/Rijanara-Teknologi/RijanPHP/wiki"
fi

echo ""
echo "=========================================="
echo "  Thank you for installing RijanPHP!"
echo "=========================================="