<?php
// scripts/populate_mysql.php
// Run: php scripts/populate_mysql.php
// This script uses mysqli to insert sample data into a MySQL database used by the Laravel app.
// It is idempotent: it checks for existing users/services by unique fields and reuses them.

// Configuration: reads from .env (project root) if present, otherwise falls back to defaults below.
$envPathCandidates = [__DIR__ . '/../.env', __DIR__ . '/../.env.example'];
$env = [];
foreach ($envPathCandidates as $p) {
    $p = realpath($p) ?: $p;
    if (is_readable($p)) {
        $lines = file($p, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#') continue;
            if (strpos($line, '=') === false) continue;
            [$k, $v] = explode('=', $line, 2);
            $env[trim($k)] = trim($v);
        }
        break;
    }
}

// Defaults (safe local defaults)
$dbHost = $env['DB_HOST'] ?? getenv('DB_HOST') ?: '127.0.0.1';
$dbPort = $env['DB_PORT'] ?? getenv('DB_PORT') ?: '3306';
$dbName = $env['DB_DATABASE'] ?? getenv('DB_DATABASE') ?: 'landscape_grooming';
$dbUser = $env['DB_USERNAME'] ?? getenv('DB_USERNAME') ?: 'root';
$dbPass = $env['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?: '';

echo "Using DB host={$dbHost} port={$dbPort} db={$dbName} user={$dbUser}\n";

$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName, (int)$dbPort);
if ($mysqli->connect_errno) {
    fwrite(STDERR, "ERROR: Could not connect to MySQL: ({$mysqli->connect_errno}) {$mysqli->connect_error}\n");
    exit(1);
}
$mysqli->set_charset('utf8mb4');

// Helper: run query and show error
function execOrDie(mysqli $m, string $sql) {
    if (!$m->query($sql)) {
        fwrite(STDERR, "SQL Error: " . $m->error . "\nQuery: " . $sql . "\n");
        exit(1);
    }
}

// Ensure required tables exist (lightweight checks). If the tables are managed by Laravel migrations, skip.
$requiredTables = ['users', 'services', 'bookings'];
$missing = [];
$res = $mysqli->query("SHOW TABLES");
$tables = [];
while ($row = $res->fetch_array(MYSQLI_NUM)) {
    $tables[] = $row[0];
}
foreach ($requiredTables as $t) {
    if (!in_array($t, $tables)) $missing[] = $t;
}

if (!empty($missing)) {
    echo "Note: some tables are missing: " . implode(', ', $missing) . "\n";
    echo "This script will attempt to create minimal tables compatible with the migrations.\n";
    // Create minimal tables matching the migrations in this repo
    // users
    if (in_array('users', $missing)) {
        $sql = "CREATE TABLE users (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            email_verified_at TIMESTAMP NULL DEFAULT NULL,
            password VARCHAR(255) NOT NULL,
            mobile VARCHAR(255) NULL UNIQUE,
            role VARCHAR(50) NOT NULL DEFAULT 'customer',
            remember_token VARCHAR(100) NULL,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        execOrDie($mysqli, $sql);
        echo "Created table users\n";
    }
    // services
    if (in_array('services', $missing)) {
        $sql = "CREATE TABLE services (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT NULL,
            price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            duration INT NOT NULL DEFAULT 60,
            image_path VARCHAR(255) NULL,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        execOrDie($mysqli, $sql);
        echo "Created table services\n";
    }
    // bookings
    if (in_array('bookings', $missing)) {
        $sql = "CREATE TABLE bookings (
            id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            service_id BIGINT UNSIGNED NOT NULL,
            customer_id BIGINT UNSIGNED NOT NULL,
            staff_id BIGINT UNSIGNED NULL,
            scheduled_at TIMESTAMP NOT NULL,
            price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            status ENUM('pending','confirmed','in_progress','completed','cancelled') NOT NULL DEFAULT 'pending',
            notes TEXT NULL,
            created_at TIMESTAMP NULL DEFAULT NULL,
            updated_at TIMESTAMP NULL DEFAULT NULL,
            FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE,
            FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (staff_id) REFERENCES users(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        execOrDie($mysqli, $sql);
        echo "Created table bookings\n";
    }
}

// Idempotent helpers
function findUserByEmail(mysqli $m, string $email) {
    $stmt = $m->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->bind_result($id);
    $found = $stmt->fetch() ? $id : null;
    $stmt->close();
    return $found;
}
function createUserIfNotExists(mysqli $m, array $data) {
    $existing = findUserByEmail($m, $data['email']);
    if ($existing) return $existing;
    $stmt = $m->prepare('INSERT INTO users (name, email, password, mobile, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
    $stmt->bind_param('sssss', $data['name'], $data['email'], $data['password'], $data['mobile'], $data['role']);
    if (!$stmt->execute()) {
        fwrite(STDERR, "Failed to insert user: " . $stmt->error . "\n");
        $stmt->close();
        exit(1);
    }
    $id = $m->insert_id;
    $stmt->close();
    echo "Inserted user {$data['email']} (id={$id})\n";
    return $id;
}

function findServiceByName(mysqli $m, string $name) {
    $stmt = $m->prepare('SELECT id FROM services WHERE name = ? LIMIT 1');
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $stmt->bind_result($id);
    $found = $stmt->fetch() ? $id : null;
    $stmt->close();
    return $found;
}
function createServiceIfNotExists(mysqli $m, array $data) {
    $existing = findServiceByName($m, $data['name']);
    if ($existing) return $existing;
    $stmt = $m->prepare('INSERT INTO services (name, description, price, duration, image_path, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
    $stmt->bind_param('ssdis', $data['name'], $data['description'], $data['price'], $data['duration'], $data['image_path']);
    if (!$stmt->execute()) {
        fwrite(STDERR, "Failed to insert service: " . $stmt->error . "\n");
        $stmt->close();
        exit(1);
    }
    $id = $m->insert_id;
    $stmt->close();
    echo "Inserted service {$data['name']} (id={$id})\n";
    return $id;
}

function createBookingIfNotExists(mysqli $m, array $data) {
    // Determine existence by service, customer, scheduled_at
    $stmt = $m->prepare('SELECT id FROM bookings WHERE service_id = ? AND customer_id = ? AND scheduled_at = ? LIMIT 1');
    $stmt->bind_param('iis', $data['service_id'], $data['customer_id'], $data['scheduled_at']);
    if (!$stmt->execute()) {
        fwrite(STDERR, "Failed to query bookings: " . $stmt->error . "\n");
        $stmt->close();
        exit(1);
    }
    $stmt->bind_result($id);
    if ($stmt->fetch()) {
        $stmt->close();
        echo "Booking already exists (id={$id})\n";
        return $id;
    }
    $stmt->close();

    $stmt = $m->prepare('INSERT INTO bookings (service_id, customer_id, staff_id, scheduled_at, price, status, notes, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())');
    $stmt->bind_param('iiisdss', $data['service_id'], $data['customer_id'], $data['staff_id'], $data['scheduled_at'], $data['price'], $data['status'], $data['notes']);
    if (!$stmt->execute()) {
        fwrite(STDERR, "Failed to insert booking: " . $stmt->error . "\n");
        $stmt->close();
        exit(1);
    }
    $id = $m->insert_id;
    $stmt->close();
    echo "Inserted booking id={$id} (service_id={$data['service_id']} customer_id={$data['customer_id']})\n";
    return $id;
}

// Start populating
$mysqli->begin_transaction();
try {
    // Users: admin, staff, customer
    $adminId = createUserIfNotExists($mysqli, [
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => password_hash('password', PASSWORD_BCRYPT),
        'mobile' => '5550000001',
        'role' => 'admin'
    ]);

    $staffId = createUserIfNotExists($mysqli, [
        'name' => 'Staff Member',
        'email' => 'staff@example.com',
        'password' => password_hash('password', PASSWORD_BCRYPT),
        'mobile' => '5550000002',
        'role' => 'staff'
    ]);

    $customerId = createUserIfNotExists($mysqli, [
        'name' => 'Jane Customer',
        'email' => 'jane@example.com',
        'password' => password_hash('password', PASSWORD_BCRYPT),
        'mobile' => '5550000003',
        'role' => 'customer'
    ]);

    // Services
    $service1 = createServiceIfNotExists($mysqli, [
        'name' => 'Lawn Mowing',
        'description' => 'Standard lawn mowing service.',
        'price' => 45.00,
        'duration' => 60,
        'image_path' => null
    ]);

    $service2 = createServiceIfNotExists($mysqli, [
        'name' => 'Hedge Trimming',
        'description' => 'Professional hedge trimming.',
        'price' => 80.00,
        'duration' => 90,
        'image_path' => null
    ]);

    $service3 = createServiceIfNotExists($mysqli, [
        'name' => 'Seasonal Cleanup',
        'description' => 'Leaves and debris removal.',
        'price' => 120.00,
        'duration' => 180,
        'image_path' => null
    ]);

    // Bookings (use ISO datetime strings)
    $now = new DateTimeImmutable();
    $booking1At = $now->modify('+2 days')->setTime(9, 0)->format('Y-m-d H:i:s');
    $booking2At = $now->modify('+3 days')->setTime(13, 30)->format('Y-m-d H:i:s');

    createBookingIfNotExists($mysqli, [
        'service_id' => $service1,
        'customer_id' => $customerId,
        'staff_id' => $staffId,
        'scheduled_at' => $booking1At,
        'price' => 45.00,
        'status' => 'confirmed',
        'notes' => 'Please avoid the flower bed near the driveway.'
    ]);

    createBookingIfNotExists($mysqli, [
        'service_id' => $service2,
        'customer_id' => $customerId,
        'staff_id' => null,
        'scheduled_at' => $booking2At,
        'price' => 80.00,
        'status' => 'pending',
        'notes' => null
    ]);

    $mysqli->commit();
    echo "\nPopulation complete.\n";
} catch (Throwable $e) {
    $mysqli->rollback();
    fwrite(STDERR, "Transaction failed: " . $e->getMessage() . "\n");
    exit(1);
}

$mysqli->close();
return 0;
