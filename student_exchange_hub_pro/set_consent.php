<?php
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php');
verify_csrf();
$choice = $_POST['choice'] ?? 'custom';
if ($choice === 'all') {

    $cats = array('pref', 'stats');
}

elseif ($choice === 'essential') {

    $cats = array();
}

else {

    // "custom": read the toggles from the preferences dialog
    $cats = array();
    if (!empty($_POST['pref']))  $cats[] = 'pref';
    if (!empty($_POST['stats'])) $cats[] = 'stats';

}

$value = $cats ? implode(',', $cats) : 'essential';
if (count($cats) === 2) $value = 'all';
$_SESSION['consent'] = $value;
seh_set_cookie('seh_consent', $value, 365);

// Remove optional cookies the visitor did not allow; set the theme cookie if allowed
if (!in_array('pref', $cats, true)) {

    seh_set_cookie('seh_theme', '', 0); seh_set_cookie('seh_email', '', 0);
}

else {

    seh_set_cookie('seh_theme', current_theme(), 365);
}

if (!in_array('stats', $cats, true)) {

    seh_set_cookie('seh_last_visit', '', 0);
}

if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')) {

    header('Content-Type: application/json'); echo json_encode(array('ok' => true, 'value' => $value)); exit;

}

$back = $_POST['return'] ?? 'index.php';
if (!preg_match('/^[a-z_]+\.php(\?[A-Za-z0-9_=&%.\-]*)?$/', $back)) $back = 'index.php';
redirect($back);
