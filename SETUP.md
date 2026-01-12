# Setup Instructions for MintyOTP

Follow these steps to set up and run your MintyOTP desktop application:

## Prerequisites

1. **PHP 8.1+** - Make sure PHP is installed
   ```bash
   php --version
   ```

2. **Composer** - PHP package manager
   ```bash
   composer --version
   ```

3. **Node.js and npm** - Required for NativePHP/Electron
   ```bash
   node --version
   npm --version
   ```

## Installation Steps

1. **Install PHP Dependencies**
   ```bash
   composer install
   ```

2. **Create Environment File**
   ```bash
   cp .env.example .env
   ```

3. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

4. **Create SQLite Database**
   ```bash
   touch database/database.sqlite
   ```

5. **Run Database Migrations**
   ```bash
   php artisan migrate
   ```

6. **Set Permissions (if needed)**
   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

## Running the Application

### Development Mode

To run the app in development mode with hot-reload:

```bash
php artisan native:serve
```

Or using npm:

```bash
npm run dev
```

This will:
- Start the Laravel development server
- Open the NativePHP Electron window
- Enable hot-reload for development

### Building for Production

#### macOS
```bash
php artisan native:build mac
```

#### Windows
```bash
php artisan native:build windows
```

The built applications will be available in the `dist/` directory.

## Troubleshooting

### SQLite Extension Not Found

If you get an error about SQLite extension:

**macOS (Homebrew):**
```bash
brew install php-sqlite3
```

**Linux:**
```bash
sudo apt-get install php-sqlite3
```

**Windows:**
Enable the `php_pdo_sqlite.dll` extension in your `php.ini` file.

### Permission Errors

If you encounter permission errors with storage:

```bash
chmod -R 775 storage bootstrap/cache
```

### NativePHP Not Found

Make sure you've installed all Composer dependencies:

```bash
composer install
```

If NativePHP package is not found, you may need to add it manually or check the package availability.

## Next Steps

1. Open the application
2. Add your first TOTP entry using either:
   - Manual entry (secret key)
   - QR Code / URI (paste the TOTP URI)
3. Your TOTP codes will auto-refresh every 30 seconds (or your custom period)

Enjoy using MintyOTP! 🔐
