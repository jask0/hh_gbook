<?php
declare(strict_types=1);

require __DIR__ . '/gb.php';
handle_errors();
start_session();

$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
    require_csrf();
    if (field($_POST, 'action') === 'logout') {
        $_SESSION = [];
        $cookie = session_get_cookie_params();
        unset($cookie['lifetime']);
        setcookie(session_name(), '', ['expires' => time() - 3600] + $cookie);
        session_destroy();
        redirect('login.php');
    }
} elseif ($method !== 'GET' && $method !== 'HEAD') {
    header('Allow: GET, HEAD, POST');
    fail(405, 'Method not allowed.');
}

if (!configured()) {
    fail(503, 'The guestbook is not set up. Follow the installation instructions in README.md.');
}

$gb = new GB();
$user = $gb->getUserSettings();
$language = $user['user_language'] ?? 'en';
$l = $gb->getLanguage($language);
if ($gb->isLoggedIn()) {
    redirect('index.php');
}

$error = false;
if ($method === 'POST') {
    $username = trim(field($_POST, 'username'));
    $password = field($_POST, 'password');
    $hash = $user['password'] ?? '';
    $validPassword = strlen($password) <= 72 && !str_contains($password, "\0")
        && password_verify($password, $hash);
    if ($validPassword && $username === ($user['user'] ?? '')) {
        if (!session_regenerate_id(true)) {
            fail(500, 'Could not start an admin session.');
        }
        $_SESSION = ['admin' => true, 'admin_credential' => admin_credential($user)];
        redirect('index.php');
    }
    http_response_code(401);
    $error = true;
}
?>
<!doctype html>
<html lang="<?= e($language) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($l['login']) ?> | <?= e($l['gb']) ?></title>
    <link rel="stylesheet" href="conf/css/bootstrap.min.css">
</head>
<body>
<main class="container">
    <div class="row">
        <div class="col-sm-6 col-sm-offset-3">
            <h1><?= e($l['login_form']) ?></h1>
            <?php if ($error): ?>
                <p class="alert alert-danger"><?= e($l['login_failed']) ?></p>
            <?php endif; ?>
            <form action="login.php" method="post">
                <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                <div class="form-group">
                    <label for="username"><?= e($l['username']) ?></label>
                    <input class="form-control" id="username" name="username" autocomplete="username" required>
                </div>
                <div class="form-group">
                    <label for="password"><?= e($l['password']) ?></label>
                    <input class="form-control" id="password" type="password" name="password" autocomplete="current-password" required>
                </div>
                <button class="btn btn-primary"><?= e($l['login']) ?></button>
                <a class="btn btn-default" href="../"><?= e($l['to_gb']) ?></a>
            </form>
        </div>
    </div>
</main>
</body>
</html>
