<?php
require_once 'db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect('index.php');
verify_csrf();
$_SESSION = array();
if (ini_get('session.use_cookies')) {

    $params = session_get_cookie_params();
    setcookie(session_name(), '', array('expires' => time() - 42000, 'path' => $params['path'], 'domain' => $params['domain'], 'secure' => $params['secure'], 'httponly' => $params['httponly'], 'samesite' => 'Lax'));

}

session_destroy();
redirect('index.php');
