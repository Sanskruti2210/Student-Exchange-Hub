<?php
require_once 'db.php';
$cats=array(array('Books','B','Textbooks, reference books and notes.','linear'),array('Electronics','E','Calculators, accessories and useful gadgets.','blue'),array('Furniture','F','Make your room or study corner work better.','orange'),array('Accessories','A','Bags, stationery and everyday essentials.','pink'),array('Other','+','Unexpected finds that are still useful.','green'));
$stmt=mysqli_prepare($conn,"SELECT category,COUNT(*) AS total FROM products WHERE status='Available' GROUP BY category");
mysqli_stmt_execute($stmt);
$result=mysqli_stmt_get_result($stmt);
$counts=array();
while($r=mysqli_fetch_assoc($result))$counts[$r['category']]=(int)$r['total'];
?>
<!doctype html>
<html lang="en" data-theme="<?= h(current_theme()) ?>" data-bs-theme="<?= current_theme() === 'dark' ? 'dark' : 'light' ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Categories | Student Exchange Hub</title><?php include 'head_assets.php'; ?>
</head>
<body><?php include 'header.php'; ?>
<main class="page-width content-page">
<div class="page-title-row">
<div><span class="eyebrow">BROWSE YOUR WAY</span>
<h1>Something for every semester.</h1>
<p class="muted">Pick a category and find student-friendly essentials.</p>
</div>
</div>
<div class="category-page-grid"><?php foreach($cats as $c): ?><a class="category-large category-<?= h($c[4]) ?>" href="browse.php?category=<?= urlencode($c[0]) ?>"><span class="category-symbol"><?= h($c[1]) ?></span><span class="category-count"><?= (int)($counts[$c[0]]??0) ?> active listings</span>
<h2><?= h($c[0]) ?>
</h2>
<p><?= h($c[2]) ?>
</p><strong>Explore category <span>↗</span></strong></a><?php endforeach; ?>
</div>
</main><?php include 'footer.php'; ?>
</body>
</html>
