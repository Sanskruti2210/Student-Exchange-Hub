<?php
require_once 'db.php';
if (is_logged_in()) redirect('dashboard.php');
$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && functional_cookies_allowed() && !empty($_COOKIE['seh_email']) && filter_var($_COOKIE['seh_email'], FILTER_VALIDATE_EMAIL)) {

    $email = strtolower($_COOKIE['seh_email']); $rememberChecked = true;
}
else {

    $rememberChecked = false;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';
    $stmt = mysqli_prepare($conn, 'SELECT id,name,email,password FROM users WHERE email=? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    if ($user && password_verify($password, $user['password'])) {

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['login_time'] = time();
        $_SESSION['session_rotated'] = time();
        $_SESSION['last_activity'] = time();
        if (!empty($_POST['remember_email']) && functional_cookies_allowed()) {

            seh_set_cookie('seh_email', $user['email'], 30);
        }

        else {

            seh_set_cookie('seh_email', '', 0);
        }

        set_flash('success', 'You are signed in.');
        redirect('dashboard.php');

    }

    $error = 'The email or password is incorrect.';
    mysqli_stmt_close($stmt);

}

?>
<!doctype html>
<html lang="en" data-theme="<?= h(current_theme()) ?>" data-bs-theme="<?= current_theme() === 'dark' ? 'dark' : 'light' ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Sign in | Student Exchange Hub</title><?php include 'head_assets.php'; ?>
</head>
<body><?php include 'header.php'; ?>
<main class="page-width form-layout login-layout">
<div class="form-intro"><span class="eyebrow">WELCOME BACK</span>
<h1>Your campus community is waiting.</h1>
<p>Sign in to manage your listings and continue conversations with other students.</p>
<div class="form-aside"><strong>Exchange with care.</strong>
<p>Keep conversations respectful and arrange meetups in public, familiar places.</p>
</div>
</div>
<section class="form-panel">
<h2>Sign in</h2>
<p class="muted">Use the email address associated with your account.</p><?php show_flash(); ?><?php if($error): ?>
<div class="notice error"><?= h($error) ?>
</div><?php endif; ?>
<form method="post" class="stack-form" id="loginForm" novalidate><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
<label>Email address<input type="email" name="email" required autocomplete="email" value="<?= h($email) ?>">
</label>
<label>Password<input type="password" name="password" required autocomplete="current-password">
</label>
<label class="check-row"><input type="checkbox" name="remember_email" value="1" <?= $rememberChecked ? 'checked' : '' ?>> <span>Remember my email on this device <small>(uses a cookie; needs “Preferences” cookies)</small></span>
</label>
<button class="button button-wide" type="submit">Sign in</button>
<p class="form-foot">New to the Hub? <a href="register.php">Create an account</a>
</p>
</form>
</section>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
