<?php
require_once __DIR__ . '/config.php';

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
if (session_status() === PHP_SESSION_NONE) {

    ini_set('session.use_only_cookies', '1');
    ini_set('session.use_strict_mode', '1');
    session_set_cookie_params(array(
    'lifetime' => 0,
    'path' => '/',
    'secure' => $isHttps,
    'httponly' => true,
    'samesite' => 'Lax'
    ));
    session_start();

}

/* ---------- Session lifecycle: idle timeout + periodic ID rotation ---------- */
define('SESSION_IDLE_SECONDS', 1800);   // sign out after 30 minutes of inactivity
define('SESSION_ROTATE_SECONDS', 900);  // rotate the session ID every 15 minutes
$seh_now = time();
if (!empty($_SESSION['user_id'])) {

    if (isset($_SESSION['last_activity']) && ($seh_now - (int)$_SESSION['last_activity']) > SESSION_IDLE_SECONDS) {

        $keepTheme = $_SESSION['theme'] ?? null;
        $_SESSION = array();
        session_regenerate_id(true);
        if ($keepTheme) {

            $_SESSION['theme'] = $keepTheme;
        }

        $_SESSION['flash'] = array('type' => 'error', 'message' => 'You were signed out after 30 minutes of inactivity. Please sign in again.');

    }
    elseif (isset($_SESSION['session_rotated']) && ($seh_now - (int)$_SESSION['session_rotated']) > SESSION_ROTATE_SECONDS) {

        session_regenerate_id(true);
        $_SESSION['session_rotated'] = $seh_now;

    }

}

if (!empty($_SESSION['user_id'])) {

    $_SESSION['last_activity'] = $seh_now;
}

mysqli_report(MYSQLI_REPORT_OFF);
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
if (!$conn) {

    http_response_code(500);
    if (APP_ENV === 'development') {

        exit('Database connection failed. Check config.php and import setup.sql. Details: ' . htmlspecialchars(mysqli_connect_error(), ENT_QUOTES, 'UTF-8'));

    }

    exit('The service is temporarily unavailable. Please try again later.');

}

mysqli_set_charset($conn, 'utf8mb4');

function h($value) {

    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

}

function redirect($url) {

    header('Location: ' . $url);
    exit;

}

function is_logged_in() {

    return isset($_SESSION['user_id']) && (int)$_SESSION['user_id'] > 0;

}

function require_login() {

    if (!is_logged_in()) {

        $_SESSION['flash'] = array('type' => 'error', 'message' => 'Please sign in to continue.');
        redirect('login.php');

    }

}

function csrf_token() {

    if (empty($_SESSION['csrf_token'])) {

        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

    }

    return $_SESSION['csrf_token'];

}

function verify_csrf() {

    $sent = isset($_POST['csrf_token']) ? (string)$_POST['csrf_token'] : '';
    if ($sent === '' || empty($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $sent)) {

        http_response_code(403);
        exit('Request could not be verified. Refresh the page and try again.');

    }

}

function set_flash($type, $message) {

    $_SESSION['flash'] = array('type' => $type, 'message' => $message);

}

function show_flash() {

    if (!empty($_SESSION['flash'])) {

        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        echo '<div class="notice ' . h($flash['type']) . '" role="status">' . h($flash['message']) . '</div>';

    }

}

function valid_category($category) {

    return in_array($category, array('Books', 'Electronics', 'Furniture', 'Accessories', 'Other'), true);

}

function valid_item_type($type) {

    return in_array($type, array('Sell', 'Exchange', 'Free'), true);

}

function money_label($price, $type) {

    return $type === 'Free' ? 'Free' : '₹' . number_format((float)$price, 2);

}

function current_user_id() {

    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

}

/* ---------- Cookies & preferences ---------- */
function allowed_themes() {

    return array('ocean', 'dark');
}

function consent_raw() {

    $c = $_COOKIE['seh_consent'] ?? ($_SESSION['consent'] ?? '');
    return is_string($c) ? $c : '';

}

function consent_chosen() {

    return consent_raw() !== '';
}

function consent_categories() {

    // Stored value: "all", "essential" or a custom list such as "pref,stats". Essential cookies are always on.
    $raw = consent_raw();
    if ($raw === 'all') return array('pref', 'stats');
    if ($raw === '' || $raw === 'essential') return array();
    return array_values(array_intersect(explode(',', $raw), array('pref', 'stats')));

}

function consent_allows($category) {

    return in_array($category, consent_categories(), true);
}

function consent_level() {

    return consent_chosen() ? (consent_raw()) : '';
}
// kept for older code
function functional_cookies_allowed() {

    return consent_allows('pref');
}

function seh_set_cookie($name, $value, $days = 365) {

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    setcookie($name, (string)$value, array(
    'expires' => $days > 0 ? time() + 86400 * $days : time() - 3600,
    'path' => '/', 'secure' => $isHttps, 'httponly' => false, 'samesite' => 'Lax'
    ));
    if ($days > 0) {

        $_COOKIE[$name] = (string)$value;
    }
    else {

        unset($_COOKIE[$name]);
    }

}

function current_theme() {

    $t = $_SESSION['theme'] ?? ($_COOKIE['seh_theme'] ?? 'ocean');
    return in_array($t, allowed_themes(), true) ? $t : 'ocean';

}

function remember_recent_product($id, $title, $category) {

    // Session-based "recently viewed" list (newest first, max 6)
    $list = isset($_SESSION['recent']) && is_array($_SESSION['recent']) ? $_SESSION['recent'] : array();
    unset($list[$id]);
    $list = array($id => array('title' => $title, 'category' => $category)) + $list;
    $_SESSION['recent'] = array_slice($list, 0, 6, true);

}

/* ---------- Stored procedure helpers (MySQLi) ---------- */
function flush_results($conn) {

    while (mysqli_more_results($conn) && mysqli_next_result($conn)) {

        $extra = mysqli_use_result($conn);
        if ($extra instanceof mysqli_result) {

            mysqli_free_result($extra);
        }

    }

}

function marketplace_stats($conn) {

    $stats = array('items' => 0, 'categories' => 0, 'students' => 0);
    // 1) Try the stored procedure
    $res = mysqli_query($conn, 'CALL sp_marketplace_stats()');
    if ($res instanceof mysqli_result) {

        $row = mysqli_fetch_assoc($res); mysqli_free_result($res); flush_results($conn);
        if ($row) {

            return array('items' => (int)$row['items'], 'categories' => (int)$row['categories'], 'students' => (int)$row['students']);
        }

    }

    flush_results($conn);
    // 2) Fallback to plain queries if the procedure has not been imported yet
    $r = mysqli_query($conn, "SELECT COUNT(*) AS total, COUNT(DISTINCT category) AS categories FROM products WHERE status='Available'");
    if ($r) {

        $row = mysqli_fetch_assoc($r); $stats['items'] = (int)$row['total']; $stats['categories'] = (int)$row['categories'];
    }

    $r = mysqli_query($conn, 'SELECT COUNT(*) AS total FROM users');
    if ($r) {

        $stats['students'] = (int)mysqli_fetch_assoc($r)['total'];
    }

    return $stats;

}

function search_titles($conn, $term) {

    $rows = array();
    $stmt = mysqli_prepare($conn, 'CALL sp_search_titles(?)');   // prepared statement + stored procedure
    if ($stmt) {

        mysqli_stmt_bind_param($stmt, 's', $term);
        if (mysqli_stmt_execute($stmt)) {

            $res = mysqli_stmt_get_result($stmt);
            if ($res) {

                while ($r = mysqli_fetch_assoc($res)) {

                    $rows[] = $r;
                }

            }

        }

        mysqli_stmt_close($stmt);
        flush_results($conn);
        return $rows;

    }

    // Fallback (procedure missing)
    $like = '%' . $term . '%';
    $stmt = mysqli_prepare($conn, "SELECT id,title,category,item_type FROM products WHERE status='Available' AND title LIKE ? ORDER BY created_at DESC LIMIT 6");
    mysqli_stmt_bind_param($stmt, 's', $like); mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    while ($r = mysqli_fetch_assoc($res)) {

        $rows[] = $r;
    }

    mysqli_stmt_close($stmt);
    return $rows;

}
