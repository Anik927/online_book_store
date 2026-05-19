<?php
session_start();
require_once __DIR__ . '../../control/login_control.php';
autoLoginFromCookie($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — BookStore</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/login.css">
</head>
<body>

<div class="page-wrap">

    <!-- Left decorative panel -->
    <div class="side-panel">
        <div class="side-inner">
            <div class="logo-mark">📚</div>
            <h1 class="brand">BookStore</h1>
            <p class="tagline">Your next great read<br>is one login away.</p>
        </div>
    </div>

    <!-- Right form panel -->
    <div class="form-panel">
        <div class="form-box">

            <h2>Welcome back</h2>
            <p class="sub">Sign in to your account</p>



            <!-- AJAX error banner -->
            <div class="alert-error" id="alertError" style="display:none;"></div>

            <form id="loginForm" novalidate>

                <div class="field" id="fieldEmail">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                           placeholder="you@example.com" autocomplete="email">
                    <span class="field-error" id="emailError"></span>
                </div>

                <div class="field" id="fieldPassword">
                    <label for="password">Password</label>
                    <div class="password-wrap">
                        <input type="password" id="password" name="password"
                               placeholder="Min. 8 characters" autocomplete="current-password">
                        <button type="button" class="eye-btn" id="togglePass" aria-label="Show password">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                 viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                    <span class="field-error" id="passwordError"></span>
                </div>

                <div class="remember-row">
                    <label class="check-label">
                        <input type="checkbox" name="remember_me" id="rememberMe">
                        <span class="check-box"></span>
                        Remember me for 7 days
                    </label>
                </div>

                <button type="submit" class="btn-login" id="submitBtn">
                    <span id="btnText">Sign In</span>
                    <span id="btnSpinner" class="spinner" style="display:none;"></span>
                </button>

                <div class="divider">
                    <div class="divider-line"></div>
                    <span class="divider-text">no account?</span>
                    <div class="divider-line"></div>
                </div>

                <p class="register-link">
                    <a href="/online_book_store/view/register.php">Create one</a>
                </p>

            </form>
        </div>
    </div>

</div>

<script src="../public/js/login.js"></script>
</body>
</html>