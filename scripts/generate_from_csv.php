<?php
/**
 * Laravel File Generator from CSV Catalog
 * 
 * This script reads the file_catalog.csv and generates the actual Laravel files
 * with proper directory structure and dependencies.
 */

require_once __DIR__ . '/../vendor/autoload.php';

class LaravelFileGenerator
{
    private $basePath;
    private $csvPath;
    private $codeExtractPath;
    
    public function __construct($basePath)
    {
        $this->basePath = $basePath;
        $this->csvPath = $basePath . '/exports/file_catalog.csv';
        $this->codeExtractPath = $basePath . '/exports/full_project_code.txt';
    }
    
    public function generate()
    {
        echo "🚀 Laravel File Generator Starting...\n";
        
        // Read CSV catalog
        $files = $this->readCsvCatalog();
        
        // Read the full code extract
        $codeContent = file_get_contents($this->codeExtractPath);
        
        // Generate each file
        foreach ($files as $file) {
            $this->generateFile($file, $codeContent);
        }
        
        // Generate additional required files
        $this->generateMissingComponents();
        
        echo "✅ All files generated successfully!\n";
        echo "📁 Run: composer dump-autoload && php artisan migrate\n";
    }
    
    private function readCsvCatalog()
    {
        $files = [];
        $handle = fopen($this->csvPath, 'r');
        
        // Skip header
        fgetcsv($handle);
        
        while (($data = fgetcsv($handle)) !== false) {
            $files[] = [
                'name' => $data[0],
                'path' => $data[1],
                'description' => $data[2],
                'functionality' => $data[3]
            ];
        }
        
        fclose($handle);
        return $files;
    }
    
    private function generateFile($fileInfo, $codeContent)
    {
        $filePath = $this->basePath . '/' . $fileInfo['path'];
        $fileName = $fileInfo['name'];
        
        // Extract code for this specific file from the consolidated code
        $pattern = '/FILE:\s*' . preg_quote($fileInfo['path'], '/') . '\s*.*?(?=FILE:|=============================================================================|$)/s';
        
        if (preg_match($pattern, $codeContent, $matches)) {
            // Clean up the extracted code
            $code = $matches[0];
            
            // Remove the file header comments and extract just the PHP code
            $lines = explode("\n", $code);
            $phpStarted = false;
            $cleanCode = [];
            
            foreach ($lines as $line) {
                if (trim($line) === '<?php') {
                    $phpStarted = true;
                }
                
                if ($phpStarted) {
                    $cleanCode[] = $line;
                }
            }
            
            $finalCode = implode("\n", $cleanCode);
            
            // Ensure directory exists
            $directory = dirname($filePath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }
            
            // Write the file
            file_put_contents($filePath, $finalCode);
            echo "✓ Generated: {$fileInfo['path']}\n";
        } else {
            echo "⚠ Could not extract code for: {$fileInfo['path']}\n";
        }
    }
    
    private function generateMissingComponents()
    {
        echo "\n📋 Generating missing Laravel components...\n";
        
        // Generate Routes
        $this->generateRoutes();
        
        // Generate Policies
        $this->generatePolicies();
        
        // Generate Migrations
        $this->generateMigrations();
        
        // Generate Basic Views
        $this->generateViews();
        
        // Generate Kernel registration
        $this->updateKernel();
        
        // Generate Service Provider updates
        $this->updateServiceProviders();
    }
    
    private function generateRoutes()
    {
        // Web Routes
        $webRoutes = <<<'PHP'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;

// Public routes
Route::get('/', function () {
    return redirect()->route('services.index');
});

Route::resource('services', ServiceController::class)->only(['index', 'show']);

// Authentication routes
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Authenticated routes
Route::middleware(['auth'])->group(function () {
    Route::resource('bookings', BookingController::class);
    Route::post('/bookings/{booking}/assign-staff', [BookingController::class, 'assignStaff'])
         ->name('bookings.assign-staff');
});

// Admin routes
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('services', ServiceController::class)->except(['index', 'show']);
    Route::resource('users', UserController::class)->only(['index', 'edit', 'update']);
});
PHP;

        // API Routes
        $apiRoutes = <<<'PHP'
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;

Route::prefix('v1')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::post('/users/check-mobile', [UserController::class, 'checkMobile']);
});
PHP;

        file_put_contents($this->basePath . '/routes/web.php', $webRoutes);
        file_put_contents($this->basePath . '/routes/api.php', $apiRoutes);
        
        echo "✓ Generated: routes/web.php\n";
        echo "✓ Generated: routes/api.php\n";
    }
    
    private function generatePolicies()
    {
        $servicePolicy = <<<'PHP'
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Service;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view services
    }

    public function view(User $user, Service $service): bool
    {
        return true; // Anyone can view a service
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Service $service): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Service $service): bool
    {
        return $user->isAdmin();
    }
}
PHP;

        $bookingPolicy = <<<'PHP'
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Booking;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Users can view bookings (filtered in controller)
    }

    public function view(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || 
               $booking->customer_id === $user->id || 
               $booking->staff_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isCustomer() || $user->isAdmin();
    }

    public function update(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $booking->customer_id === $user->id;
    }

    public function delete(User $user, Booking $booking): bool
    {
        return $user->isAdmin() || $booking->customer_id === $user->id;
    }

    public function assignStaff(User $user, Booking $booking): bool
    {
        return $user->isAdmin();
    }
}
PHP;

        $policyDir = $this->basePath . '/app/Policies';
        if (!is_dir($policyDir)) {
            mkdir($policyDir, 0755, true);
        }
        
        file_put_contents($policyDir . '/ServicePolicy.php', $servicePolicy);
        file_put_contents($policyDir . '/BookingPolicy.php', $bookingPolicy);
        
        echo "✓ Generated: app/Policies/ServicePolicy.php\n";
        echo "✓ Generated: app/Policies/BookingPolicy.php\n";
    }
    
    private function generateMigrations()
    {
        $timestamp = date('Y_m_d_His');
        
        // Services migration
        $servicesMigration = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->text('description')->nullable();
            \$table->decimal('price', 10, 2);
            \$table->integer('duration')->nullable(); // in minutes
            \$table->string('image_path')->nullable();
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
PHP;

        // Bookings migration
        $bookingsMigration = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint \$table) {
            \$table->id();
            \$table->foreignId('service_id')->constrained()->onDelete('cascade');
            \$table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            \$table->foreignId('staff_id')->nullable()->constrained('users')->onDelete('set null');
            \$table->datetime('scheduled_at');
            \$table->decimal('price', 10, 2);
            \$table->enum('status', ['pending', 'confirmed', 'in_progress', 'completed', 'cancelled'])->default('pending');
            \$table->text('notes')->nullable();
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
PHP;

        // User role migration
        $userRoleMigration = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint \$table) {
            \$table->string('mobile')->nullable()->unique();
            \$table->enum('role', ['customer', 'staff', 'admin'])->default('customer');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint \$table) {
            \$table->dropColumn(['mobile', 'role']);
        });
    }
};
PHP;

        $migrationDir = $this->basePath . '/database/migrations';
        
        file_put_contents($migrationDir . '/2024_01_01_000001_create_services_table.php', $servicesMigration);
        file_put_contents($migrationDir . '/2024_01_01_000002_create_bookings_table.php', $bookingsMigration);
        file_put_contents($migrationDir . '/2024_01_01_000003_add_role_and_mobile_to_users_table.php', $userRoleMigration);
        
        echo "✓ Generated: database/migrations/create_services_table.php\n";
        echo "✓ Generated: database/migrations/create_bookings_table.php\n";
        echo "✓ Generated: database/migrations/add_role_and_mobile_to_users_table.php\n";
    }
    
    private function generateViews()
    {
        // Create basic view structure
        $viewDirs = [
            'resources/views/layouts',
            'resources/views/auth',
            'resources/views/services',
            'resources/views/bookings',
            'resources/views/admin/users'
        ];
        
        foreach ($viewDirs as $dir) {
            $fullPath = $this->basePath . '/' . $dir;
            if (!is_dir($fullPath)) {
                mkdir($fullPath, 0755, true);
            }
        }
        
        // Generate basic layout
        $layout = <<<'BLADE'
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landscape Grooming - @yield('title', 'Service Booking')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body class="bucolic-bg">
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="{{ route('services.index') }}">🌿 Landscape Grooming</a>
            
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="{{ route('services.index') }}">Services</a>
                
                @auth
                    <a class="nav-link" href="{{ route('bookings.index') }}">My Bookings</a>
                    
                    @if(auth()->user()->isAdmin())
                        <a class="nav-link" href="{{ route('admin.dashboard') }}">Admin Dashboard</a>
                    @endif
                    
                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link nav-link">Logout</button>
                    </form>
                @else
                    <a class="nav-link" href="{{ route('login') }}">Login</a>
                    <a class="nav-link" href="{{ route('register') }}">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="container mt-4">
        @if(session('status'))
            <div class="alert alert-success">{{ session('status') }}</div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @yield('scripts')
</body>
</html>
BLADE;

        // Services index view
        $servicesIndex = <<<'BLADE'
@extends('layouts.app')

@section('title', 'Our Services')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Our Landscaping Services</h1>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.services.create') }}" class="btn btn-success">Add New Service</a>
                @endif
            @endauth
        </div>
        
        <div class="row">
            @forelse($services as $service)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        @if($service->image_path)
                            <img src="{{ asset('storage/' . $service->image_path) }}" class="card-img-top" alt="{{ $service->name }}">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $service->name }}</h5>
                            <p class="card-text">{{ Str::limit($service->description, 100) }}</p>
                            <p class="text-success fw-bold">${{ number_format($service->price, 2) }}</p>
                            @if($service->duration)
                                <small class="text-muted">Duration: {{ $service->duration }} minutes</small>
                            @endif
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('services.show', $service) }}" class="btn btn-primary">View Details</a>
                            @auth
                                <a href="{{ route('bookings.create', ['service_id' => $service->id]) }}" class="btn btn-success">Book Now</a>
                            @endauth
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info">No services available at the moment.</div>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
BLADE;

        file_put_contents($this->basePath . '/resources/views/layouts/app.blade.php', $layout);
        file_put_contents($this->basePath . '/resources/views/services/index.blade.php', $servicesIndex);
        
        echo "✓ Generated: resources/views/layouts/app.blade.php\n";
        echo "✓ Generated: resources/views/services/index.blade.php\n";
    }
    
    private function updateKernel()
    {
        $kernelContent = <<<'PHP'
<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    protected $middleware = [
        // Global middleware
    ];

    protected $middlewareGroups = [
        'web' => [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

    protected $middlewareAliases = [
        'auth' => \Illuminate\Auth\Middleware\Authenticate::class,
        'role' => \App\Http\Middleware\RoleMiddleware::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
    ];
}
PHP;

        file_put_contents($this->basePath . '/app/Http/Kernel.php', $kernelContent);
        echo "✓ Generated: app/Http/Kernel.php\n";
    }
    
    private function updateServiceProviders()
    {
        $authServiceProvider = <<<'PHP'
<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Service;
use App\Models\Booking;
use App\Policies\ServicePolicy;
use App\Policies\BookingPolicy;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Service::class => ServicePolicy::class,
        Booking::class => BookingPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
PHP;

        file_put_contents($this->basePath . '/app/Providers/AuthServiceProvider.php', $authServiceProvider);
        echo "✓ Generated: app/Providers/AuthServiceProvider.php\n";
    }
}

// Run the generator
if (php_sapi_name() === 'cli') {
    $basePath = dirname(__DIR__);
    $generator = new LaravelFileGenerator($basePath);
    $generator->generate();
}