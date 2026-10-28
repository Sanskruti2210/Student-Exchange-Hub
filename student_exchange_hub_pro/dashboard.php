<?php
require_once 'db.php';
require_login();
$uid=current_user_id();
function scalar_count($conn,$sql,$uid=null) {

    $stmt=mysqli_prepare($conn,$sql);
    if($uid!==null)mysqli_stmt_bind_param($stmt,'i',$uid);
    mysqli_stmt_execute($stmt);
    $row=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
    mysqli_stmt_close($stmt);
    return (int)($row['total']??0);

}

$my=scalar_count($conn,'SELECT COUNT(*) total FROM products WHERE seller_id=?',$uid);
$views=scalar_count($conn,'SELECT COALESCE(SUM(views),0) total FROM products WHERE seller_id=?',$uid);
$active=scalar_count($conn,"SELECT COUNT(*) total FROM products WHERE status='Available'");
$stmt=mysqli_prepare($conn,'SELECT id,title,category,status,views,created_at FROM products WHERE seller_id=? ORDER BY created_at DESC LIMIT 5');
mysqli_stmt_bind_param($stmt,'i',$uid);
mysqli_stmt_execute($stmt);
$recent=mysqli_stmt_get_result($stmt);
?>
<!doctype html>
<html lang="en" data-theme="<?= h(current_theme()) ?>" data-bs-theme="<?= current_theme() === 'dark' ? 'dark' : 'light' ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Dashboard | Student Exchange Hub</title><?php include 'head_assets.php'; ?>
</head>
<body><?php include 'header.php'; ?>
<main class="page-width content-page">
<div class="dashboard-welcome">
<div><span class="eyebrow">YOUR STUDENT HUB</span>
<h1>Welcome back, <?= h($_SESSION['user_name'] ?? 'Student') ?>.</h1>
<p class="muted">Here is a snapshot of your marketplace activity.</p>
</div><a class="button" href="add_product.php">＋ Create listing</a>
</div><?php show_flash(); ?>
<div class="stats-grid">
<div class="stat-card"><span>My listings</span><strong><?= $my ?></strong><small>All your listings</small>
</div>
<div class="stat-card"><span>Total views</span><strong><?= number_format($views) ?></strong><small>Views across your items</small>
</div>
<div class="stat-card"><span>Available items</span><strong><?= number_format($active) ?></strong><small>Across the marketplace</small>
</div>
<div class="stat-card"><span>Messages</span><strong>↗</strong><small><a href="messages.php">Open your inbox</a></small>
</div>
</div>
<div class="dashboard-columns">
<section class="dashboard-card">
<div class="section-heading">
<div><span class="eyebrow">YOUR ITEMS</span>
<h2>Recent listings</h2>
</div><a class="text-link" href="my_products.php">Manage all →</a>
</div>
<div class="recent-list"><?php if(mysqli_num_rows($recent)):while($r=mysqli_fetch_assoc($recent)): ?><a href="edit_product.php?id=<?= (int)$r['id'] ?>" class="recent-item"><span class="recent-symbol"><?= h(strtoupper(substr($r['category'],0,1))) ?></span><span class="recent-name"><strong><?= h($r['title']) ?></strong><small><?= h($r['category']) ?> · <?= (int)$r['views'] ?> views</small></span><span class="status status-<?= strtolower($r['status']) ?>"><?= h($r['status']) ?></span></a><?php endwhile;
else: ?>
<div class="empty-inline">
<p>No listings yet.</p><a href="add_product.php">List your first item →</a>
</div><?php endif; ?>
</div>
</section>

<section class="dashboard-card quick-actions"><span class="eyebrow">QUICK ACTIONS</span>
<h2>Make the most of the Hub.</h2><a href="add_product.php"><span>＋</span>
<div><strong>List an item</strong><small>Give a useful item a new home.</small>
</div><b>↗</b></a><a href="browse.php"><span>⌕</span>
<div><strong>Explore listings</strong><small>Find what other students are sharing.</small>
</div><b>↗</b></a><a href="profile.php"><span>◎</span>
<div><strong>Update profile</strong><small>Keep your student details current.</small>
</div><b>↗</b></a>
</section>
</div>
</main><?php include 'footer.php'; ?>
</body>
</html>
