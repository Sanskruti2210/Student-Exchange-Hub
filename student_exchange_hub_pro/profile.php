<?php
require_once 'db.php';
require_login();
$uid=current_user_id();
$errors=array();
$message='';
$stmt=mysqli_prepare($conn,'SELECT name,email,phone,course,college,created_at FROM users WHERE id=?');
mysqli_stmt_bind_param($stmt,'i',$uid);
mysqli_stmt_execute($stmt);
$user=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
if($_SERVER['REQUEST_METHOD']==='POST') {

    verify_csrf();
    $name=trim($_POST['name']??'');
    $phone=trim($_POST['phone']??'');
    $course=trim($_POST['course']??'');
    $college=trim($_POST['college']??'');
    if(mb_strlen($name)<2||mb_strlen($name)>100)$errors[]='Name must be between 2 and 100 characters.';
    if(strlen($phone)>30||mb_strlen($course)>100||mb_strlen($college)>150)$errors[]='One of the fields is too long.';
    if(!$errors) {

        $stmt=mysqli_prepare($conn,'UPDATE users SET name=?,phone=?,course=?,college=? WHERE id=?');
        mysqli_stmt_bind_param($stmt,'ssssi',$name,$phone,$course,$college,$uid);
        if(mysqli_stmt_execute($stmt)) {

            $_SESSION['user_name']=$name;
            $message='Profile updated successfully.';
            $user=array_merge($user,array('name'=>$name,'phone'=>$phone,'course'=>$course,'college'=>$college));

        }
        else {

            $errors[]='Could not update your profile.';

        }
        mysqli_stmt_close($stmt);

    }

}

?>
<!doctype html>
<html lang="en" data-theme="<?= h(current_theme()) ?>" data-bs-theme="<?= current_theme() === 'dark' ? 'dark' : 'light' ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Profile | Student Exchange Hub</title><?php include 'head_assets.php'; ?>
</head>
<body><?php include 'header.php'; ?>
<main class="page-width form-page">
<div class="page-title-row">
<div><span class="eyebrow">YOUR ACCOUNT</span>
<h1>Profile settings</h1>
<p class="muted">Update how other students see your profile.</p>
</div>
</div>
<section class="form-panel wide-panel"><?php if($message): ?>
<div class="notice success"><?= h($message) ?>
</div><?php endif; ?><?php if($errors): ?>
<div class="notice error">
<ul><?php foreach($errors as $e): ?>
<li><?= h($e) ?>
</li><?php endforeach; ?>
</ul>
</div><?php endif; ?>
<form method="post" class="stack-form"><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
<label>Full name<input name="name" required maxlength="100" value="<?= h($user['name']) ?>">
</label>
<label>Email address<input value="<?= h($user['email']) ?>" disabled><small>Email address cannot be changed here.</small>
</label>
<div class="form-two">
<label>Phone<input name="phone" maxlength="30" value="<?= h($user['phone']) ?>">
</label>
<label>Course<input name="course" maxlength="100" value="<?= h($user['course']) ?>">
</label>
</div>
<label>College<input name="college" maxlength="150" value="<?= h($user['college']) ?>">
</label>
<div class="form-actions">
<button class="button" type="submit">Save profile</button><a class="button button-outline" href="dashboard.php">Cancel</a>
</div>
</form>
</section>
</main><?php include 'footer.php'; ?>
</body>
</html>
