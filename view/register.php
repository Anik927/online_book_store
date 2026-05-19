<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Online Book Store</title>
    <link rel="stylesheet" href="../public/css/register.css"> 
</head>
<body>
    <div class="register-container">
        <h2>Create an Account</h2>
        
        <?php if(isset($_GET['error'])): ?>
            <div class="error-msg" style="color: red; margin-bottom: 15px;">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <form id="registerForm" action="/online_book_store/control/register_control.php" method="POST">            
            <div class="form-group">
                <label for="name">Full Name:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" minlength="8" required>
                <small style="display:block; color:#666;">Minimum 8 characters</small>
            </div>

            <div class="form-group">
                <label for="address">Mailing Address:</label>
                <textarea id="address" name="address" rows="3" required></textarea>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number:</label>
                <input type="text" id="phone" name="phone" required>
            </div>

            <div class="form-group">
                <label for="role">Account Type:</label>
                <select id="role" name="role" required>
                    <option value="customer">Customer</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <button type="submit" class="btn-submit">Register Account</button>
        </form>
        
        <p style="margin-top: 15px;">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </div>

    <script src="../public/js/register.js"></script>
</body>
</html>