# Laravel Landscape Grooming - Development Guide

## 🚀 Quick Start (Automated)

### Option 1: PowerShell (Windows - Recommended)
```powershell
# Run the automated setup
.\scripts\quick_setup.ps1
```

### Option 2: PHP Script (Cross-platform)
```bash
php scripts/quick_setup.php
```

### Option 3: VS Code Integration
1. Open folder in VS Code
2. Install recommended extensions when prompted
3. Press `Ctrl+Shift+P` → `Tasks: Run Task` → `Setup Laravel Project`

## 📋 Manual Setup Steps

If you prefer manual setup or the automated scripts fail:

### 1. Install Dependencies
```powershell
composer install
npm install
```

### 2. Environment Setup
```powershell
copy .env.example .env
php artisan key:generate
```

### 3. Database Setup
```powershell
# Create SQLite file (Windows)
New-Item -ItemType File database\database.sqlite

# Create SQLite file (Unix/Linux/Mac)
touch database/database.sqlite
```

### 4. Generate Laravel Files from CSV
```powershell
php scripts/generate_from_csv.php
```

### 5. Run Migrations and Seed Data
```powershell
php artisan migrate
php artisan db:seed
```

### 6. Create Storage Link
```powershell
php artisan storage:link
```

### 7. Start Development Server
```powershell
php artisan serve --host=127.0.0.1 --port=8000
```

## 🌐 Access Your Application

- **Local URL**: http://127.0.0.1:8000
- **Custom Domain**: http://landscape_grooming.test:8000 (requires hosts file setup)

### Login Credentials
- **Admin**: admin@landscape.test / password
- **Staff**: staff@landscape.test / password  
- **Customer**: customer@landscape.test / password

## 🛠️ VS Code Features

### Recommended Extensions
- **Intelephense** - PHP IntelliSense and debugging
- **Laravel Blade Snippets** - Blade template support
- **Laravel Extension Pack** - Complete Laravel tooling
- **Tailwind CSS IntelliSense** - CSS framework support

### Available Tasks (Ctrl+Shift+P → Tasks: Run Task)
- `Generate Laravel Files from CSV` - Regenerate files from CSV catalog
- `Laravel Migrate` - Run database migrations
- `Laravel Serve` - Start development server
- `Setup Laravel Project` - Complete automated setup

### Debug Configuration
- F5 to start debugging
- Breakpoints supported in PHP files
- Integrated terminal for artisan commands

## 📊 Project Structure

### Generated from CSV Catalog
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── ServiceController.php
│   │   ├── BookingController.php
│   │   ├── DashboardController.php
│   │   ├── UserController.php
│   │   └── Api/
│   │       └── UserController.php
│   └── Middleware/
│       └── RoleMiddleware.php
├── Models/
│   ├── User.php
│   ├── Service.php
│   └── Booking.php
└── Policies/
    ├── ServicePolicy.php
    └── BookingPolicy.php

database/
└── migrations/
    ├── create_services_table.php
    ├── create_bookings_table.php
    └── add_role_and_mobile_to_users_table.php

resources/views/
├── layouts/
│   └── app.blade.php
├── services/
│   └── index.blade.php
└── auth/
```

## 🔧 Development Workflow

### 1. Modify CSV Catalog
Edit `exports/file_catalog.csv` to add/modify file descriptions

### 2. Update Code Extract
Edit `exports/full_project_code.txt` with new/modified code

### 3. Regenerate Files
```powershell
php scripts/generate_from_csv.php
```

### 4. Run Migrations (if schema changed)
```powershell
php artisan migrate
```

### 5. Clear Caches
```powershell
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 🎯 Features Overview

### Core Functionality
- **Multi-role Authentication** (Admin, Staff, Customer)
- **Service Management** (CRUD operations for admins)
- **Booking System** (Customer bookings with staff assignment)
- **Admin Dashboard** (Metrics and user management)
- **RESTful API** (Mobile/JS client support)

### Security
- Role-based middleware protection
- Laravel authorization policies
- CSRF protection
- Input validation

### Database
- SQLite for development (easy setup)
- Proper foreign key relationships
- Migration-based schema management
- Factory-based test data

## 🐛 Troubleshooting

### Common Issues

#### "No such table: services" Error
```powershell
php artisan migrate
```

#### Missing Storage Directory
```powershell
php artisan storage:link
```

#### Permission Errors (Linux/Mac)
```bash
chmod -R 775 storage bootstrap/cache
```

#### VS Code PHP IntelliSense Not Working
1. Install Intelephense extension
2. Reload VS Code window
3. Check PHP executable path in settings

### File Generation Issues
If CSV generation fails:
1. Check `exports/file_catalog.csv` format
2. Verify `exports/full_project_code.txt` exists
3. Ensure proper file separators in code extract

## 📚 API Documentation

### Endpoints
- `GET /api/v1/users` - List users (paginated)
- `POST /api/v1/users` - Create user
- `GET /api/v1/users/{id}` - Get user details
- `PUT /api/v1/users/{id}` - Update user
- `DELETE /api/v1/users/{id}` - Delete user
- `POST /api/v1/users/check-mobile` - Check mobile availability

### Authentication
- Web: Session-based authentication
- API: Add Laravel Sanctum for token-based auth (optional)

## 🚀 Production Deployment

### Environment Variables
Update `.env` for production:
```env
APP_ENV=production
APP_DEBUG=false
DB_CONNECTION=mysql  # or postgresql
# Add proper database credentials
```

### Optimization Commands
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

### Web Server Configuration
- Point document root to `public/` directory
- Enable URL rewriting for Laravel routes
- Set proper file permissions

## 🎨 Customization

### Adding New Files to CSV System
1. Add entry to `exports/file_catalog.csv`
2. Add code to `exports/full_project_code.txt`
3. Update generator script if needed
4. Run regeneration

### Styling
- Modify `resources/css/app.css` for custom styles
- Views use Bootstrap 5 (CDN) by default
- Blade templates in `resources/views/`

### Database Changes
1. Create new migration: `php artisan make:migration`
2. Update models and relationships
3. Regenerate files if structure changes
4. Run migrations

---

**Need Help?** Check the generated files in `exports/` directory or examine the VS Code tasks for automation options.