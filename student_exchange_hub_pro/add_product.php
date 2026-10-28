<?php
require_once 'db.php'; require_login();
$errors=array(); $title=$category=$price=$condition=$type=$description=$location='';
if($_SERVER['REQUEST_METHOD']==='POST') {

    verify_csrf();
    $title=trim($_POST['title']??''); $category=trim($_POST['category']??''); $price=trim($_POST['price']??'');
    $condition=trim($_POST['condition']??''); $type=trim($_POST['type']??''); $description=trim($_POST['description']??''); $location=trim($_POST['location']??'');
    if($title===''||mb_strlen($title)>150)$errors[]='Title is required and must be at most 150 characters.';
    if(!valid_category($category))$errors[]='Choose a valid category.';
    if(!is_numeric($price)||(float)$price<0||(float)$price>99999999.99)$errors[]='Enter a valid non-negative price.';
    if($condition===''||mb_strlen($condition)>50)$errors[]='Enter the item condition (maximum 50 characters).';
    if(!valid_item_type($type))$errors[]='Choose Sell, Exchange or Free.';
    if($description===''||mb_strlen($description)>5000)$errors[]='Description is required and must be at most 5,000 characters.';
    if($location===''||mb_strlen($location)>100)$errors[]='Enter a meetup location (maximum 100 characters).';
    if($type==='Free')$price='0';
    if(!$errors) {

        $price=(float)$price;
        $uid=current_user_id();
        $stmt=mysqli_prepare($conn,"INSERT INTO products (title,category,price,item_condition,item_type,description,location,seller_id,status) VALUES (?,?,?,?,?,?,?,?,'Available')");
        mysqli_stmt_bind_param($stmt,'ssdssssi',$title,$category,$price,$condition,$type,$description,$location,$uid);
        if(mysqli_stmt_execute($stmt)) {

            set_flash('success','Your listing is live on the marketplace.');
            redirect('product.php?id=' . mysqli_insert_id($conn));

        }
        else {

            $errors[]='Could not publish your listing. Please try again.';

        }
        mysqli_stmt_close($stmt);

    }

}

?>
<!doctype html>
<html lang="en" data-theme="<?= h(current_theme()) ?>" data-bs-theme="<?= current_theme() === 'dark' ? 'dark' : 'light' ?>">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>List an item | Student Exchange Hub</title><?php include 'head_assets.php'; ?>
</head>
<body><?php include 'header.php'; ?>
<main class="page-width form-page">
<div class="page-title-row">
<div><span class="eyebrow">GIVE IT A NEW HOME</span>
<h1>List an item</h1>
<p class="muted">A clear, honest listing helps the right student find it.</p>
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
<label>Item title *<input name="title" id="title" required maxlength="150" value="<?= h($title) ?>" placeholder="e.g. Engineering Mathematics textbook">
</label>
<div class="form-two">
<label>Category *<select name="category" id="category" required>
<option value="">Choose a category</option><?php foreach(array('Books','Electronics','Furniture','Accessories','Other') as $c): ?>
<option <?= $category===$c?'selected':'' ?>><?= h($c) ?>
</option><?php endforeach; ?>
</select>
</label>
<label>Condition *<select name="condition" required>
<option value="">Choose condition</option><?php foreach(array('New','Like New','Excellent','Good','Fair','For Parts') as $c): ?>
<option <?= $condition===$c?'selected':'' ?>><?= h($c) ?>
</option><?php endforeach; ?>
</select>
</label>
</div>
<div class="form-two">
<label>Listing type *<select name="type" id="itemType" required>
<option value="">Choose type</option><?php foreach(array('Sell','Exchange','Free') as $t): ?>
<option <?= $type===$t?'selected':'' ?>><?= h($t) ?>
</option><?php endforeach; ?>
</select>
</label>
<label>Price in ₹ *<input type="number" name="price" id="price" min="0" max="99999999.99" step="0.01" required value="<?= h($price) ?>" placeholder="0.00"><small>Free items are automatically listed at ₹0.</small>
</label>
</div>
<label>Campus meetup location *<input name="location" required maxlength="100" value="<?= h($location) ?>" placeholder="e.g. Library entrance">
</label>
<label>Description *<textarea name="description" rows="6" required maxlength="5000" placeholder="Describe the item's condition, age, included accessories and anything a student should know."><?= h($description) ?>
</textarea><small><span id="descriptionCount">0</span>/5000 characters</small>
</label>
<div class="form-actions">
<button class="button" type="submit">Publish listing</button><a class="button button-outline" href="browse.php">Cancel</a>
</div>
</form>
</section>
</main><?php include 'footer.php'; ?>
</body>
</html>
