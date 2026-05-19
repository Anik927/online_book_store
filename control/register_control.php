<?php
// control/register_control.php
session_start();
require_once '../model/register_model.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize raw POST data 
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    $role = $_POST['role'];

    // 1. Server-side Validation: Check Password Length Requirement (>= 8 chars)
    if (strlen($password) < 8) {
        header("Location: ../view/register.php?error=Password must be at least 8 characters long.");
        exit();
    }

    // 2. Server-side Validation: Enforce Unique Email Constraint
    if (emailExists($email)) {
        header("Location: ../view/register.php?error=This email address is already registered.");
        exit();
    }

    // 3. Cryptographic Security: Securely hash raw text password using standard algorithm
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 4. Persistence: Commit data structure to the model layer
    $isRegistered = registerUser($name, $email, $hashed_password, $role, $address, $phone);

    if ($isRegistered) {
        // Redirect successfully to login page view upon insertion
        header("Location: ../view/login.php?success=Registration complete! Please sign in.");
        exit();
    } else {
        header("Location: ../view/register.php?error=Critical registration failure. Try again.");
        exit();
    }
} else {
    // Block outside execution paths directly targeting this file
    header("Location: ../view/register.php");
    exit();
}
?>