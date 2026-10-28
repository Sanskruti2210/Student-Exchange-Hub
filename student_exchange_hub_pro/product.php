<?php
require_once 'db.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) {

    http_response_code(404); $product = null;
}

else {

    $stmt = mysqli_prepare($conn, "SELECT p.*,u.name AS seller_name,u.email AS seller_email,u.phone AS seller_phone,u.course AS seller_course,u.college AS seller_college FROM products p JOIN users u ON u.id=p.seller_id WHERE p.id=? AND p.status IN ('Available','Sold') LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'i', $id); mysqli_stmt_execute($stmt); $product = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt)); mysqli_stmt_close($stmt);
    if ($product && $product['status'] === 'Available' && (!isset($_SESSION['viewed_products'][$id]) || $_SESSION['viewed_products'][$id] !== true)) {

        $update = mysqli_prepare($conn, 'UPDATE products SET views=views+1 WHERE id=?');
        mysqli_stmt_bind_param($update, 'i', $id); mysqli_stmt_execute($update); mysqli_stmt_close($update);
        $_SESSION['viewed_products'][$id] = true; $product['views'] = (int)$product['views'] + 1;

    }

}

if (!$product) {

    http_response_code(404);
}
else {

    remember_recent_product((int)$product['id'], $product['title'], $product['category']);
}

?>
<!doctype html>
<html lang="en" data-theme="<?= h(current_theme()) ?>" data-bs-theme="<?= current_theme() === 'dark' ? 'dark' : 'light' ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= $product ? h($product['title']) . ' | Student Exchange Hub' : 'Listing not found' ?></title><?php include 'head_assets.php'; ?>
</head>
<body><?php include 'header.php'; ?>
<main class="page-width content-page">
<?php show_flash(); ?>
<?php if(!$product): ?>
<div class="empty-state">
<h1>Listing not found</h1>
<p>This listing may have been removed or is no longer available.</p><a class="button" href="browse.php">Back to marketplace</a>
</div>
<?php else: ?>
<div class="breadcrumbs"><a href="browse.php">Marketplace</a><span>/</span><a href="browse.php?category=<?= urlencode($product['category']) ?>"><?= h($product['category']) ?></a><span>/</span><span><?= h($product['title']) ?></span>
</div>
<div class="detail-layout">
<section class="detail-main">
<div class="detail-art visual-<?= strtolower(substr($product['category'],0,1)) ?>"><span><?= h(strtoupper(substr($product['category'],0,1))) ?></span><small><?= h($product['category']) ?></small>
</div>
<div class="detail-heading">
<div><span class="pill"><?= h($product['item_type']) ?></span>
<h1><?= h($product['title']) ?>
</h1>
<p class="muted">Listed <?= h(date('M j, Y', strtotime($product['created_at']))) ?> · <?= (int)$product['views'] ?> views</p>
</div><strong class="detail-price"><?= h(money_label($product['price'],$product['item_type'])) ?></strong>
</div>
<div class="detail-section">
<h2>About this item</h2>
<p class="description-text"><?= nl2br(h($product['description'])) ?>
</p>
</div>
<div class="spec-grid">
<div><small>Condition</small><strong><?= h($product['item_condition']) ?></strong>
</div>
<div><small>Category</small><strong><?= h($product['category']) ?></strong>
</div>
<div><small>Listing type</small><strong><?= h($product['item_type']) ?></strong>
</div>
<div><small>Meetup area</small><strong><?= h($product['location']) ?></strong>
</div>
</div>
</section>
<aside class="seller-panel"><span class="eyebrow">LISTED BY</span>
<div class="seller-avatar"><?= h(strtoupper(substr($product['seller_name'],0,1))) ?>
</div>
<h2><?= h($product['seller_name']) ?>
</h2>
<p><?= h($product['seller_course'] ?: 'Student community member') ?>
</p><?php if($product['seller_college']): ?>
<p><?= h($product['seller_college']) ?>
</p><?php endif; ?>
<div class="seller-divider">
</div><?php if($product['status']!=='Available'): ?>
<div class="notice">This item has been marked sold.</div><?php elseif(is_logged_in() && current_user_id()===(int)$product['seller_id']): ?><a class="button button-wide" href="edit_product.php?id=<?= (int)$product['id'] ?>">Edit your listing</a><a class="button button-outline button-wide" href="my_products.php">Manage listings</a><?php elseif(is_logged_in()): ?><a class="button button-wide" href="messages.php?to=<?= (int)$product['seller_id'] ?>&product_id=<?= (int)$product['id'] ?>">Message seller</a>
<p class="tiny-note">Keep personal information private until you feel comfortable.</p><?php else: ?><a class="button button-wide" href="login.php">Sign in to contact seller</a>
<p class="tiny-note">You need an account to message the seller.</p><?php endif; ?>
<div class="safety-tip"><strong>Meet safely</strong>
<p>Choose a busy public campus spot, inspect the item before paying, and tell a friend about the meetup.</p>
</div>
</aside>
</div>
<?php endif; ?>
</main><?php include 'footer.php'; ?>
</body>
</html>
