<?php
/**
 * Improved Laravel File Extractor
 * Extracts clean Laravel files from the consolidated text file
 */

class LaravelFileExtractor
{
    private $basePath;
    private $sourceFile;
    
    public function __construct($basePath)
    {
        $this->basePath = $basePath;
        $this->sourceFile = $basePath . '/exports/full_project_code.txt';
    }
    
    public function extract()
    {
        echo "🚀 Extracting Laravel files from consolidated code...\n";
        
        if (!file_exists($this->sourceFile)) {
            die("❌ Source file not found: {$this->sourceFile}\n");
        }
        
        $content = file_get_contents($this->sourceFile);
        
        // Parse and extract files
        $this->extractModels($content);
        $this->extractMiddleware($content);
        $this->extractControllers($content);
        $this->generateAdditionalFiles();
        
        echo "✅ All files extracted successfully!\n";
        echo "📁 Run: composer dump-autoload\n";
    }
    
    private function extractModels($content)
    {
        echo "\n📦 Extracting Models...\n";
        
        // Extract User Model
        $userCode = $this->extractFileSection($content, 'app/Models/User.php');
        $this->writeFile('app/Models/User.php', $userCode);
        
        // Extract Service Model  
        $serviceCode = $this->extractFileSection($content, 'app/Models/Service.php');
        $this->writeFile('app/Models/Service.php', $serviceCode);
        
        // Extract Booking Model
        $bookingCode = $this->extractFileSection($content, 'app/Models/Booking.php');
        $this->writeFile('app/Models/Booking.php', $bookingCode);
    }
    
    private function extractMiddleware($content)
    {
        echo "\n🛡️ Extracting Middleware...\n";
        
        $middlewareCode = $this->extractFileSection($content, 'app/Http/Middleware/RoleMiddleware.php');
        $this->writeFile('app/Http/Middleware/RoleMiddleware.php', $middlewareCode);
    }
    
    private function extractControllers($content)
    {
        echo "\n🎮 Extracting Controllers...\n";
        
        $controllers = [
            'app/Http/Controllers/AuthController.php',
            'app/Http/Controllers/ServiceController.php',
            'app/Http/Controllers/UserController.php',
            'app/Http/Controllers/BookingController.php',
            'app/Http/Controllers/DashboardController.php',
            'app/Http/Controllers/Api/UserController.php'
        ];
        
        foreach ($controllers as $controllerPath) {
            $code = $this->extractFileSection($content, $controllerPath);
            $this->writeFile($controllerPath, $code);
        }
    }
    
    private function extractFileSection($content, $filePath)
    {
        // Find the file section using the FILE: marker
        $pattern = '/FILE:\s*' . preg_quote($filePath, '/') . '\s*.*?\n\n(.*?)(?=\n\n-{70,}|=============================================================================|$)/s';
        
        if (preg_match($pattern, $content, $matches)) {
            $code = trim($matches[1]);
            
            // Clean up the code - remove any remaining separators
            $lines = explode("\n", $code);
            $cleanLines = [];
            
            foreach ($lines as $line) {
                // Skip separator lines
                if (preg_match('/^-{5,}/', $line) || preg_match('/^={5,}/', $line)) {
                    continue;
                }
                $cleanLines[] = $line;
            }
            
            return implode("\n", $cleanLines);
        }
        
        echo "⚠ Could not extract: $filePath\n";
        return '';
    }
    
    private function writeFile($relativePath, $content)
    {
        if (empty($content)) {
            return;
        }
        
        $fullPath = $this->basePath . '/' . $relativePath;
        $directory = dirname($fullPath);
        
        // Create directory if it doesn't exist
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }
        
        // Write the file
        file_put_contents($fullPath, $content);
        echo "✓ Extracted: $relativePath\n";
    }
    
    private function generateAdditionalFiles()
    {
        echo "\n🔧 Generating additional files...\n";
        
        // Generate Controller base class
        $this->generateBaseController();
        
        // Generate Kernel
        $this->generateKernel();
        
        // Generate Policies
        $this->generatePolicies();
        
        // Generate Routes
        $this->generateRoutes();
        
        // Generate Views
        $this->generateViews();
        
        // Generate AuthServiceProvider
        $this->generateAuthServiceProvider();
    }
    
    private function generateBaseController()
    {
        $content = <<<'PHP'
<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}
PHP;
        $this->writeFile('app/Http/Controllers/Controller.php', $content);
    }
    
    private function generateKernel()
    {
        $content = <<<'PHP'
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
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
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
        $this->writeFile('app/Http/Kernel.php', $content);
    }
    
    private function generatePolicies()
    {
        // Service Policy
        $servicePolicy = <<<'PHP'
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Service;

class ServicePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Service $service): bool
    {
        return true;
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

        // Booking Policy
        $bookingPolicy = <<<'PHP'
<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Booking;

class BookingPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
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

        $this->writeFile('app/Policies/ServicePolicy.php', $servicePolicy);
        $this->writeFile('app/Policies/BookingPolicy.php', $bookingPolicy);
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

        $this->writeFile('routes/web.php', $webRoutes);
        $this->writeFile('routes/api.php', $apiRoutes);
    }
    
    private function generateViews()
    {
        // Main Layout
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
                        <button type="submit" class="btn btn-link nav-link text-light">Logout</button>
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

        // Services Index
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

        $this->writeFile('resources/views/layouts/app.blade.php', $layout);
        $this->writeFile('resources/views/services/index.blade.php', $servicesIndex);
    }
    
    private function generateAuthServiceProvider()
    {
        $content = <<<'PHP'
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
        $this->writeFile('app/Providers/AuthServiceProvider.php', $content);
    }
}

// Run the extractor
if (php_sapi_name() === 'cli') {
    $basePath = dirname(__DIR__);
    $extractor = new LaravelFileExtractor($basePath);
    $extractor->extract();
}