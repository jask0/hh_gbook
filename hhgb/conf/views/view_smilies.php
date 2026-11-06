<?php
defined('HHGB_ADMIN_VIEW') || exit;

$smilies = json_decode($gbs['smilies'] ?? '', true);
$sets = admin_smilie_sets();
$selected = $smilies['folder'] ?? '';
if (!is_string($selected) || !array_key_exists($selected, $sets)) {
    $selected = array_key_first($sets);
}
?>
<form action="?page=smilies" method="post">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <div class="checkbox">
        <label><input type="checkbox" name="smilies_on" value="1" <?= (int) ($gbs['smilies_on'] ?? 0) === 1 ? 'checked' : '' ?>>
            <?= e($l['smilies']) ?></label>
    </div>
    <div class="form-group">
        <label for="folder"><?= e($l['smilie_set']) ?></label>
        <select class="form-control" id="folder" name="folder" required>
            <?php foreach (array_keys($sets) as $option): ?>
                <option value="<?= e($option) ?>" <?= $selected === $option ? 'selected' : '' ?>><?= e($option) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php if ($selected !== null): ?>
        <div class="form-group">
            <?php foreach ($sets[$selected] as $file): ?>
                <img src="smilies/<?= e(rawurlencode((string) $selected)) ?>/<?= e(rawurlencode($file)) ?>" alt="<?= e($file) ?>">
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <button class="btn btn-primary"><?= e($l['save']) ?></button>
</form>
