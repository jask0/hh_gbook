<?php
if (!isset($gbs, $l, $data, $action)) {
    http_response_code(404);
    return;
}
?>
<?php if (isset($flash['key'])): ?>
    <p class="gb-status" role="status"><?= e($l[$flash['key']]) ?></p>
<?php endif; ?>
<form class="gb-form" action="<?= e($action) ?>" method="post">
    <input type="hidden" name="hhgb_action" value="post">
    <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
    <label><?= e($l['name']) ?> *
        <input name="name" value="<?= e($data['name']) ?>" required maxlength="50" autocomplete="name">
    </label>
    <?php foreach ([
        'email' => ['email', 'email', 'email', 254],
        'homepage' => ['hp', 'homepage', 'url', 2048],
        'bild_url' => ['img_url', 'image', 'url', 2048],
        'betreff' => ['subject', 'subject', 'text', 100],
    ] as $key => [$setting, $label, $type, $limit]): ?>
        <?php if (field($gbs, 'view_' . $setting . '_input', '1') === '1'): ?>
            <label><?= e($l[$label]) ?>
                <input name="<?= e($key) ?>" type="<?= e($type) ?>" maxlength="<?= $limit ?>" value="<?= e($data[$key]) ?>">
            </label>
        <?php endif; ?>
    <?php endforeach; ?>
    <label><?= e($l['msg']) ?> *
        <textarea name="nachricht" rows="5" maxlength="10000" required><?= e($data['nachricht']) ?></textarea>
    </label>
    <?php if ($smilies): ?>
        <div class="gb-smilies" aria-label="<?= e($l['smilies']) ?>">
            <?php foreach ($smilies as $name => $file): ?>
                <button type="button" data-hhgb-smiley="<?= e('{{' . $name . '}}') ?>">
                    <img src="<?= e($path . 'hhgb/smilies/' . $file) ?>" alt="<?= e($name) ?>">
                </button>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php if ($captcha): ?>
        <label><?= e($l['captcha']) ?> *
            <img class="gb-captcha" src="<?= e($path . 'hhgb/captcha/captcha.php') ?>" alt="<?= e($l['captcha']) ?>" width="88" height="31">
            <input name="captcha" required minlength="6" maxlength="6" autocomplete="off" autocapitalize="characters">
        </label>
    <?php endif; ?>
    <button class="gb-button <?= e(field($gbs, 'send_btn_color')) ?>" type="submit"><?= e($l['send']) ?></button>
</form>
