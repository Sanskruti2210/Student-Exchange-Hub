<?php
require_once 'db.php';
require_login();
$uid=current_user_id();
$stmt=mysqli_prepare($conn,'SELECT id,title,category,price,item_condition,item_type,status,views,created_at FROM products WHERE seller_id=? ORDER BY created_at DESC');
mysqli_stmt_bind_param($stmt,'i',$uid);
mysqli_stmt_execute($stmt);
$result=mysqli_stmt_get_result($stmt);
?>
<!doctype html>
<html lang="en" data-theme="<?= h(current_theme()) ?>" data-bs-theme="<?= current_theme() === 'dark' ? 'dark' : 'light' ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>My listings | Student Exchange Hub</title><?php include 'head_assets.php'; ?>
</head>
<body><?php include 'header.php'; ?>
<main class="page-width content-page">
<div class="page-title-row">
<div><span class="eyebrow">YOUR MARKETPLACE</span>
<h1>My listings</h1>
<p class="muted">Keep track of what you have listed, sold or hidden.</p>
</div><a class="button" href="add_product.php">＋ List an item</a>
</div><?php show_flash(); ?>
<div class="table-wrap">
<table class="data-table">
<thead>
<tr>
<th>Item</th>
<th>Type</th>
<th>Price</th>
<th>Status</th>
<th>Views</th>
<th>Actions</th>
</tr>
</thead>
<tbody>
<?php if(mysqli_num_rows($result)):while($r=mysqli_fetch_assoc($result)): ?>
<tr>
<td><strong><?= h($r['title']) ?></strong><small><?= h($r['category']) ?> · <?= h($r['item_condition']) ?></small>
</td>
<td><?= h($r['item_type']) ?>
</td>
<td><?= h(money_label($r['price'],$r['item_type'])) ?>
</td>
<td><span class="status status-<?= strtolower($r['status']) ?>"><?= h($r['status']) ?></span>
</td>
<td><?= (int)$r['views'] ?>
</td>
<td>
<div class="table-actions"><a href="product.php?id=<?= (int)$r['id'] ?>">View</a><a href="edit_product.php?id=<?= (int)$r['id'] ?>">Edit</a>
<form method="post" action="delete_product.php" class="inline-form delete-form"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
<button class="link-danger" type="submit">Delete</button>
</form>
</div>
</td>
</tr><?php endwhile;
else: ?>
<tr>
<td colspan="6">
<div class="empty-inline"><strong>You have not listed anything yet.</strong>
<p>Your unused items could be useful to another student.</p><a href="add_product.php">Create your first listing →</a>
</div>
</td>
</tr><?php endif; ?>
</tbody>
</table>
</div>
</main><?php include 'footer.php'; ?>
</body>
</html>
