<?php
defined('HHGB_ADMIN_VIEW') || exit;

$fields = [
    'name' => ['name', 'text', 50],
    'email' => ['email', 'email', 254],
    'homepage' => ['homepage', 'url', 2048],
    'betreff' => ['subject', 'text', 100],
    'bild_url' => ['image', 'url', 2048],
];
$values = $error === '' ? $entry : $_POST;
?>
<form action="?page=edit&amp;id=<?= (int) $entry['id'] ?>" method="post">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <?php foreach ($fields as $key => [$label, $type, $max]): ?>
        <div class="form-group">
            <label for="<?= e($key) ?>"><?= e($l[$label]) ?></label>
            <input class="form-control" id="<?= e($key) ?>" name="<?= e($key) ?>" type="<?= e($type) ?>"
                   maxlength="<?= $max ?>" value="<?= e(field($values, $key)) ?>" <?= $key === 'name' ? 'required' : '' ?>>
        </div>
    <?php endforeach; ?>
    <div class="form-group">
        <label for="nachricht"><?= e($l['msg']) ?></label>
        <textarea class="form-control" id="nachricht" name="nachricht" rows="5" required><?= e(field($values, 'nachricht')) ?></textarea>
    </div>
    <div class="form-group">
        <label for="kommentar"><?= e($l['comment']) ?></label>
        <textarea class="form-control" id="kommentar" name="kommentar" rows="3"><?= e(field($values, 'kommentar')) ?></textarea>
    </div>
    <div class="checkbox">
        <label><input type="checkbox" name="public" value="1" <?= in_array($values['public'] ?? null, [1, '1'], true) ? 'checked' : '' ?>>
            <?= e($l['publish']) ?></label>
    </div>
    <button class="btn btn-primary" name="action" value="save"><?= e($l['save']) ?></button>
    <button class="btn btn-danger" name="action" value="delete" formnovalidate><?= e($l['delete']) ?></button>
</form>
