<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/legacy_database.php';

function currentUser(): ?array
{
    if (isset($_SESSION['user']) && is_array($_SESSION['user'])) {
        return $_SESSION['user'];
    }

    if (function_exists('app')) {
        try {
            $container = app();
            if ($container->bound('auth')) {
                $auth = $container->make('auth');
                if ($auth->check()) {
                    $user = $auth->user();

                    return [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email,
                        'account_status' => $user->isAdmin() ? 'admin' : 'user',
                    ];
                }
            }
        } catch (Throwable $e) {
            // The legacy storefront may be included outside the full Laravel app.
        }
    }

    return null;
}

function isLoggedIn(): bool
{
    return currentUser() !== null;
}

function isEmployee(): bool
{
    $user = currentUser();
    return $user !== null && in_array($user['account_status'], ['employee', 'admin'], true);
}

function requireEmployee(): void
{
    if (!isEmployee()) {
        $target = isLoggedIn() ? '../index.php' : '../login.php';
        header('Location: ' . $target);
        exit;
    }
}

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

function lowStockProducts(PDO $pdo): array
{
    return $pdo
        ->query('SELECT * FROM products WHERE stock_quantity <= low_stock_threshold ORDER BY stock_quantity ASC')
        ->fetchAll();
}
