<?php
require_once dirname(__DIR__, 2) . '/config/auth.php';
require_once dirname(__DIR__, 2) . '/config/database.php';

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (attemptLogin($pdo, $email, $password)) {
        header('Location: ' . (isEmployee() ? 'employee/dashboard.php' : 'index.php'));
        exit;
    }

    $error = 'Incorrect email or password.';
}

$pageTitle = 'Sign In — Davao Boss Computer';
$activeNav = '';
include __DIR__ . '/header.php';
?>

<section class="max-w-sm mx-auto px-4 py-16">
    <h1 class="text-2xl font-bold mb-6">Sign In</h1>

    <?php if ($error): ?>
        <div class="bg-red-50 border border-red-300 text-red-800 text-sm rounded p-3 mb-4">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <form method="post" class="space-y-4">
        <div>
            <label for="email" class="block text-xs font-semibold uppercase tracking-wide text-gray-600 mb-1">Email</label>
            <input type="email" id="email" name="email" required
                   value="<?= htmlspecialchars($_POST['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-purple-600">
        </div>
        <div>
            <label for="password" class="block text-xs font-semibold uppercase tracking-wide text-gray-600 mb-1">Password</label>
            <input type="password" id="password" name="password" required
                   class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-purple-600">
        </div>
        <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold text-sm tracking-wide px-6 py-3 rounded">
            Sign In
        </button>
    </form>

    <p class="text-xs text-gray-500 mt-4">
        Employee accounts land in the Warehouse dashboard automatically after signing in.
    </p>
</section>

<?php include __DIR__ . '/footer.php'; ?>
