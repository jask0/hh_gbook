<?php
defined('HHGB_ADMIN_VIEW') || exit;

$limit = 20;
$total = $page === 'offline' ? $unpublished : $db->countPosts();
$lastPage = max(1, (int) ceil($total / $limit));
$listPage = min(positive_int(field($_GET, 'p'), 1), $lastPage);
$entries = $db->readPostsForPage($limit, ($listPage - 1) * $limit, true, $page === 'offline' ? 0 : null);
?>
<p><?= e($l['post_count']) ?>: <?= e($total) ?></p>
<div class="table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th scope="col"><?= e($l['name']) ?></th>
                <th scope="col"><?= e($l['subject']) ?></th>
                <th scope="col"><?= e($l['publish']) ?></th>
                <th scope="col"><?= e($l['edit']) ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($entries as $item): ?>
                <tr>
                    <td><?= e($item['name']) ?></td>
                    <td><?= e($item['betreff']) ?></td>
                    <td><?= e($l[(int) $item['public'] === 1 ? 'yes' : 'no']) ?></td>
                    <td><a href="?page=edit&amp;id=<?= (int) $item['id'] ?>"><?= e($l['edit']) ?></a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php if ($lastPage > 1): ?>
    <nav aria-label="<?= e($l['msgs_per_page']) ?>">
        <?php if ($listPage > 1): ?>
            <a href="?page=<?= e($page) ?>&amp;p=<?= $listPage - 1 ?>" rel="prev">&larr; <?= $listPage - 1 ?></a>
        <?php endif; ?>
        <span aria-current="page"><?= $listPage ?></span>
        <?php if ($listPage < $lastPage): ?>
            <a href="?page=<?= e($page) ?>&amp;p=<?= $listPage + 1 ?>" rel="next"><?= $listPage + 1 ?> &rarr;</a>
        <?php endif; ?>
    </nav>
<?php endif; ?>
