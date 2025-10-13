#!/usr/bin/env php
<?php
/**
 * Quick Setup Script for Laravel Landscape Grooming Project
 * 
 * This script automates the complete setup process:
 * 1. Generates all Laravel files from CSV catalog
 * 2. Sets up the database
 * 3. Runs migrations
 * 4. Creates sample data
 * 5. Starts the development server
 */

echo "🌿 Laravel Landscape Grooming - Quick Setup\n";
echo "==========================================\n\n";

$basePath = dirname(__DIR__);
chdir($basePath);

// Function to run commands and show output
function runCommand($command, $description) {
    echo "📋 $description\n";
    echo "   → $command\n";
    
    $output = [];
    $returnCode = 0;
    exec($command . ' 2>&1', $output, $returnCode);
    
    if ($returnCode === 0) {
        echo "   ✅ Success\n";
        return true;
    } else {
        echo "   ❌ Failed:\n";
        foreach ($output as $line) {
            echo "      $line\n";
        }
        return false;
    }
}

// Step 1: Check if PHP and Composer are available
echo "🔍 Checking requirements...\n";
if (!runCommand('php --version', 'Checking PHP')) {
    die("❌ PHP is required but not found in PATH\n");
}

if (!runCommand('composer --version', 'Checking Composer')) {
    die("❌ Composer is required but not found in PATH\n");
}

// Step 2: Install dependencies if needed
if (!file_exists('vendor/autoload.php')) {
    if (!runCommand('composer install', 'Installing PHP dependencies')) {
        die("❌ Failed to install dependencies\n");
    }
}

// Step 3: Setup environment
if (!file_exists('.env')) {
    if (file_exists('.env.example')) {
        copy('.env.example', '.env');
        echo "✅ Created .env file\n";
    }
    
    if (!runCommand('php artisan key:generate', 'Generating application key')) {
        echo "⚠ Warning: Could not generate app key\n";
    }
}

// Step 4: Setup SQLite database
$dbPath = 'database/database.sqlite';
if (!file_exists($dbPath)) {
    touch($dbPath);
    echo "✅ Created SQLite database file\n";
}

// Step 5: Generate Laravel files from CSV
echo "\n🏗️ Generating Laravel files from CSV catalog...\n";
if (file_exists('scripts/generate_from_csv.php')) {
    if (!runCommand('php scripts/generate_from_csv.php', 'Generating files from CSV')) {
        echo "⚠ Warning: File generation had issues\n";
    }
} else {
    echo "⚠ Generator script not found, skipping file generation\n";
}

// Step 6: Run migrations
echo "\n📊 Setting up database...\n";
runCommand('php artisan migrate --force', 'Running database migrations');

// Step 7: Clear caches
runCommand('php artisan config:clear', 'Clearing config cache');
runCommand('php artisan route:clear', 'Clearing route cache');
runCommand('php artisan view:clear', 'Clearing view cache');

// Step 8: Create storage link
runCommand('php artisan storage:link', 'Creating storage symlink');

// Step 9: Create admin user (seeder)
$seederCode = <<<'PHP'
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Service;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@landscape.test',
            'role' => 'admin'
        ]);

        // Create staff user
        User::factory()->create([
            'name' => 'Staff Member',
            'email' => 'staff@landscape.test',
            'role' => 'staff'
        ]);

        // Create customer
        User::factory()->create([
            'name' => 'Customer',
            'email' => 'customer@landscape.test',
            'role' => 'customer'
        ]);

        // Create sample services
        Service::create([
            'name' => 'Lawn Mowing',
            'description' => 'Professional lawn mowing service for residential properties.',
            'price' => 50.00,
            'duration' => 60
        ]);

        Service::create([
            'name' => 'Hedge Trimming',
            'description' => 'Expert hedge trimming and shaping services.',
            'price' => 75.00,
            'duration' => 90
        ]);

        Service::create([
            'name' => 'Garden Cleanup',
            'description' => 'Complete garden cleanup including weeding and debris removal.',
            'price' => 100.00,
            'duration' => 120
        ]);
    }
}
PHP;

file_put_contents('database/seeders/DatabaseSeeder.php', $seederCode);
runCommand('php artisan db:seed', 'Creating sample data');

echo "\n🎉 Setup Complete!\n";
echo "================\n\n";

echo "🌐 Your application is ready at:\n";
echo "   → http://127.0.0.1:8000\n";
echo "   → http://landscape_grooming.test:8000 (if hosts file configured)\n\n";

echo "👤 Login credentials:\n";
echo "   Admin:    admin@landscape.test    / password\n";
echo "   Staff:    staff@landscape.test    / password\n";
echo "   Customer: customer@landscape.test / password\n\n";

echo "🚀 To start the development server:\n";
echo "   php artisan serve --host=127.0.0.1 --port=8000\n\n";

echo "💡 VS Code Integration:\n";
echo "   1. Open folder in VS Code\n";
echo "   2. Install recommended extensions\n";
echo "   3. Use Ctrl+Shift+P → 'Tasks: Run Task' → 'Laravel Serve'\n\n";

// Ask if user wants to start the server now
echo "🔥 Start the development server now? [Y/n]: ";
$handle = fopen("php://stdin", "r");
$response = trim(fgets($handle));
fclose($handle);

if (empty($response) || strtolower($response) === 'y') {
    echo "\n🌟 Starting Laravel development server...\n";
    echo "   Press Ctrl+C to stop\n\n";
    passthru('php artisan serve --host=127.0.0.1 --port=8000');
} else {
    echo "\n✅ Setup complete! Run 'php artisan serve' when ready.\n";
}