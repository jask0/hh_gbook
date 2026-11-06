<?php
defined('HHGB_ADMIN_VIEW') || exit;

$colors = json_decode($gbs['custom_css'] ?? '', true);
?>
<form action="?page=custom" method="post">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
    <div class="checkbox">
        <label><input type="checkbox" name="use_custom_css" value="1" <?= (int) ($gbs['use_custom_css'] ?? 0) === 1 ? 'checked' : '' ?>>
            <?= e($l['use_custom_css']) ?></label>
    </div>
    <?php foreach (admin_colors() as $key => [$selector, $property, $label]): ?>
        <?php
        $color = $colors[$selector][$property] ?? '';
        if (!is_string($color) || !preg_match('/\A[0-9a-f]{6}\z/i', $color)) {
            $color = '000000';
        }
        ?>
        <div class="form-group">
            <label for="<?= e($key) ?>"><?= e($l[$label]) ?></label>
            <input id="<?= e($key) ?>" type="color" name="<?= e($key) ?>" value="#<?= e($color) ?>">
        </div>
    <?php endforeach; ?>
    <?php foreach (['send_btn_color' => 'btn_send_color', 'link_btn_color' => 'btn_mail_color'] as $key => $label): ?>
        <div class="form-group">
            <label for="<?= e($key) ?>"><?= e($l[$label]) ?></label>
            <select class="form-control" id="<?= e($key) ?>" name="<?= e($key) ?>">
                <?php foreach (admin_buttons() as $option): ?>
                    <option value="<?= e($option) ?>" <?= ($gbs[$key] ?? 'btn-default') === $option ? 'selected' : '' ?>>
                        <?= e($l[str_replace('-', '_', $option)]) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    <?php endforeach; ?>
    <button class="btn btn-primary"><?= e($l['save']) ?></button>
</form>
