# 🔐 MintyOTP

> A beautiful, secure desktop TOTP (Time-based One-Time Password) authenticator built with Laravel and NativePHP. Generate and manage your 2FA codes directly from your Mac or Windows desktop.

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://www.php.net/)
[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com/)

## ✨ Features

- 🔐 **Secure TOTP Generation** - Industry-standard algorithms (SHA1, SHA256, SHA512)
- 🔄 **Auto-Refresh Codes** - Codes automatically update every 30 seconds (or custom period)
- 📱 **Multiple Entry Methods** - Add entries via manual input or TOTP URI/QR code
- 💾 **Encrypted Storage** - All secrets are encrypted using Laravel's encryption before storage
- 🎨 **Modern, Professional UI** - Clean, intuitive interface with real-time countdown timers
- 🖥️ **Cross-Platform** - Native desktop app for macOS and Windows
- ⚡ **Fast & Lightweight** - Built with Laravel and Electron for optimal performance
- 🔒 **Local-First** - All data stored locally in SQLite - your secrets never leave your device

## 📸 Screenshots

> *Screenshots coming soon*

## 🚀 Quick Start

### Prerequisites

- **PHP 8.1+** - [Download PHP](https://www.php.net/downloads)
- **Composer** - [Install Composer](https://getcomposer.org/download/)
- **Node.js & npm** - Required for NativePHP/Electron ([Download Node.js](https://nodejs.org/))
- **SQLite Extension** - Usually included with PHP

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/mintyotp.git
   cd mintyotp
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Create environment file**
   ```bash
   cp .env.example .env
   ```

4. **Generate application key**
   ```bash
   php artisan key:generate
   ```

5. **Create SQLite database**
   ```bash
   touch database/database.sqlite
   ```

6. **Run database migrations**
   ```bash
   php artisan migrate
   ```

7. **Install NativePHP Electron dependencies** (if needed)
   ```bash
   php artisan native:install
   ```

## 💻 Development

### Running in Development Mode

Start the development server with hot-reload:

```bash
php artisan native:serve
```

This will:
- Start the Laravel development server
- Launch the Electron window
- Enable hot-reload for instant updates

### Building for Production

The `native:build` command builds for the current platform automatically:

```bash
php artisan native:build
```

**Note:** The command builds for the platform you're running it on:
- On macOS: Builds for macOS (ARM64)
- On Windows: You may need to run the npm scripts directly from `vendor/nativephp/electron/resources/js/`

The built applications will be available in the `dist/` directory.

To build for specific platforms, you can run the npm scripts directly, but you need to set the `APP_PATH` environment variable first:

```bash
# From your project root directory
cd vendor/nativephp/electron/resources/js
export APP_PATH="$(cd ../../../../.. && pwd)"  # Set to your project root
npm run build:mac      # Builds for both ARM64 and x64 macOS
npm run build:mac-arm  # Builds for macOS ARM64 only
npm run build:mac-x86  # Builds for macOS x64 only
npm run build:win      # Builds for Windows
npm run build:linux    # Builds for Linux
```

Or set it from the project root:
```bash
# From your project root
export APP_PATH="$(pwd)"
cd vendor/nativephp/electron/resources/js
npm run build:mac
```

**Note:** It's recommended to use `php artisan native:build` instead, as it automatically sets all required environment variables.

## 📖 Usage

### Main Screen

The main screen displays all your TOTP entries with:
- Current 6-digit (or 8-digit) codes
- Real-time countdown timers
- Visual progress bars
- Quick delete actions

### Adding TOTP Entries

#### Method 1: Manual Entry

1. Click **"+ Add Manual Entry"** from the main screen
2. Enter the account name (e.g., `john@example.com`)
3. Enter the secret key (Base32 encoded)
4. Optionally specify the issuer (e.g., Google, GitHub)
5. Adjust digits (6 or 8) and period (default: 30 seconds) if needed
6. Click **"Add TOTP Entry"**

#### Method 2: From URI/QR Code

1. Click **"+ Add from URI"** from the main screen
2. Paste the complete TOTP URI (format: `otpauth://totp/...`)
   - You can get this from QR codes or export from other authenticator apps
3. Click **"Add from URI"**

### Managing Entries

- **View Codes**: All codes are displayed on the main screen and auto-refresh
- **Delete Entry**: Click the "Delete" button on any TOTP card
- **Copy Code**: Click on any code to copy it to clipboard (coming soon)

## 🔒 Security

MintyOTP takes security seriously:

- ✅ **Encrypted Storage**: All TOTP secrets are encrypted using Laravel's encryption before storing in SQLite
- ✅ **Local-First**: All data is stored locally on your device - no cloud sync, no external servers
- ✅ **Decryption on Demand**: Secrets are only decrypted when needed for code generation
- ✅ **Industry Standards**: Uses RFC 6238 compliant TOTP implementation
- ✅ **Secure Algorithms**: Supports SHA1, SHA256, and SHA512

### Best Practices

- 🔐 Keep your SQLite database file secure and backed up
- 🔐 Use strong system-level encryption for your device
- 🔐 Regularly backup your database file
- 🔐 Don't share your database file or `.env` file

## 🛠️ Technology Stack

- **Backend Framework**: [Laravel 10](https://laravel.com/)
- **Desktop Framework**: [NativePHP](https://nativephp.com/) (Electron)
- **TOTP Library**: [spomky-labs/otphp](https://github.com/Spomky-Labs/otphp) (RFC 6238 compliant)
- **Database**: SQLite
- **Frontend**: Vanilla JavaScript with modern CSS
- **PHP Version**: 8.1+

## 📁 Project Structure

```
MintyOTP/
├── app/
│   ├── Http/Controllers/    # API controllers
│   ├── Models/             # Eloquent models
│   ├── Services/           # Business logic (TOTP service)
│   └── Providers/          # Service providers
├── config/                 # Configuration files
├── database/
│   ├── migrations/         # Database migrations
│   └── database.sqlite     # SQLite database
├── public/                 # Public assets (logo, etc.)
├── resources/
│   └── views/              # Blade templates
├── routes/
│   ├── web.php            # Web routes
│   └── api.php            # API routes
└── vendor/                # Composer dependencies
```

## 🐛 Troubleshooting

### SQLite Extension Not Found

**macOS (Homebrew):**
```bash
brew install php-sqlite3
```

**Linux (Ubuntu/Debian):**
```bash
sudo apt-get install php-sqlite3
```

**Windows:**
Enable the `php_pdo_sqlite.dll` extension in your `php.ini` file.

### NativePHP Not Starting

1. Ensure all dependencies are installed:
   ```bash
   composer install
   npm install
   ```

2. Clear Laravel caches:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

3. Rebuild NativePHP resources:
   ```bash
   php artisan native:install
   ```

### Permission Errors

```bash
chmod -R 775 storage bootstrap/cache
```

### Electron Window Shows Blank Screen

- Ensure the Laravel server is running on the correct port
- Check browser console for JavaScript errors
- Verify the `.env` file is properly configured

## 🤝 Contributing

Contributions are welcome and greatly appreciated! Here's how you can help:

1. **Fork the repository**
2. **Create a feature branch** (`git checkout -b feature/amazing-feature`)
3. **Commit your changes** (`git commit -m 'Add some amazing feature'`)
4. **Push to the branch** (`git push origin feature/amazing-feature`)
5. **Open a Pull Request**

### Development Guidelines

- Follow PSR-12 coding standards
- Write clear commit messages
- Add tests for new features
- Update documentation as needed

## 📝 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 🙏 Acknowledgments

- Built with [Laravel](https://laravel.com/) - The PHP Framework for Web Artisans
- Desktop functionality powered by [NativePHP](https://nativephp.com/)
- TOTP implementation by [spomky-labs/otphp](https://github.com/Spomky-Labs/otphp)

## 📧 Support

- 🐛 **Bug Reports**: [Open an issue](https://github.com/yourusername/mintyotp/issues)
- 💡 **Feature Requests**: [Open an issue](https://github.com/yourusername/mintyotp/issues)
- 📖 **Documentation**: Check the [SETUP.md](SETUP.md) file for detailed setup instructions

---

**Made with ❤️ for secure desktop authentication**
