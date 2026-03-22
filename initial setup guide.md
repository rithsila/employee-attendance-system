# Proxmox LXC Deployment & Setup Guide

This guide covers how to set up the Employee Attendance System on a fresh Proxmox LXC container running **Ubuntu 22.04** (or Debian). It includes installing the required LEMP stack (Linux, Nginx, MySQL, PHP) and Node.js.

---

## 1. System Update & Prerequisites
First, SSH into your Proxmox LXC container and update the package lists:

```bash
apt update && apt upgrade -y
apt install -y software-properties-common curl git unzip zip nano
```

## 2. Install PHP 8.1 and Extensions
Laravel 9 requires PHP 8.0.2+, and PHP 8.1 is highly recommended.

```bash
# Add the ondrej PPA for PHP
add-apt-repository ppa:ondrej/php -y
apt update

# Install PHP 8.1 and required extensions for Laravel
apt install -y php8.1-fpm php8.1-mysql php8.1-mbstring php8.1-xml php8.1-bcmath php8.1-curl php8.1-zip php8.1-intl
```

## 3. Install Composer
Composer is needed to install Laravel's PHP dependencies.

```bash
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

## 4. Install Node.js & NPM
Node.js is used to compile the frontend assets (Laravel Mix).

```bash
# Install Node.js (v18 is a stable LTS version suitable for Laravel Mix/Webpack)
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt install -y nodejs
```

## 5. Install & Configure MySQL/MariaDB
```bash
# Install MariaDB (drop-in replacement for MySQL)
apt install -y mariadb-server

# Secure the installation (optional but recommended)
mysql_secure_installation

# Log into the MariaDB shell to create the database and user
mysql -u root
```

Inside the MySQL shell, run the following commands (replace `your_secure_password` with a strong password):

```sql
CREATE DATABASE absensi_app;
CREATE USER 'absensi_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON absensi_app.* TO 'absensi_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

## 6. Clone the Repository & Install Dependencies
Move to your web directory (`/var/www`) and clone the project.

```bash
# Assuming you are cloning into /var/www/employee-attendance-system
cd /var/www
# git clone <your-repo-url> employee-attendance-system
cd employee-attendance-system

# Install PHP dependencies
composer install --no-interaction --optimize-autoloader --no-dev

# Install NPM dependencies and compile frontend
npm install
npm run build
# OR run `npm run production` or `npm run dev` depending on your mix setup
```

## 7. Configure the Environment
Set up the `.env` file credentials mapping to the database we just made:

```bash
cp .env.example .env
nano .env
```

Update your `.env` to match the MySQL credentials you created:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=http://your-lxc-ip-or-domain

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=absensi_app
DB_USERNAME=absensi_user
DB_PASSWORD=your_secure_password
```

Generate the app key and run migrations/seeders:
```bash
php artisan key:generate
php artisan migrate --seed --force

# Symlink the storage directory so files (like QR codes/images) can be accessed publicly
php artisan storage:link
```

### Default Login Credentials (from the seeder):
- **Email:** `admin@gmail.com`
- **Password:** `password`

## 8. Set File Permissions
Give the web server (Nginx/www-data) permission to read and write to the Laravel `storage` and `bootstrap/cache` folders.

```bash
chown -R www-data:www-data /var/www/employee-attendance-system
chmod -R 775 /var/www/employee-attendance-system/storage
chmod -R 775 /var/www/employee-attendance-system/bootstrap/cache
```

## 9. Install & Configure Nginx
Install the Nginx web server:
```bash
apt install -y nginx
```

Create a new Nginx virtual host configuration:
```bash
nano /etc/nginx/sites-available/absensi_app
```

Paste the following configuration (replace `your_lxc_ip` with your container's IP or domain name):

```nginx
server {
    listen 80;
    server_name your_lxc_ip;
    root /var/www/employee-attendance-system/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the Nginx site and restart services:
```bash
ln -s /etc/nginx/sites-available/absensi_app /etc/nginx/sites-enabled/
# Remove the default nginx page
rm /etc/nginx/sites-enabled/default

# Test config and restart Nginx
nginx -t
systemctl restart nginx
systemctl restart php8.1-fpm
```

## 10. Access the Application
You should now be able to visit `http://your-lxc-ip` in your browser and log in with the default admin credentials!
