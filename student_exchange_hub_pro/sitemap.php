<?php require_once 'db.php'; ?>
<!doctype html>
<html lang="en" data-theme="<?= h(current_theme()) ?>" data-bs-theme="<?= current_theme() === 'dark' ? 'dark' : 'light' ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Sitemap | Student Exchange Hub</title><meta name="description" content="Every page of Student Exchange Hub Pro in one place."><?php include 'head_assets.php'; ?>
</head>
<body>
<?php include 'header.php'; ?>
<main class="page-width content-page">
<div class="page-title-row">
<div><span class="eyebrow">NAVIGATION</span>
<h1>Sitemap</h1>
<p class="muted">All pages of the website. Machine readable version: <a href="sitemap.xml">sitemap.xml</a>
</p>
</div>
</div>
<div class="row g-4">
<div class="col-md-6 col-lg-4">
<div class="dashboard-card h-100">
<h2>Public pages</h2>
<ul class="muted mb-0">
<li><a href="index.php">Home</a>
</li>
<li><a href="browse.php">Marketplace</a>
</li>
<li><a href="categories.php">Categories</a>
</li>
<li><a href="how_it_works.php">How it works &amp; FAQ</a>
</li>
<li><a href="about.php">About &amp; contact</a>
</li>
<li><a href="sitemap.php">Sitemap</a>
</li>
</ul>
</div>
</div>
<div class="col-md-6 col-lg-4">
<div class="dashboard-card h-100">
<h2>Account</h2>
<ul class="muted mb-0">
<li><a href="register.php">Create account</a>
</li>
<li><a href="login.php">Sign in</a>
</li>
</ul>
</div>
</div>
<div class="col-md-6 col-lg-4">
<div class="dashboard-card h-100">
<h2>Signed-in area</h2>
<ul class="muted mb-0">
<li><a href="dashboard.php">Dashboard</a>
</li>
<li><a href="my_products.php">My listings</a>
</li>
<li><a href="add_product.php">List an item</a>
</li>
<li><a href="messages.php">Messages</a>
</li>
<li><a href="profile.php">Profile</a>
</li>
</ul>
</div>
</div>
</div>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
