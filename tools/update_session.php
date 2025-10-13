<?php
$db = getcwd() . '/database/database.sqlite';
if (!file_exists($db)) {
    echo "ERROR: database file not found: $db\n";
    exit(1);
}
try {
    $pdo = new PDO('sqlite:' . $db);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo "PDO error: " . $e->getMessage() . "\n";
    exit(1);
}

$id = 'fxujeRrT3iMcE1d9qAjL4B5WRNJanpgKoKzk1h7D';

function fetchRow($pdo, $id) {
    $stmt = $pdo->prepare('SELECT * FROM sessions WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

echo "BEFORE:\n";
$before = fetchRow($pdo, $id);
echo json_encode($before, JSON_PRETTY_PRINT) . "\n\n";

// Prepare update values from user's request
$payload = 'YTozOntzOjY6Il90b2tlbiI7czo0MDoib3NRUjZadFBJdktDTGRVdGdLajR1dUt3U21KUjB5SmZmZ2hicVJiZCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzY6Imh0dHA6Ly9sYW5kc2NhcGVfZ3Jvb21pbmcudGVzdC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=';
$last_activity = 1759756338;
$user_id = null;
$ip_address = '127.0.0.1';
$user_agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36 Edg/141.0.0.0';

$sql = 'UPDATE sessions SET payload = ?, last_activity = ?, user_id = ?, ip_address = ?, user_agent = ? WHERE id = ?';
$stmt = $pdo->prepare($sql);
$stmt->execute([$payload, $last_activity, $user_id, $ip_address, $user_agent, $id]);
$count = $stmt->rowCount();

echo "UPDATED rows: $count\n\n";

echo "AFTER:\n";
$after = fetchRow($pdo, $id);
echo json_encode($after, JSON_PRETTY_PRINT) . "\n";

return 0;
