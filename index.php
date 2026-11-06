<?php

require_once __DIR__ . '/hhgb/gb.php';

handle_errors();
start_session();
if (!configured()) {
    fail(503, 'The guestbook is not configured. Run php hhgb/install.php install on the server.');
}

$gb = new GB();
$gb->handleSubmission(basename(__FILE__));
?>
<!doctype html>
<html lang="<?= e($gb->getLanguageCode()) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($gb->getGbSettings()['gb_title'] ?? 'Guestbook') ?></title>
    <?php $gb->includeMeta(); ?>
</head>
<body>
<main class="hhgb">
    <h1><?= e($gb->getGbSettings()['gb_title'] ?? 'Guestbook') ?></h1>
    <?php $gb->showGBookForm(basename(__FILE__)); ?>
    <?php $gb->showGBookPosts(); ?>
    <?php $gb->showGBpageNavi(); ?>
    <p><a href="hhgb/"><?= e($gb->getLanguage()['admin_area']) ?></a></p>
</main>
</body>
</html>
