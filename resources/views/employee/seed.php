<?php
/**
 * database/seed.php
 *
 * Run once after update-schema.sql to get working test logins for every
 * account_status (customer / employee / admin):
 *
 *   php database/seed.php
 *
 * (Your products were already inserted by your own SQL script — this
 * only creates accounts, it doesn't touch the products table.)
 */

require __DIR__ . '/../config/db.php';

$accounts = [
    ['name' => 'Warehouse Staff', 'email' => 'employee@davaobosscomputer.com', 'password' => 'employee123', 'account_status' => 'employee'],
    ['name' => 'Site Admin',      'email' => 'admin@davaobosscomputer.com',    'password' => 'admin123',    'account_status' => 'admin'],
    ['name' => 'Test Customer',   'email' => 'customer@davaobosscomputer.com', 'password' => 'customer123', 'account_status' => 'customer'],
];

$stmt = $pdo->prepare(
    'INSERT INTO users (name, email, password_hash, account_status)
     VALUES (?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), account_status = VALUES(account_status)'
);

foreach ($accounts as $account) {
    $stmt->execute([
        $account['name'],
        $account['email'],
        password_hash($account['password'], PASSWORD_DEFAULT),
        $account['account_status'],
    ]);
}

echo "Seed complete. Test logins (change passwords after logging in):\n";
foreach ($accounts as $account) {
    echo "  [{$account['account_status']}] {$account['email']} / {$account['password']}\n";
}
