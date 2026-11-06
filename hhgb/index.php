<?php
declare(strict_types=1);

require __DIR__ . '/gb.php';
handle_errors();
start_session();

if (($_SESSION['admin'] ?? false) !== true) {
    redirect('login.php');
}
if (!configured()) {
    fail(503, 'The guestbook is not set up. Follow the installation instructions in README.md.');
}
$gb = new GB();
if (!$gb->isLoggedIn()) {
    unset($_SESSION['admin'], $_SESSION['admin_credential']);
    redirect('login.php');
}

require __DIR__ . '/conf/admin.php';

$pages = [
    'index' => [__DIR__ . '/conf/views/view_index.php', 'home'],
    'offline' => [__DIR__ . '/conf/views/view_index.php', 'unpublished_msg'],
    'edit' => [__DIR__ . '/conf/views/view_edit.php', 'edit'],
    'settings' => [__DIR__ . '/conf/views/view_settings.php', 'settings'],
    'profile' => [__DIR__ . '/conf/views/view_profile.php', 'profile'],
    'custom' => [__DIR__ . '/conf/views/view_custom.php', 'custom_css'],
    'smilies' => [__DIR__ . '/conf/views/view_smilies.php', 'smilies'],
];
$page = array_key_exists('page', $_GET) ? field($_GET, 'page') : 'index';
if (!isset($pages[$page])) {
    fail(404, 'Page not found.');
}

$db = $gb->getDB();
$gbs = $gb->getGbSettings();
$user = $gb->getUserSettings();
$language = $user['user_language'] ?? 'en';
$l = $gb->getLanguage($language);
$csrf = e(csrf_token());

$entry = null;
if ($page === 'edit') {
    $id = filter_var(field($_GET, 'id'), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $entry = $id === false ? null : $db->readPost($id);
    if ($entry === null) {
        fail(404, 'Message not found.');
    }
}

$notice = field($_SESSION, 'admin_notice');
unset($_SESSION['admin_notice']);
$error = '';
$method = $_SERVER['REQUEST_METHOD'];
if ($method === 'POST') {
    require_csrf();
    if ($page === 'index' || $page === 'offline') {
        header('Allow: GET, HEAD');
        fail(405, 'Method not allowed.');
    }
    try {
        $destination = save_admin_page($page, $db, $user, $entry);
        redirect($destination);
    } catch (InvalidArgumentException $exception) {
        http_response_code(422);
        $error = $exception->getMessage();
    }
} elseif ($method !== 'GET' && $method !== 'HEAD') {
    header('Allow: GET, HEAD, POST');
    fail(405, 'Method not allowed.');
}

$unpublished = $db->countPosts(0);
define('HHGB_ADMIN_VIEW', true);
?>
<!doctype html>
<html lang="<?= e($language) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($l[$pages[$page][1]]) ?> | <?= e($l['admin_area']) ?></title>
    <link rel="stylesheet" href="conf/css/bootstrap.min.css">
</head>
<body>
<main class="container">
    <h1><?= e($l['admin_area']) ?></h1>
    <nav aria-label="<?= e($l['admin_area']) ?>">
        <ul class="nav nav-pills">
            <li><a href="../"><?= e($l['to_gb']) ?></a></li>
            <?php foreach ($pages as $route => [, $label]): ?>
                <?php if ($route !== 'edit'): ?>
                    <li class="<?= $route === $page ? 'active' : '' ?>">
                        <a href="?page=<?= e($route) ?>">
                            <?= e($l[$label]) ?><?= $route === 'offline' ? ' (' . e($unpublished) . ')' : '' ?>
                        </a>
                    </li>
                <?php endif; ?>
            <?php endforeach; ?>
            <li>
                <form action="login.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                    <button class="btn btn-link" name="action" value="logout"><?= e($l['logout']) ?></button>
                </form>
            </li>
        </ul>
    </nav>
    <h2><?= e($l[$pages[$page][1]]) ?></h2>
    <?php if ($notice !== ''): ?>
        <p class="alert alert-success" role="status"><?= e($l[$notice]) ?></p>
    <?php endif; ?>
    <?php if ($error !== ''): ?>
        <p class="alert alert-danger" role="alert"><?= e($error) ?></p>
    <?php endif; ?>
    <?php require $pages[$page][0]; ?>
</main>
</body>
</html>
