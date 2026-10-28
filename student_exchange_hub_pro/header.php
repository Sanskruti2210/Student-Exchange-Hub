<?php
$current = basename($_SERVER['PHP_SELF'] ?? '');
$themeReturn = $current . (!empty($_SERVER['QUERY_STRING']) ? '?' . $_SERVER['QUERY_STRING'] : '');
$themeList = array('ocean' => 'Light', 'dark' => 'Dark');
$hubActive = in_array($current, array('dashboard.php', 'my_products.php', 'messages.php', 'profile.php', 'add_product.php', 'edit_product.php'), true);
$marketActive = in_array($current, array('browse.php', 'product.php'), true) && (($_GET['type'] ?? '') !== 'Free');
$categoriesActive = $current === 'categories.php';
$freeActive = $current === 'browse.php' && (($_GET['type'] ?? '') === 'Free');
$howActive = $current === 'how_it_works.php';
$aboutActive = $current === 'about.php';
$remaining = 0; $loginSince = '';
if (is_logged_in()) {

    $remaining = max(0, SESSION_IDLE_SECONDS - (time() - (int)($_SESSION['last_activity'] ?? time())));
    $loginSince = isset($_SESSION['login_time']) ? date('d M, g:i a', (int)$_SESSION['login_time']) : '';

}

$initial = strtoupper(substr((string)($_SESSION['user_name'] ?? 'S'), 0, 1));
?>
<header class="site-header">
<nav class="navbar navbar-expand-lg" aria-label="Main navigation">
<div class="container-xl">
<a class="navbar-brand brand" href="index.php" aria-label="Student Exchange Hub Pro home">
<img class="brand-logo" src="assets/brand-logo.png" alt="Student Exchange Hub logo"><span class="brand-text"><strong>Student Exchange Hub</strong><small>Buy less new. Exchange more.</small></span>
</a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span>
</button>

<div class="collapse navbar-collapse" id="mainNav">
<div class="seh-nav-shell">
<div class="seh-nav-brand-slot" aria-hidden="true">
</div>
<ul class="navbar-nav seh-nav-tabs align-items-lg-center">
<li class="nav-item"><a class="nav-link <?= $current === 'index.php' ? 'active' : '' ?>" href="index.php">Home</a>
</li>
<li class="nav-item"><a class="nav-link <?= $marketActive ? 'active' : '' ?>" href="browse.php">Marketplace</a>
</li>
<li class="nav-item"><a class="nav-link <?= $categoriesActive ? 'active' : '' ?>" href="categories.php">Categories</a>
</li>
<li class="nav-item"><a class="nav-link <?= $freeActive ? 'active' : '' ?>" href="browse.php?type=Free">Freebies</a>
</li>
<li class="nav-item"><a class="nav-link <?= $howActive ? 'active' : '' ?>" href="how_it_works.php">Guide</a>
</li>
<li class="nav-item"><a class="nav-link <?= $aboutActive ? 'active' : '' ?>" href="about.php">About</a>
</li>

<?php if (is_logged_in()): ?>
<li class="nav-item dropdown seh-desktop-hub">
<a class="nav-link dropdown-toggle <?= $hubActive ? 'active' : '' ?>" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">My Hub</a>
<div class="dropdown-menu dropdown-menu-lg-end">
<a class="dropdown-item" href="dashboard.php"><span class="dd-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg></span><span><strong>Dashboard</strong><small>Your overview</small></span></a>
<a class="dropdown-item" href="my_products.php"><span class="dd-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 8l-9-5-9 5v8l9 5 9-5V8z"/><path d="M3 8l9 5 9-5M12 13v8"/></svg></span><span><strong>My listings</strong><small>Manage your items</small></span></a>
<a class="dropdown-item" href="messages.php"><span class="dd-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12a8 8 0 0 1-11.5 7.2L4 20l1-4.5A8 8 0 1 1 21 12z"/></svg></span><span><strong>Messages</strong><small>Chat with students</small></span></a>
<a class="dropdown-item" href="add_product.php"><span class="dd-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg></span><span><strong>List an item</strong><small>Sell, swap or give</small></span></a>
</div>
</li>
<?php endif; ?>
</ul>

<div class="seh-nav-actions">
<div class="nav-item dropdown">
<button class="nav-link theme-btn dropdown-toggle" type="button" id="themeBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false"><span class="theme-dot" aria-hidden="true"></span>Theme</button>
<form class="dropdown-menu dropdown-menu-lg-end theme-panel" id="themeMenu" action="set_theme.php" method="post" role="radiogroup" aria-label="Choose a colour theme">
<input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
<input type="hidden" name="return" value="<?= h($themeReturn) ?>">
<h4>Colour theme</h4>
<?php foreach ($themeList as $key => $label): ?>
<button class="theme-option" type="submit" name="theme" value="<?= h($key) ?>" role="radio" aria-checked="<?= current_theme() === $key ? 'true' : 'false' ?>"><span class="swatch sw-<?= h($key) ?>"></span><?= h($label) ?>
</button>
<?php endforeach; ?>
</form>
</div>

<?php if (is_logged_in()): ?>
<div class="nav-item dropdown">
<button class="nav-link user-btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="user-avatar"><?= h($initial) ?></span><span class="btn-label"><?= h($_SESSION['user_name'] ?? 'Student') ?></span>
</button>
<div class="dropdown-menu dropdown-menu-lg-end">
<div class="session-card" id="sessionCard" data-remaining="<?= (int)$remaining ?>">
<span class="live-dot"></span>
<div><strong>Session active</strong><small><?= $loginSince ? 'Signed in ' . h($loginSince) . ' · ' : '' ?>auto sign-out in <b id="sessionTimer">--:--</b></small>
</div>
</div>
<a class="dropdown-item" href="profile.php"><span class="dd-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg></span><span><strong>My profile</strong><small><?= h($_SESSION['user_email'] ?? '') ?></small></span></a>
<form action="logout.php" method="post" class="m-0">
<input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
<button class="dropdown-item" type="submit"><span class="dd-ico"><svg viewBox="0 0 24 24" width="20" height="20" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 4H5v16h5M15 8l4 4-4 4M19 12H9"/></svg></span><span><strong>Sign out</strong><small>End this session</small></span>
</button>
</form>
</div>
</div>
<?php else: ?>
<a class="nav-link login-link" href="login.php">Sign in</a>
<a class="create-account-icon" href="register.php" aria-label="Create account" title="Create account"><span aria-hidden="true">+</span></a>
<?php endif; ?>
</div>
</div>
</div>
</div>
</nav>
</header>
