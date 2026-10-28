<?php
require_once 'db.php';
$search = trim($_GET['search'] ?? '');
$category = trim($_GET['category'] ?? '');
$type = trim($_GET['type'] ?? '');
$sort = $_GET['sort'] ?? 'newest';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;
if (!valid_category($category)) $category = '';
if (!valid_item_type($type)) $type = '';
$sortSql = array('newest' => 'p.created_at DESC', 'price_low' => 'p.price ASC', 'price_high' => 'p.price DESC', 'popular' => 'p.views DESC');
if (!isset($sortSql[$sort])) $sort = 'newest';
$where = " WHERE p.status='Available' ";
$params = array(); $types = '';
if ($search !== '') {

    $where .= " AND (p.title LIKE ? OR p.description LIKE ? OR p.category LIKE ?)"; $like = '%' . $search . '%'; $params[]=$like; $params[]=$like; $params[]=$like; $types.='sss';
}

if ($category !== '') {

    $where .= ' AND p.category=?'; $params[]=$category; $types.='s';
}

if ($type !== '') {

    $where .= ' AND p.item_type=?'; $params[]=$type; $types.='s';
}

$countSql = 'SELECT COUNT(*) AS total FROM products p' . $where;
$stmt = mysqli_prepare($conn, $countSql);
if ($types !== '') mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt); $total = (int)mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['total']; mysqli_stmt_close($stmt);
$totalPages = max(1, (int)ceil($total / $perPage)); if ($page > $totalPages) $page = $totalPages;
$offset = ($page - 1) * $perPage;
$sql = 'SELECT p.id,p.title,p.category,p.price,p.item_type,p.item_condition,p.location,p.views,p.created_at,u.name AS seller_name FROM products p JOIN users u ON u.id=p.seller_id' . $where . ' ORDER BY ' . $sortSql[$sort] . ' LIMIT ? OFFSET ?';
$stmt = mysqli_prepare($conn, $sql);
$bindTypes = $types . 'ii'; $bindParams = $params; $bindParams[]=$perPage; $bindParams[]=$offset;
mysqli_stmt_bind_param($stmt, $bindTypes, ...$bindParams); mysqli_stmt_execute($stmt); $result = mysqli_stmt_get_result($stmt);
function browse_query($changes = array()) {

    $q = array_merge($_GET, $changes); unset($q['page']); return http_build_query($q);
}

?>
<!doctype html>
<html lang="en" data-theme="<?= h(current_theme()) ?>" data-bs-theme="<?= current_theme() === 'dark' ? 'dark' : 'light' ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Marketplace | Student Exchange Hub</title><?php include 'head_assets.php'; ?>
</head>
<body><?php include 'header.php'; ?>
<main class="page-width content-page">
<div class="page-title-row">
<div><span class="eyebrow">THE MARKETPLACE</span>
<h1>Find your next useful thing.</h1>
<p class="muted">Browse campus finds, compare prices and connect with student sellers.</p>
</div><?php if(is_logged_in()): ?><a class="button" href="add_product.php">＋ List an item</a><?php endif; ?>
</div>
<form method="get" class="filter-panel" id="filterForm">
<label class="search-field"><span>Search</span><input id="liveSearch" type="search" name="search" value="<?= h($search) ?>" placeholder="Try “calculator” or “notes”">
<div class="suggest-box" id="suggestBox">
</div>
</label>
<label><span>Category</span>
<select name="category">
<option value="">All categories</option><?php foreach(array('Books','Electronics','Furniture','Accessories','Other') as $c): ?>
<option value="<?= h($c) ?>" <?= $category===$c?'selected':'' ?>><?= h($c) ?>
</option><?php endforeach; ?>
</select>
</label>
<label><span>Listing type</span>
<select name="type">
<option value="">All types</option><?php foreach(array('Sell','Exchange','Free') as $t): ?>
<option value="<?= h($t) ?>" <?= $type===$t?'selected':'' ?>><?= h($t) ?>
</option><?php endforeach; ?>
</select>
</label>
<label><span>Sort by</span>
<select name="sort"><?php foreach(array('newest'=>'Newest','popular'=>'Most viewed','price_low'=>'Price: low to high','price_high'=>'Price: high to low') as $k=>$v): ?>
<option value="<?= h($k) ?>" <?= $sort===$k?'selected':'' ?>><?= h($v) ?>
</option><?php endforeach; ?>
</select>
</label>
<button class="button" type="submit">Apply filters</button><?php if($search!==''||$category!==''||$type!==''): ?><a class="clear-link" href="browse.php">Clear</a><?php endif; ?>
</form>
<div class="results-line"><strong><?= number_format($total) ?> <?= $total===1?'listing':'listings' ?></strong><span>Page <?= $page ?> of <?= $totalPages ?></span>
</div>
<div class="product-grid">
<?php if(mysqli_num_rows($result)): while($p=mysqli_fetch_assoc($result)): ?>
<article class="product-card"><a class="product-card-main" href="product.php?id=<?= (int)$p['id'] ?>">
<div class="product-visual visual-<?= strtolower(substr($p['category'],0,1)) ?>"><span><?= h(strtoupper(substr($p['category'],0,1))) ?></span><small><?= h($p['category']) ?></small>
</div>
<div class="product-content">
<div class="product-meta"><span class="pill"><?= h($p['item_type']) ?></span><span><?= h($p['item_condition']) ?></span>
</div>
<h3><?= h($p['title']) ?>
</h3>
<p class="product-location">⌖ <?= h($p['location']) ?>
</p>
<div class="product-bottom"><strong><?= h(money_label($p['price'],$p['item_type'])) ?></strong><span><?= (int)$p['views'] ?> views ↗</span>
</div>
</div></a>
</article><?php endwhile; else: ?>
<div class="empty-state grid-empty"><span class="empty-icon">⌕</span>
<h3>No listings found</h3>
<p>Try a different search or remove one of your filters.</p><a class="button button-outline" href="browse.php">Reset filters</a>
</div><?php endif; ?>
</div>
<?php if($totalPages>1): ?>
<nav class="pagination" aria-label="Pagination"><?php if($page>1): ?><a href="browse.php?<?= h(http_build_query(array_merge($_GET,array('page'=>$page-1)))) ?>">← Previous</a><?php endif; ?><span><?= $page ?> / <?= $totalPages ?></span><?php if($page<$totalPages): ?><a href="browse.php?<?= h(http_build_query(array_merge($_GET,array('page'=>$page+1)))) ?>">Next →</a><?php endif; ?>
</nav><?php endif; ?>
</main><?php include 'footer.php'; ?>
</body>
</html>
