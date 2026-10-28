<?php
require_once 'db.php';
// Hero photos. Drop your own files at images/hero-1.jpg ... hero-4.jpg and they are used automatically;
// otherwise the Unsplash photos below load (free to use under the Unsplash License).
function hero_img($n, $unsplashId) {

    return file_exists(__DIR__ . "/images/hero-$n.jpg") ? "images/hero-$n.jpg" : "https://unsplash.com/photos/$unsplashId/download?force=true&w=1400";

}

$heroImgs = array(
1 => array(hero_img(1,'RBxh2DjoQWY'), 'Library shelves full of books'),
2 => array(hero_img(2,'984e1Fk4E04'), 'Stack of books on a library shelf'),
3 => array(hero_img(3,'0mX_FXDojEE'), 'Laptop and monitor on a desk'),
4 => array(hero_img(4,'6OA05PwDzKw'), 'Student working on a laptop with headphones'),
);
// Cookie: remember when the visitor was last here (only with "Accept all")
$lastVisit = null;
if (consent_allows('stats')) {

    if (!empty($_COOKIE['seh_last_visit']) && ctype_digit($_COOKIE['seh_last_visit'])) {

        $lastVisit = (int)$_COOKIE['seh_last_visit'];
    }

    if (empty($_SESSION['visit_marked'])) {

        $_SESSION['visit_marked'] = true; seh_set_cookie('seh_last_visit', time(), 365);
    }

    else {

        $lastVisit = $_SESSION['prev_visit'] ?? $lastVisit;
    }

    if ($lastVisit) {

        $_SESSION['prev_visit'] = $lastVisit;
    }

}

$recent = isset($_SESSION['recent']) && is_array($_SESSION['recent']) ? $_SESSION['recent'] : array();
$stats = marketplace_stats($conn);   // calls stored procedure sp_marketplace_stats (falls back to plain SQL)
$latest = mysqli_query($conn, "SELECT p.id,p.title,p.category,p.price,p.item_type,p.item_condition,p.location,p.created_at,u.name AS seller_name FROM products p JOIN users u ON u.id=p.seller_id WHERE p.status='Available' ORDER BY p.created_at DESC LIMIT 6");
?>
<!doctype html>
<html lang="en" data-theme="<?= h(current_theme()) ?>" data-bs-theme="<?= current_theme() === 'dark' ? 'dark' : 'light' ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Student Exchange Hub Pro | Campus marketplace</title><meta name="description" content="Buy, sell, give away and exchange useful student essentials on your campus."><?php include 'head_assets.php'; ?>
</head>
<body>
<?php include 'header.php'; ?>
<main>
<?php if ($lastVisit && time() - $lastVisit > 3600): ?>
<div class="welcome-back">Welcome back<?= is_logged_in() ? ', ' . h($_SESSION['user_name'] ?? '') : '' ?>! Your last visit was on <?= h(date('d M Y, g:i a', $lastVisit)) ?>. <a href="browse.php">See what's new →</a>
</div><?php endif; ?>
<section id="heroCarousel" class="carousel slide hero-carousel" data-bs-ride="carousel" data-bs-interval="4500" data-bs-pause="false" data-bs-wrap="true" aria-roledescription="carousel" aria-label="Featured highlights">
<div class="carousel-indicators">
<button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1">
</button>
<button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2">
</button>
<button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3">
</button>
<button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3" aria-label="Slide 4">
</button>
</div>
<div class="carousel-inner">
<div class="carousel-item active">
<div class="container-xl slide-pad">
<div class="row align-items-center g-4">
<div class="col-lg-6 slide-copy"><span class="eyebrow">THE CAMPUS MARKETPLACE</span>
<h1>Good things deserve <em>a second semester.</em>
</h1>
<p>Find affordable books, gadgets and everyday essentials from fellow students. Give unused things a new home.</p>
<div class="hero-actions"><a class="button" href="browse.php">Explore marketplace <span aria-hidden="true">→</span></a><a class="button button-outline" href="add_product.php">List an item</a>
</div>
<div class="hero-proof"><span><strong data-stat="items"><?= number_format($stats['items']) ?></strong> active listings</span><span><strong data-stat="students"><?= number_format($stats['students']) ?></strong> student accounts</span><span><strong data-stat="categories"><?= number_format($stats['categories']) ?></strong> categories</span>
</div>
</div>
<div class="col-lg-6 slide-art"><figure class="slide-photo"><img src="<?= h($heroImgs[1][0]) ?>" alt="<?= h($heroImgs[1][1]) ?>" fetchpriority="high" decoding="async" referrerpolicy="no-referrer"></figure>
<div class="photo-chip pc1"><strong>Books</strong><small>from ₹50</small>
</div>
<div class="photo-chip pc2"><strong>Gadgets</strong><small>half the price</small>
</div>
</div>
</div>
</div>
</div>
<div class="carousel-item">
<div class="container-xl slide-pad">
<div class="row align-items-center g-4">
<div class="col-lg-6 slide-copy"><span class="eyebrow">BOOKS &amp; NOTES</span>
<h1>Pass the <em>knowledge</em> along.</h1>
<p>Textbooks, notes and reference guides your seniors no longer need, ready for your next exam season.</p>
<div class="hero-actions"><a class="button" href="browse.php?category=Books">Browse books <span aria-hidden="true">→</span></a><a class="button button-outline" href="add_product.php">Sell yours</a>
</div>
</div>
<div class="col-lg-6 slide-art"><figure class="slide-photo"><img src="<?= h($heroImgs[2][0]) ?>" alt="<?= h($heroImgs[2][1]) ?>" loading="lazy" decoding="async" referrerpolicy="no-referrer"></figure>
<div class="photo-chip pc1"><strong>Stationery</strong><small>bundle deals</small>
</div>
<div class="photo-chip pc2"><strong>Semester notes</strong><small>exam ready</small>
</div>
</div>
</div>
</div>
</div>
<div class="carousel-item">
<div class="container-xl slide-pad">
<div class="row align-items-center g-4">
<div class="col-lg-6 slide-copy"><span class="eyebrow">TECH &amp; ELECTRONICS</span>
<h1>Upgrade your setup, <em>not your bill.</em>
</h1>
<p>Laptops, calculators, headphones and more, priced fairly by students who have used them.</p>
<div class="hero-actions"><a class="button" href="browse.php?category=Electronics">Shop electronics <span aria-hidden="true">→</span></a><a class="button button-outline" href="how_it_works.php">Stay safe</a>
</div>
</div>
<div class="col-lg-6 slide-art"><figure class="slide-photo"><img src="<?= h($heroImgs[3][0]) ?>" alt="<?= h($heroImgs[3][1]) ?>" loading="lazy" decoding="async" referrerpolicy="no-referrer"></figure>
<div class="photo-chip pc1"><strong>Audio</strong><small>pre-loved</small>
</div>
<div class="photo-chip pc2"><strong>Calculators</strong><small>exam approved</small>
</div>
</div>
</div>
</div>
</div>
<div class="carousel-item">
<div class="container-xl slide-pad">
<div class="row align-items-center g-4">
<div class="col-lg-6 slide-copy"><span class="eyebrow">FREE &amp; EXCHANGE</span>
<h1>Give it away. <em>Swap it around.</em>
</h1>
<p>Moving out or upgrading? Offer items for free or exchange them with someone on campus. Everyone wins.</p>
<div class="hero-actions"><a class="button" href="add_product.php">List an item <span aria-hidden="true">→</span></a><a class="button button-outline" href="browse.php?type=Free">See free stuff</a>
</div>
</div>
<div class="col-lg-6 slide-art"><figure class="slide-photo"><img src="<?= h($heroImgs[4][0]) ?>" alt="<?= h($heroImgs[4][1]) ?>" loading="lazy" decoding="async" referrerpolicy="no-referrer"></figure>
<div class="photo-chip pc1"><strong>Free items</strong><small>take what you need</small>
</div>
<div class="photo-chip pc2"><strong>Exchange</strong><small>swap with students</small>
</div>
</div>
</div>
</div>
</div>
</div>
<button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev"><span aria-hidden="true">‹</span><span class="visually-hidden">Previous slide</span>
</button>
<button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next"><span aria-hidden="true">›</span><span class="visually-hidden">Next slide</span>
</button>
</section>

<section class="page-width section reveal">
<div class="section-heading">
<div><span class="eyebrow">START EXPLORING</span>
<h2>Find what you need.</h2>
</div><a class="text-link" href="categories.php">All categories →</a>
</div>
<div class="row g-3 row-cols-2 row-cols-md-3 row-cols-xl-5">
<?php $cats = array(array('Books','B','For the next big idea'),array('Electronics','E','Tools for your toolkit'),array('Furniture','F','Make your space yours'),array('Accessories','A','Everyday essentials'),array('Other','+','Unexpected useful finds')); foreach($cats as $c): ?>
<div class="col"><a class="category-tile h-100" href="browse.php?category=<?= urlencode($c[0]) ?>"><span class="category-symbol"><?= h($c[1]) ?></span><span><strong><?= h($c[0]) ?></strong><small><?= h($c[2]) ?></small></span><span class="tile-arrow">↗</span></a>
</div>
<?php endforeach; ?>
</div>
</section>

<section class="section latest-section">
<div class="page-width">
<div class="section-heading">
<div><span class="eyebrow">FRESH ON THE HUB</span>
<h2>Recently listed</h2>
</div><a class="text-link" href="browse.php">View all items →</a>
</div>
<div class="row g-4">
<?php if ($latest && mysqli_num_rows($latest)): while($p=mysqli_fetch_assoc($latest)): ?>
<div class="col-12 col-md-6 col-lg-4">
<article class="product-card h-100"><a class="product-card-main" href="product.php?id=<?= (int)$p['id'] ?>">
<div class="product-visual visual-<?= strtolower(substr($p['category'],0,1)) ?>"><span><?= h(strtoupper(substr($p['category'],0,1))) ?></span><small><?= h($p['category']) ?></small>
</div>
<div class="product-content">
<div class="product-meta"><span class="pill"><?= h($p['item_type']) ?></span><span><?= h($p['item_condition']) ?></span>
</div>
<h3><?= h($p['title']) ?>
</h3>
<p class="product-location"><?= h($p['location']) ?>
</p>
<div class="product-bottom"><strong><?= h(money_label($p['price'],$p['item_type'])) ?></strong><span>View item ↗</span>
</div>
</div></a>
</article>
</div>
<?php endwhile; else: ?>
<div class="col-12">
<div class="empty-state">
<h3>Your campus marketplace starts here.</h3>
<p>No listings yet. Create an account and publish the first item.</p><a class="button" href="register.php">Join the Hub</a>
</div>
</div><?php endif; ?>
</div>
</div>
</section>
<?php if ($recent): ?>
<section class="page-width section reveal" style="padding-bottom:10px">
<div class="section-heading">
<div><span class="eyebrow">PICK UP WHERE YOU LEFT OFF</span>
<h2>Recently viewed</h2>
</div>
</div>
<div class="recent-strip"><?php foreach ($recent as $rid => $r): ?><a class="recent-chip" href="product.php?id=<?= (int)$rid ?>"><?= h($r['title']) ?> <small><?= h($r['category']) ?></small></a><?php endforeach; ?>
</div>
</section><?php endif; ?>
<section class="page-width section how-strip reveal">
<div><span class="eyebrow">SIMPLE BY DESIGN</span>
<h2>One student’s clutter<br>is another’s <em>great find.</em>
</h2>
</div>
<div class="steps">
<div><span>01</span>
<p><strong>Discover</strong><small>Search useful things by category.</small>
</p>
</div>
<div><span>02</span>
<p><strong>Connect</strong><small>Message the student who listed it.</small>
</p>
</div>
<div><span>03</span>
<p><strong>Exchange</strong><small>Agree on a safe campus meetup.</small>
</p>
</div>
</div>
</section>
</main><?php include 'footer.php'; ?>
</body>
</html>
