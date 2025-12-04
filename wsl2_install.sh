#!/bin/bash
# Laravel Development Environment Setup for Linux/WSL2

echo "=== Laravel Development Environment Setup for Linux/WSL2 ==="

# Detect if running in WSL2
if grep -qEi "(Microsoft|WSL)" /proc/version &> /dev/null; then
    echo "🔍 Detected: WSL2 Environment"
    IS_WSL=true
else
    echo "🔍 Detected: Native Linux Environment"
    IS_WSL=false
fi

# Check current versions
echo "=== Checking Current Installations ==="
echo "PHP location: $(which php 2>/dev/null || echo 'Not found')"
if command -v php &> /dev/null; then
    echo "PHP version: $(php -v | head -1)"
fi

echo "Composer location: $(which composer 2>/dev/null || echo 'Not found')"
if command -v composer &> /dev/null; then
    echo "Composer version: $(composer --version 2>/dev/null || echo 'Not accessible')"
fi

echo "MySQL location: $(which mysql 2>/dev/null || echo 'Not found')"
if command -v mysql &> /dev/null; then
    echo "MySQL version: $(mysql --version 2>/dev/null || echo 'Not accessible')"
fi

echo ""
echo "=== Updating System Packages ==="
sudo apt update && sudo apt upgrade -y

echo "=== Installing PHP and Extensions ==="
sudo apt install -y \
    php \
    php-cli \
    php-fpm \
    php-json \
    php-common \
    php-mysql \
    php-zip \
    php-gd \
    php-mbstring \
    php-curl \
    php-xml \
    php-pear \
    php-bcmath \
    php-sqlite3 \
    php-intl \
    php-tokenizer

echo "=== Installing Composer ==="
# Remove old composer if exists
sudo rm -f /usr/local/bin/composer

# Download and install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo chmod +x /usr/local/bin/composer

# Add to PATH if not already there
if ! grep -q "/usr/local/bin" ~/.bashrc; then
    echo 'export PATH="/usr/local/bin:$PATH"' >> ~/.bashrc
    export PATH="/usr/local/bin:$PATH"
fi

echo "=== Installing MySQL ==="
# Install MySQL client and server
sudo apt install -y mysql-client-core-8.0 mysql-server

# Handle MySQL service based on environment
echo "=== Starting MySQL Service ==="
if $IS_WSL; then
    echo "WSL2 detected - starting MySQL service..."
    # WSL2 might need different approach for services
    sudo service mysql start
    
    # Check if systemd is available in WSL2
    if command -v systemctl &> /dev/null; then
        sudo systemctl enable mysql
        sudo systemctl start mysql
    fi
else
    echo "Native Linux detected - using systemctl..."
    sudo systemctl enable mysql
    sudo systemctl start mysql
fi

echo "=== Installing Additional Tools ==="
sudo apt install -y dos2unix curl wget git unzip

echo "=== Verification ==="
echo ""
echo "📋 Installation Summary:"
echo "========================"

# Check PHP
if command -v php &> /dev/null; then
    echo "✅ PHP: $(php --version | head -1)"
else
    echo "❌ PHP: Installation failed"
fi

# Check Composer
if command -v composer &> /dev/null; then
    echo "✅ Composer: $(composer --version)"
else
    echo "❌ Composer: Installation failed"
fi

# Check MySQL
if command -v mysql &> /dev/null; then
    echo "✅ MySQL: $(mysql --version)"
else
    echo "❌ MySQL: Installation failed"
fi

echo ""
echo "=== Checking MySQL Service ==="
# Check MySQL port based on environment
if $IS_WSL; then
    echo "Checking MySQL port (WSL2):"
    if command -v netstat &> /dev/null; then
        sudo netstat -tlnp | grep 3306 || echo "MySQL not listening on port 3306"
    else
        sudo ss -tlnp | grep 3306 || echo "MySQL not listening on port 3306"
    fi
else
    echo "Checking MySQL port (Native Linux):"
    sudo netstat -tlnp | grep 3306 || echo "MySQL not listening on port 3306"
fi

# Check MySQL service status
if command -v systemctl &> /dev/null; then
    echo "MySQL service status:"
    sudo systemctl is-active mysql || echo "MySQL service not active"
else
    echo "Checking MySQL process:"
    sudo service mysql status || echo "MySQL service status unknown"
fi

echo ""
echo "=== File Conversion Setup ==="
# Convert script files if they exist
for script_file in run2.sh run3.sh run4.sh run5.sh; do
    if [ -f "$script_file" ]; then
        echo "Converting $script_file to Unix format..."
        dos2unix "$script_file"
        chmod +x "$script_file"
    fi
done

echo ""
echo "=== MySQL Configuration Instructions ==="
echo "🔧 Next Steps for MySQL Setup:"
echo "1. Secure MySQL installation (recommended):"
echo "   sudo mysql_secure_installation"
echo ""
echo "2. Set root password manually:"
echo "   sudo mysql -u root"
echo "   Then run these SQL commands:"
echo "   ALTER USER 'root'@'localhost' IDENTIFIED BY '123456';"
echo "   FLUSH PRIVILEGES;"
echo "   EXIT;"
echo ""
echo "3. Test MySQL connection:"
echo "   mysql -u root -p"
echo "   (Enter password: 123456)"

echo ""
echo "=== Laravel Installation Test ==="
echo "🚀 Optional: Test Laravel installation:"
echo "composer create-project laravel/laravel test-app"
echo "cd test-app"
echo "php artisan serve"

echo ""
echo "=== Service Management Commands ==="
if $IS_WSL; then
    echo "WSL2 Service Commands:"
    echo "Start MySQL:  sudo service mysql start"
    echo "Stop MySQL:   sudo service mysql stop"
    echo "Status:       sudo service mysql status"
    if command -v systemctl &> /dev/null; then
        echo ""
        echo "Or with systemctl (if available):"
        echo "Start MySQL:  sudo systemctl start mysql"
        echo "Stop MySQL:   sudo systemctl stop mysql"
        echo "Status:       sudo systemctl status mysql"
    fi
else
    echo "Native Linux Service Commands:"
    echo "Start MySQL:  sudo systemctl start mysql"
    echo "Stop MySQL:   sudo systemctl stop mysql"
    echo "Restart:      sudo systemctl restart mysql"
    echo "Status:       sudo systemctl status mysql"
    echo "Enable:       sudo systemctl enable mysql"
fi

echo ""
echo "🎉 Installation Complete!"
echo ""
echo "⚠️  Important Notes:"
if $IS_WSL; then
    echo "• WSL2 services may not start automatically on boot"
    echo "• You may need to manually start MySQL: sudo service mysql start"
    echo "• Consider adding MySQL start command to your ~/.bashrc"
fi
echo "• Configure MySQL root password before using"
echo "• Test all components before starting development"

# Reload bash profile
source ~/.bashrc 2>/dev/null || true