<?php
// JSON endpoint for jQuery AJAX live search suggestions (uses a stored procedure).
require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');
$q = trim((string)($_GET['q'] ?? ''));
if (mb_strlen($q) < 2) {

    echo '[]'; exit;
}

$q = mb_substr($q, 0, 60);
echo json_encode(search_titles($conn, $q));
