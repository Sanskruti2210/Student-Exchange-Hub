<?php
// JSON endpoint polled by JavaScript fetch() for "real-time" counters on the home page.
require_once 'db.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
echo json_encode(marketplace_stats($conn));
