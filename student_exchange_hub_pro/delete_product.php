<?php
require_once 'db.php'; require_login();
if($_SERVER['REQUEST_METHOD']!=='POST')redirect('my_products.php');
verify_csrf();
$id=filter_input(INPUT_POST,'id',FILTER_VALIDATE_INT);
$uid=current_user_id();
if($id && $id>0) {

    $stmt=mysqli_prepare($conn,'DELETE FROM products WHERE id=? AND seller_id=?');
    mysqli_stmt_bind_param($stmt,'ii',$id,$uid);
    mysqli_stmt_execute($stmt);
    $deleted=mysqli_stmt_affected_rows($stmt)>0;
    mysqli_stmt_close($stmt);
    set_flash($deleted?'success':'error',$deleted?'Listing deleted.':'Listing could not be deleted.');

}

redirect('my_products.php');
