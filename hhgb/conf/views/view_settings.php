<?php defined('HHGB_ADMIN_VIEW') || exit; ?>
<form action="?page=settings" method="post">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <div class="form-group">
        <label for="gb_title"><?= e($l['gb_title']) ?></label>
        <input class="form-control" id="gb_title" name="gb_title" maxlength="100" value="<?= e($gbs['gb_title'] ?? '') ?>" required>
    </div>
    <div class="form-group">
        <label for="posts_on_page"><?= e($l['msgs_per_page']) ?></label>
        <input class="form-control" id="posts_on_page" name="posts_on_page" type="number" min="1" max="100"
               value="<?= e($gbs['posts_on_page'] ?? 5) ?>" required>
    </div>
    <div class="form-group">
        <label for="gb_language"><?= e($l['language']) ?></label>
        <select class="form-control" id="gb_language" name="gb_language">
            <?php foreach (LANGUAGES as $option): ?>
                <option value="<?= e($option) ?>" <?= ($gbs['gb_language'] ?? 'en') === $option ? 'selected' : '' ?>><?= e($option) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php foreach (admin_flags() as $key => $label): ?>
        <div class="checkbox">
            <label><input type="checkbox" name="<?= e($key) ?>" value="1" <?= (int) ($gbs[$key] ?? 0) === 1 ? 'checked' : '' ?>>
                <?= e($l[$label]) ?></label>
        </div>
    <?php endforeach; ?>
    <button class="btn btn-primary"><?= e($l['save']) ?></button>
</form>
