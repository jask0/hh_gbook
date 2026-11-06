<?php
if (!isset($data, $l, $gbs, $path, $admin)) {
    http_response_code(404);
    return;
}
$public = (int) $data['public'] === 1;
if (!$public && !$admin) {
    return;
}
$homepage = safe_url(field($data, 'homepage'));
$image = safe_url(field($data, 'bild_url'));
$email = field($data, 'email');
$scale = field($gbs, 'scale_image', '1') === '1';
$smileyBase = $path . 'hhgb/smilies/';
?>
<article class="gb-post <?= $public ? 'custom-panel' : 'gb-pending' ?>">
    <header class="gb-post-header <?= $public ? 'custom-heading' : '' ?>">
        <strong><?= e($data['name']) ?></strong>
        <time><?= e($data['datum']) ?></time>
        <?php if (!$public): ?><span><?= e($l['unpublished_msg']) ?></span><?php endif; ?>
        <?php if ($admin): ?><a href="<?= e($path . 'hhgb/index.php?page=edit&id=' . (int) $data['id']) ?>"><?= e($l['edit']) ?></a><?php endif; ?>
        <?php if (field($gbs, 'view_hp_input', '1') === '1' && $homepage !== ''): ?>
            <a class="<?= e(field($gbs, 'link_btn_color')) ?>" href="<?= e($homepage) ?>" rel="nofollow ugc"><?= e($l['homepage']) ?></a>
        <?php endif; ?>
        <?php if (field($gbs, 'view_email_input', '1') === '1' && filter_var($email, FILTER_VALIDATE_EMAIL)): ?>
            <a class="<?= e(field($gbs, 'link_btn_color')) ?>" href="<?= e('mailto:' . $email) ?>"><?= e($l['email']) ?></a>
        <?php endif; ?>
    </header>
    <div class="gb-post-body <?= $public ? 'custom-body' : '' ?>">
        <?php if (field($gbs, 'view_img_url_input', '1') === '1' && $image !== ''): ?>
            <a href="<?= e($image) ?>" rel="nofollow ugc"><img src="<?= e($image) ?>" alt="<?= e($l['image']) ?>"
                loading="lazy" class="custom-thumbnail <?= $scale ? 'gb-image' : '' ?>"></a>
        <?php endif; ?>
        <?php if (field($data, 'betreff') !== ''): ?><h2><?= e($data['betreff']) ?></h2><?php endif; ?>
        <div><?= bbCodeToHtml(field($data, 'nachricht'), $smilies, $smileyBase, $scale) ?></div>
    </div>
    <?php if (field($data, 'kommentar') !== ''): ?>
        <footer class="gb-post-footer <?= $public ? 'custom-footer' : '' ?>"><?= bbCodeToHtml($data['kommentar'], $smilies, $smileyBase, $scale) ?></footer>
    <?php endif; ?>
</article>
