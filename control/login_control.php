<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/../model/login_model.php';


// ============================================
// AUTO-LOGIN FROM COOKIE (called from login.php)
// ============================================
function autoLoginFromCookie($conn) {
    if (isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
        $user = getUserByToken($conn, $_COOKIE['remember_token']);

        if ($user) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];

            $redirect = $user['role'] === 'admin'
            ? '/online_book_store/control/admin_control.php'  // ← goes through controller
             : '/online_book_store/view/home_page/customer_homepage.html';

            header('Location: ' . $redirect);            
            exit;
        }
    }
}


// ── Only run routing logic when called via AJAX (POST) ───
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    return; // just return, don't exit — lets login.php continue
}


header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'login') {
    handleLogin($conn);
} elseif ($action === 'logout') {
    handleLogout($conn);
} else {
    jsonError('Invalid request.');
}


// ============================================
// LOGIN
// ============================================
function handleLogin($conn) {
    
    $email      = trim($_POST['email']    ?? '');
    $password   = trim($_POST['password'] ?? '');
    $rememberMe = ($_POST['remember_me']  ?? '0') === '1';
    $_SESSION['logged_at'] = date("Y-m-d H:i:s");
    
    
    if ($email === '' || $password === '') {
        jsonError('Please fill in all fields.');
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonError('Invalid email address.');
    }

    if (strlen($password) < 8) {
        jsonError('Password must be at least 8 characters.');
    }

    $user = getUserByEmail($conn, $email);

    if (!$user || !password_verify($password, $user['password'])) {
        jsonError('Invalid email or password.');
        }

    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name']    = $user['name'];
    $_SESSION['role']    = $user['role'];

    
    if ($rememberMe) {
        $token = bin2hex(random_bytes(32));
        updateRememberToken($conn, $user['id'], $token);
        setcookie('remember_token', $token, [
            'expires'  => time() + (7 * 24 * 60 * 60),
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            ]);    

        }

    
   $redirect = $user['role'] === 'admin'
    ? '/online_book_store/control/admin_control.php'
    : '/online_book_store/view/home_page/customer_homepage.html';   

    jsonSuccess(['redirect' => $redirect]);
}


// ============================================
// LOGOUT
// ============================================
function handleLogout($conn) {

    if (isset($_SESSION['user_id'])) {
        clearRememberToken($conn, $_SESSION['user_id']);
    }

    session_unset();
    session_destroy();

    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
        ]);        

    }

    jsonSuccess(['redirect' => '/online_book_store/view/login.php']);
}


// ============================================
// HELPERS
// ============================================
function jsonSuccess($data = []) {
    echo json_encode(array_merge(['success' => true], $data));
    exit;
}

function jsonError($message) {
    echo json_encode(['success' => false, 'message' => $message]);
    exit;
}