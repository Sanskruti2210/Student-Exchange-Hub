<?php
require_once 'db.php'; require_login();
$id=filter_input(INPUT_GET,'id',FILTER_VALIDATE_INT); if(!$id||$id<1)redirect('my_products.php');
$uid=current_user_id();
$stmt=mysqli_prepare($conn,'SELECT * FROM products WHERE id=? AND seller_id=?');
mysqli_stmt_bind_param($stmt,'ii',$id,$uid);
mysqli_stmt_execute($stmt);
$p=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
mysqli_stmt_close($stmt);
if(!$p) {

    http_response_code(404);
    exit('Listing not found or you do not have permission to edit it.');

}

$errors=array();
if($_SERVER['REQUEST_METHOD']==='POST') {

    verify_csrf();
    $title=trim($_POST['title']??'');
    $category=trim($_POST['category']??'');
    $price=trim($_POST['price']??'');
    $condition=trim($_POST['condition']??'');
    $type=trim($_POST['type']??'');
    $description=trim($_POST['description']??'');
    $location=trim($_POST['location']??'');
    $status=trim($_POST['status']??$p['status']);
    if($title===''||mb_strlen($title)>150)$errors[]='Title is required and must be at most 150 characters.';
    if(!valid_category($category))$errors[]='Choose a valid category.';
    if(!is_numeric($price)||(float)$price<0||(float)$price>99999999.99)$errors[]='Enter a valid price.';
    if($condition===''||mb_strlen($condition)>50)$errors[]='Enter a valid condition.';
    if(!valid_item_type($type))$errors[]='Choose a valid listing type.';
    if($description===''||mb_strlen($description)>5000)$errors[]='Description is required and limited to 5,000 characters.';
    if($location===''||mb_strlen($location)>100)$errors[]='Enter a valid location.';
    if(!in_array($status,array('Available','Sold','Hidden'),true))$errors[]='Choose a valid status.';
    if($type==='Free')$price='0';
    if(!$errors) {

        $price=(float)$price;
        $stmt=mysqli_prepare($conn,'UPDATE products SET title=?,category=?,price=?,item_condition=?,item_type=?,description=?,location=?,status=? WHERE id=? AND seller_id=?');
        mysqli_stmt_bind_param($stmt,'ssdssssiii',$title,$category,$price,$condition,$type,$description,$location,$status,$id,$uid);
        if(mysqli_stmt_execute($stmt)) {

            set_flash('success','Your listing has been updated.');
            redirect('my_products.php');

        }
        else {

            $errors[]='Could not save your changes.';

        }
        mysqli_stmt_close($stmt);

    }

    $p=array_merge($p,array('title'=>$title,'category'=>$category,'price'=>$price,'item_condition'=>$condition,'item_type'=>$type,'description'=>$description,'location'=>$location,'status'=>$status));

}

?>
<!doctype html>
<html lang="en" data-theme="<?= h(current_theme()) ?>" data-bs-theme="<?= current_theme() === 'dark' ? 'dark' : 'light' ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Edit listing | Student Exchange Hub</title><?php include 'head_assets.php'; ?>
</head>
<body><?php include 'header.php'; ?>
<main class="page-width form-page">
<div class="page-title-row">
<div><span class="eyebrow">YOUR MARKETPLACE</span>
<h1>Edit listing</h1>
<p class="muted">Keep details accurate so students know what to expect.</p>
</div><a class="text-link" href="my_products.php">← My listings</a>
</div>
<section class="form-panel wide-panel"><?php if($errors): ?>
<div class="notice error">
<ul><?php foreach($errors as $e): ?>
<li><?= h($e) ?>
</li><?php endforeach; ?>
</ul>
</div><?php endif; ?>
<form method="post" class="stack-form" id="productForm" novalidate><input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
<label>Item title *<input name="title" required maxlength="150" value="<?= h($p['title']) ?>">
</label>
<div class="form-two">
<label>Category *<select name="category" required><?php foreach(array('Books','Electronics','Furniture','Accessories','Other') as $c): ?>
<option <?= $p['category']===$c?'selected':'' ?>><?= h($c) ?>
</option><?php endforeach; ?>
</select>
</label>
<label>Condition *<select name="condition" required><?php foreach(array('New','Like New','Excellent','Good','Fair','For Parts') as $c): ?>
<option <?= $p['item_condition']===$c?'selected':'' ?>><?= h($c) ?>
</option><?php endforeach; ?>
</select>
</label>
</div>
<div class="form-two">
<label>Listing type *<select name="type" id="itemType"><?php foreach(array('Sell','Exchange','Free') as $t): ?>
<option <?= $p['item_type']===$t?'selected':'' ?>><?= h($t) ?>
</option><?php endforeach; ?>
</select>
</label>
<label>Price in ₹ *<input type="number" name="price" id="price" min="0" step="0.01" required value="<?= h($p['price']) ?>">
</label>
</div>
<label>Campus meetup location *<input name="location" required maxlength="100" value="<?= h($p['location']) ?>">
</label>
<label>Description *<textarea name="description" rows="6" required maxlength="5000"><?= h($p['description']) ?>
</textarea>
</label>
<label>Listing status<select name="status">
<option value="Available" <?= $p['status']==='Available'?'selected':'' ?>>Available</option>
<option value="Sold" <?= $p['status']==='Sold'?'selected':'' ?>>Sold / exchanged</option>
<option value="Hidden" <?= $p['status']==='Hidden'?'selected':'' ?>>Hide from marketplace</option>
</select><small>Hidden listings are only visible in your account.</small>
</label>
<div class="form-actions">
<button class="button" type="submit">Save changes</button><a class="button button-outline" href="my_products.php">Cancel</a>
</div>
</form>
</section>
</main><?php include 'footer.php'; ?>
</body>
</html>
