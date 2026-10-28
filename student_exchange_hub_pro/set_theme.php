<?php
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php');
verify_csrf();
$theme = $_POST['theme'] ?? '';
if (in_array($theme, allowed_themes(), true)) {

    $_SESSION['theme'] = $theme;                       // session: always remembered while the visit lasts
    if (functional_cookies_allowed()) {

        // cookie: remembered across visits (needs consent)
        seh_set_cookie('seh_theme', $theme, 365);

    }

}

$wantsJson = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest');
if ($wantsJson) {

    header('Content-Type: application/json');
    echo json_encode(array('ok' => true, 'theme' => current_theme(), 'persisted' => functional_cookies_allowed()));
    exit;

}

$back = $_POST['return'] ?? 'index.php';
if (!preg_match('/^[a-z_]+\.php(\?[A-Za-z0-9_=&%.\-]*)?$/', $back)) $back = 'index.php';
redirect($back);
