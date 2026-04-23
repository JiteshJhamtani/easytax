# EasyTax - Deployment Guide

This guide provides step-by-step instructions on how to deploy this Laravel 12 application to a live server (like a VPS, cPanel shared hosting, or dedicated server).

Since you are receiving the project files directly (bypassing Git), follow these instructions to get the application up and running.

## Prerequisites

Before deploying, ensure the target server meets the following requirements:
* **PHP**: 8.2 or higher
* **Required PHP Extensions**: BCMath, Ctype, cURL, DOM, Fileinfo, JSON, Mbstring, OpenSSL, PCRE, PDO, Tokenizer, XML (these are usually enabled by default on most modern hosting).
* **Database**: MySQL 8.0+ or MariaDB 10.3+
* **Web Server**: Apache or Nginx
* **Composer**: (Optional but recommended) for managing PHP dependencies if the `vendor` directory was not included in the transfer.

---

## Step 1: Upload and Extract Files

1. Compress all your project files into a `.zip` archive on your local machine.
2. Upload the `.zip` file to your server (via FTP, cPanel File Manager, or SSH).
3. Extract the files to the appropriate directory on your server.
   * *Note for cPanel/Shared Hosting:* It is a best practice to extract the core Laravel files **outside** of the `public_html` directory, and only place the contents of Laravel's `public/` directory inside `public_html`. If you must place everything inside `public_html`, ensure your web server configuration directs traffic straight to the `public/` folder so your `.env` file is not exposed.
   * *Note for VPS (Nginx/Apache):* Extract to a directory like `/var/www/easytax`.

## Step 2: Set Up the Database

1. Log in to your database management tool (e.g., phpMyAdmin, TablePlus, or MySQL CLI).
2. Create a new, empty database (e.g., `easytax_db`).
3. Create a database user and assign it full privileges to the new database.
4. **Import the Database:** Import the provided `.sql` file into this new database. This file contains all the tables and initial data required to run the application.

## Step 3: Environment Configuration (.env)

1. Navigate to the root folder of your extracted project on the server.
2. Look for the `.env` file. If it does not exist, copy `.env.example` and rename it to `.env`.
3. Open the `.env` file in a text editor and update the following critical settings:

   ```env
   APP_NAME="EasyTax"
   APP_ENV=production
   APP_KEY= # (Leave this as is if it's already filled, otherwise run `php artisan key:generate` via SSH)
   APP_DEBUG=false
   APP_URL=https://yourdomain.com # Very Important: Set this to your actual live URL starting with https://

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=your_new_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```

## Step 4: Install Dependencies (If needed)

*If the `vendor` folder was already included in the files you uploaded, you can skip this step.*

If the `vendor` folder is missing, you must install the PHP dependencies via SSH:
1. Connect to your server via SSH.
2. Navigate to your project root folder: `cd /path/to/easytax`
3. Run: `composer install --optimize-autoloader --no-dev`

*(Note: The frontend assets (CSS/JS) have already been compiled and should be located in the `public/build` directory, so Node.js and `npm` are not required to run the application in production unless you plan to modify the frontend code).*

## Step 5: Directory Permissions

Laravel needs write permissions to certain directories to store logs, cache, and uploaded files. Run the following commands via SSH, or set the permissions to `775` via your FTP client/File Manager for these folders:

```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```
*Ensure the web server user (like `www-data` or the cPanel user) owns these directories.*

## Step 6: Create the Storage Symlink

If your application handles file uploads (like user profile pictures or application attachments), you need to link the public storage directory so files are accessible from the web.

Via SSH in the project root folder, run:
```bash
php artisan storage:link
```
*(If you are on shared hosting without SSH access, you can sometimes run this programmatically by creating a temporary route in `routes/web.php` that calls `Artisan::call('storage:link');`, visiting that route once, and then removing it).*

## Step 7: Clear Application Caches

It is highly recommended to clear the cache to ensure no old configuration or routing data interferes with the live site. Via SSH, run:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Step 8: Configure the Web Server

Ensure your web server (Apache/Nginx) is securely pointing to the `public` directory.

### Nginx Example
```nginx
server {
    listen 80;
    server_name yourdomain.com;
    root /var/www/easytax/public; # IMPORTANT: Point to the public directory

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### Apache Example (Usually handled automatically by cPanel)
Ensure the `DocumentRoot` in your VirtualHost block points to the `public/` directory, e.g., `DocumentRoot /var/www/easytax/public`.

## Troubleshooting

- **500 Server Error**: Check the `storage/logs/laravel.log` file for the exact error. Usually, this is caused by incorrect database credentials in the `.env` file, missing vendor dependencies, or incorrect folder permissions on `storage` or `bootstrap/cache`.
- **404 Page Not Found on Pages Other Than Home**: This is usually a web server configuration issue. Ensure URL rewriting is enabled (e.g., `mod_rewrite` is enabled on Apache) and that the `.htaccess` file inside the `public/` folder uploaded successfully.
- **Images/Uploads are broken**: Ensure you ran `php artisan storage:link` in Step 6.
- **Vite Manifest Not Found**: Ensure the `public/build` directory exists and has files in it. If it doesn't, you need to run `npm install` and `npm run build` locally, then upload the `public/build` folder to the server.
