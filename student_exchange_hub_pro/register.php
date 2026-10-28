<?php
require_once 'db.php';
if (is_logged_in()) redirect('dashboard.php');
$errors = array();
$name = $email = $phone = $course = $college = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    verify_csrf();
    $name = trim($_POST['name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $course = trim($_POST['course'] ?? '');
    $college = trim($_POST['college'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';
    if (mb_strlen($name) < 2 || mb_strlen($name) > 100) $errors[] = 'Enter a name between 2 and 100 characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 8) $errors[] = 'Password must contain at least 8 characters.';
    if ($password !== $confirm) $errors[] = 'The passwords do not match.';
    if (strlen($phone) > 30 || strlen($course) > 100 || strlen($college) > 150) $errors[] = 'One of the profile fields is too long.';
    if (!$errors) {

        $stmt = mysqli_prepare($conn, 'INSERT INTO users (name,email,password,phone,course,college) VALUES (?,?,?,?,?,?)');
        $hash = password_hash($password, PASSWORD_DEFAULT);
        mysqli_stmt_bind_param($stmt, 'ssssss', $name, $email, $hash, $phone, $course, $college);
        if (mysqli_stmt_execute($stmt)) {

            session_regenerate_id(true);
            $_SESSION['user_id'] = mysqli_insert_id($conn);
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            set_flash('success', 'Your account is ready. Welcome to the Hub.');
            redirect('dashboard.php');

        }
        else {

            $errors[] = mysqli_errno($conn) === 1062 ? 'An account with this email already exists.' : 'Could not create the account. Please try again.';

        }

        mysqli_stmt_close($stmt);

    }

}

?>
<!doctype html>
<html lang="en" data-theme="<?= h(current_theme()) ?>" data-bs-theme="<?= current_theme() === 'dark' ? 'dark' : 'light' ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Create account | Student Exchange Hub</title><?php include 'head_assets.php'; ?>
</head>
<body><?php include 'header.php'; ?>
<main class="page-width form-layout">
<div class="form-intro"><span class="eyebrow">JOIN YOUR CAMPUS</span>
<h1>Your next great find starts here.</h1>
<p>Create an account to list items, save money and connect with students in your community.</p>
<div class="form-aside"><strong>Keep it local.</strong>
<p>Meet in public campus spaces, inspect items before exchanging, and never share your password.</p>
</div>
</div>
<section class="form-panel">
<h2>Create your account</h2>
<p class="muted">Fields marked * are required.</p><?php if($errors): ?>
<div class="notice error">
<ul><?php foreach($errors as $e): ?>
<li><?= h($e) ?>
</li><?php endforeach; ?>
</ul>
</div><?php endif; ?>
<form method="post" class="stack-form" id="registerForm" novalidate><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
<label>Full name *<input name="name" required maxlength="100" value="<?= h($name) ?>" autocomplete="name">
</label>
<label>Email address *<input type="email" name="email" required maxlength="190" value="<?= h($email) ?>" autocomplete="email">
</label>
<label>Password *<input type="password" name="password" required minlength="8" autocomplete="new-password"><small>Use at least 8 characters.</small>
</label>
<label>Confirm password *<input type="password" name="confirm_password" required minlength="8" autocomplete="new-password">
</label>
<div class="form-two">
<label>Phone (optional)<input name="phone" maxlength="30" value="<?= h($phone) ?>" autocomplete="tel">
</label>
<label>Course (optional)<input name="course" maxlength="100" value="<?= h($course) ?>">
</label>
</div>
<label>College (optional)<input name="college" maxlength="150" value="<?= h($college) ?>">
</label>
<button class="button button-wide" type="submit">Create account</button>
<p class="form-foot">Already a member? <a href="login.php">Sign in</a>
</p>
</form>
</section>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
