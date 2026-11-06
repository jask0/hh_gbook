<?php defined('HHGB_ADMIN_VIEW') || exit; ?>
<form action="?page=profile" method="post">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <div class="form-group">
        <label for="username"><?= e($l['username']) ?></label>
        <input class="form-control" id="username" name="username" maxlength="64"
               value="<?= e($user['user'] ?? '') ?>" autocomplete="username" required>
    </div>
    <div class="form-group">
        <label for="email"><?= e($l['email']) ?></label>
        <input class="form-control" id="email" type="email" name="email" value="<?= e($user['email'] ?? '') ?>" autocomplete="email">
    </div>
    <div class="checkbox">
        <label><input type="checkbox" name="mail_msg" value="1" <?= (int) ($user['mail_msg'] ?? 0) === 1 ? 'checked' : '' ?>>
            <?= e($l['at_new_msg_send_email_to_me']) ?></label>
    </div>
    <div class="form-group">
        <label for="user_language"><?= e($l['language']) ?></label>
        <select class="form-control" id="user_language" name="user_language">
            <?php foreach (LANGUAGES as $option): ?>
                <option value="<?= e($option) ?>" <?= $language === $option ? 'selected' : '' ?>><?= e($option) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label for="current_password"><?= e($l['current_password'] ?? 'Current password') ?></label>
        <input class="form-control" id="current_password" type="password" name="current_password" autocomplete="current-password">
    </div>
    <div class="form-group">
        <label for="password"><?= e($l['new_password'] ?? 'New password') ?></label>
        <input class="form-control" id="password" type="password" name="password" autocomplete="new-password">
    </div>
    <div class="form-group">
        <label for="password_confirmation"><?= e($l['confirm_password'] ?? 'Confirm new password') ?></label>
        <input class="form-control" id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password">
    </div>
    <button class="btn btn-primary"><?= e($l['save']) ?></button>
</form>
