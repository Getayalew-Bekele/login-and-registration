<?php
// login.php - Login page with authentication logic
session_start();
require_once 'database.php';

$error_message = '';

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = "Please fill in all fields";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password, first_name, last_name FROM information WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['first_name'] . ' ' . $user['last_name'];
                
                echo "Successful Login!";
            } else {
                $error_message = "Invalid username or password";
            }
        } catch(PDOException $e) {
            $error_message = "Login error. Please try again.";
        }
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['newaccount'])) {
    header("location: registration.php");
}
if (isset($_SESSION['user_id'])) {
    $success_message = "You are already logged in as " . $_SESSION['username'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - User Authentication System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="form-card">
            <h1 class="form-title">Login Form</h1>
            
            <?php if ($error_message): ?>
                <div class="message-area error"><?php echo htmlspecialchars($error_message); ?></div>
            <?php endif; ?>
            
            <form id="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="input-group">
                    <label for="loginUsername">Username:</label>
                    <input type="text" id="loginUsername" name="username" autocomplete="off" required>
                </div>
                <div class="input-group">
                    <label for="loginPassword">Password:</label>
                    <input type="password" id="loginPassword" name="password" autocomplete="new-password" required>
                </div>
                <div class="button-group">
                    <button type="submit" name="login" class="btn btn-primary">Login</button>
                    <button type="reset" class="btn btn-secondary" onclick="this.form.reset(); return false;">Clear</button>
                </div>
                <div class="register-link">
                    <a><button type="submit" name="newaccount">Create New Account</button></a>
                </div>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>
