<?php
// register.php - Registration page with database insertion logic
require_once 'database.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$success_message = '';
$error_message = '';

// Handle registration form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    // Sanitize and validate inputs
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $department = trim($_POST['department'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $hobbies = isset($_POST['hobbies']) ? implode(', ', $_POST['hobbies']) : '';
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validation
    $errors = [];
    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";
    if (empty($department)) $errors[] = "Department is required";
    if (empty($gender)) $errors[] = "Gender is required";
    if (empty($username)) $errors[] = "Username is required";
    if (empty($password)) $errors[] = "Password is required";
    if (strlen($password) < 4) $errors[] = "Password must be at least 4 characters";
    
    // Check if username already exists
    if (empty($errors)) {
        try {
            $check_stmt = $pdo->prepare("SELECT id FROM information WHERE username = ?");
            $check_stmt->execute([$username]);
            if ($check_stmt->fetch()) {
                $errors[] = "Username already exists. Please choose another.";
            }
        } catch(PDOException $e) {
            $errors[] = "Database error. Please try again.";
        }
    }
    
    // If no errors, insert user using prepared statement
    if (empty($errors)) {
        try {
            // Hash password for security
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            // Use prepared statement to prevent SQL injection
            $sql = "INSERT INTO information (first_name, last_name, department, gender, hobbies, username, password) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $pdo->prepare($sql);
            $result = $stmt->execute([$first_name, $last_name, $department, $gender, $hobbies, $username, $hashed_password]);
            
            if ($result) {
                $success_message = "Registration successful! You can now login.";
                // Clear form via JavaScript (handled in JS)
            } else {
                $error_message = "Registration failed. Please try again.";
            }
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry error
                $error_message = "Username already exists. Please choose another.";
            } else {
                $error_message = "Database error: " . $e->getMessage();
            }
        }
    } else {
        $error_message = implode("<br>", $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - User Authentication System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="form-card">
            <h1 class="form-title">Registration Form</h1>
            
            <?php if ($success_message): ?>
                <div class="message-area success"><?php echo htmlspecialchars($success_message); ?></div>
                <script>
                    setTimeout(function() {
                        window.location.href = 'login.php';
                    }, 2000);
                </script>
            <?php endif; ?>
            
            <?php if ($error_message): ?>
                <div class="message-area error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form id="registerForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <div class="form-row">
                    <div class="input-group half">
                        <label for="firstName">First Name <span class="required">*</span></label>
                        <input type="text" id="firstName" name="first_name" value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                    </div>
                    <div class="input-group half">
                        <label for="lastName">Last Name <span class="required">*</span></label>
                        <input type="text" id="lastName" name="last_name" value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="department">Department <span class="required">*</span></label>
                    <select id="department" name="department" required>
                        <option value="">Select Department</option>
                        <option value="Computer Science" <?php echo (isset($_POST['department']) && $_POST['department'] == 'Computer Science') ? 'selected' : ''; ?>>Computer Science</option>
                        <option value="Information Technology" <?php echo (isset($_POST['department']) && $_POST['department'] == 'Information Technology') ? 'selected' : ''; ?>>Information Technology</option>
                        <option value="Software Engineering" <?php echo (isset($_POST['department']) && $_POST['department'] == 'Software Engineering') ? 'selected' : ''; ?>>Software Engineering</option>
                        <option value="Data Science" <?php echo (isset($_POST['department']) && $_POST['department'] == 'Data Science') ? 'selected' : ''; ?>>Data Science</option>
                    </select>
                </div>
                
                <div class="input-group">
                    <label>Gender <span class="required">*</span></label>
                    <div class="radio-group">
                        <label><input type="radio" name="gender" value="Male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Male') ? 'checked' : ''; ?> required> Male</label>
                        <label><input type="radio" name="gender" value="Female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Female') ? 'checked' : ''; ?>> Female</label>
                        <label><input type="radio" name="gender" value="Other" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'Other') ? 'checked' : ''; ?>> Other</label>
                    </div>
                </div>
                
                <div class="input-group">
                    <label>Hobbies</label>
                    <div class="checkbox-group">
                        <?php 
                        $selected_hobbies = isset($_POST['hobbies']) ? $_POST['hobbies'] : [];
                        ?>
                        <label><input type="checkbox" name="hobbies[]" value="Reading" <?php echo (in_array('Reading', $selected_hobbies)) ? 'checked' : ''; ?>> Reading</label>
                        <label><input type="checkbox" name="hobbies[]" value="Sports" <?php echo (in_array('Sports', $selected_hobbies)) ? 'checked' : ''; ?>> Sports</label>
                        <label><input type="checkbox" name="hobbies[]" value="Music" <?php echo (in_array('Music', $selected_hobbies)) ? 'checked' : ''; ?>> Music</label>
                        <label><input type="checkbox" name="hobbies[]" value="Travel" <?php echo (in_array('Travel', $selected_hobbies)) ? 'checked' : ''; ?>> Travel</label>
                    </div>
                </div>
                
                <div class="input-group">
                    <label for="regUsername">Username <span class="required">*</span></label>
                    <input type="text" id="regUsername" name="username" value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" required>
                </div>
                
                <div class="input-group">
                    <label for="regPassword">Password <span class="required">*</span></label>
                    <input type="password" id="regPassword" name="password" required>
                    <small>Password must be at least 4 characters</small>
                </div>
                
                <div class="button-group">
                    <button type="submit" name="register" class="btn btn-primary">Register</button>
                    <button type="reset" class="btn btn-secondary" onclick="this.form.reset(); return false;">Clear</button>
                </div>
                
                <div class="register-link">
                    <a href="login.php">← Back to Login</a>
                </div>
            </form>
        </div>
    </div>
    <script src="script.js"></script>
</body>
</html>