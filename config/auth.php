<?php
/**
 * config/auth.php
 *
 * Session + account helpers, shared by every page. Include this before
 * you need to know who's logged in, or before checking employee access:
 *
 *   require __DIR__ . '/config/auth.php';
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/database.php'; // gives us $pdo

/** The logged-in user's session data, or null if nobody's logged in. */
function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool
{
    return currentUser() !== null;
}

/** True for account_status 'employee' or 'admin'. */
function isEmployee(): bool
{
    $user = currentUser();
    return $user !== null && in_array($user['account_status'], ['employee', 'admin'], true);
}

/** Call at the top of any page that only employees/admins should reach. */
function requireEmployee(): void
{
    if (!isEmployee()) {
        $target = isLoggedIn() ? '../index.php' : '../login.php';
        header('Location: ' . $target);
        exit;
    }
}

/** Attempts login; on success stores the user (minus password) in the session. */
function attemptLogin(PDO $pdo, string $email, string $password): bool
{
    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        unset($user['password_hash']);
        $_SESSION['user'] = $user;
        return true;
    }

    return false;
}

function logoutUser(): void
{
    $_SESSION = [];
    session_destroy();
}

/** Products at or below their low_stock_threshold, lowest stock first. */
function lowStockProducts(PDO $pdo): array
{
    return $pdo
        ->query('SELECT * FROM products WHERE stock_quantity <= low_stock_threshold ORDER BY stock_quantity ASC')
        ->fetchAll();
}