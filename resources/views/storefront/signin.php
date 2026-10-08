<?php
$pageTitle = 'Sign In — Davao Boss Computer';
$activeNav = 'signin';

$errors = [];
$email  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Enter a valid email address.';
    }
    if (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters.';
    }

    if (empty($errors)) {
        // TODO: verify credentials against your users table and start a session.
        header('Location: /index.php');
        exit;
    }
}

include __DIR__ . '/header.php';
?>

<section class="bg-white">
    <div class="max-w-md mx-auto px-4 py-16">
        <h1 class="text-2xl font-extrabold mb-2 text-center">Sign In</h1>
        <p class="text-sm text-gray-500 text-center mb-8">Track orders and save your builds.</p>

        <form method="post" class="space-y-5" novalidate>
            <div>
                <label for="email" class="block text-xs font-semibold uppercase tracking-wide text-gray-600 mb-1">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required
                       class="w-full border <?= isset($errors['email']) ? 'border-red-500' : 'border-gray-300' ?> focus:border-purple-500 focus:ring-1 focus:ring-purple-500 rounded px-3 py-2 text-sm outline-none">
                <?php if (isset($errors['email'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['email']) ?></p>
                <?php endif; ?>
            </div>
            <div>
                <label for="password" class="block text-xs font-semibold uppercase tracking-wide text-gray-600 mb-1">Password</label>
                <input type="password" id="password" name="password" required minlength="8"
                       class="w-full border <?= isset($errors['password']) ? 'border-red-500' : 'border-gray-300' ?> focus:border-purple-500 focus:ring-1 focus:ring-purple-500 rounded px-3 py-2 text-sm outline-none">
                <?php if (isset($errors['password'])): ?>
                    <p class="text-xs text-red-600 mt-1"><?= htmlspecialchars($errors['password']) ?></p>
                <?php endif; ?>
            </div>
            <div class="flex items-center justify-between text-xs">
                <label class="flex items-center gap-2 text-gray-600">
                    <input type="checkbox" name="remember"> Remember me
                </label>
                <a href="#" class="text-purple-600 hover:text-purple-700">Forgot password?</a>
            </div>
            <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold text-sm px-6 py-3">
                SIGN IN
            </button>
        </form>

        <p class="text-center text-xs text-gray-500 mt-6">
            New here? <a href="#" class="text-purple-600 hover:text-purple-700 font-semibold">Create an account</a>
        </p>
    </div>
</section>

<?php include __DIR__ . '/footer.php'; ?>
