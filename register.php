<?php
session_start();
include('templates/navbar.php');
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Trim and fetch POST data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate username: start with letter, 3-20 chars, letters/numbers/._ only
    if (!preg_match('/^[a-zA-Z][a-zA-Z0-9._]{2,19}$/', $username)) {
        $error = "Username must start with a letter and be 3-20 characters long. Allowed: letters, numbers, dots, underscores.";
    }
    // Validate email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    }
    // Validate password length
    elseif (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long.";
    }
    else {
        // Escape for SQL queries
        $username_safe = mysqli_real_escape_string($conn, $username);
        $email_safe = mysqli_real_escape_string($conn, $email);

        // Check if username or email already exists
        $checkQuery = "SELECT id FROM users WHERE username = '$username_safe' OR email = '$email_safe' LIMIT 1";
        $result = mysqli_query($conn, $checkQuery);

        if (!$result) {
            $error = "Database error: " . mysqli_error($conn);
        }
        elseif (mysqli_num_rows($result) > 0) {
            $error = "Username or email already registered. Please try logging in or use different credentials.";
        }
        else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Insert new user
            $insertQuery = "INSERT INTO users (username, email, password) VALUES ('$username_safe', '$email_safe', '$hashedPassword')";
            if (mysqli_query($conn, $insertQuery)) {
                $success = "Registration successful! Please <a href='login.php'>Login here</a>.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<div class="form-container">
    <h2>Create an Account</h2>

    <?php if ($error): ?>
        <p class="error-msg"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success-msg"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="input-group">
            <label for="username">Username</label>
            <input
              type="text"
              name="username"
              id="username"
              placeholder="Enter your username"
              required
              pattern="^[a-zA-Z][a-zA-Z0-9._]{2,19}$"
              title="Start with letter, 3-20 chars, letters, numbers, dots, underscores only"
              value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>"
            >
        </div>

        <div class="input-group">
            <label for="email">Email</label>
            <input
              type="email"
              name="email"
              id="email"
              placeholder="Enter your email"
              required
              value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>"
            >
        </div>

        <div class="input-group">
            <label for="password">Password</label>
            <input
              type="password"
              name="password"
              id="password"
              placeholder="Enter your password"
              required
              minlength="8"
            >
        </div>

        <button type="submit">Register</button>
    </form>

    <div class="login-link">
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</div>


<style>
    /* General Styles */
    body {
        background: linear-gradient(135deg, #6f7bfc, #e9d2fc);
        background-size: cover;
        background-position: center;
        font-family: Arial, sans-serif;
    }

    .form-container {
        max-width: 500px;
        margin: 50px auto;
        padding: 30px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    h2 {
        text-align: center;
        color: #2e3d49;
        font-size: 28px;
        margin-bottom: 20px;
    }

    .input-group {
        margin-bottom: 15px;
    }

    .input-group label {
        display: block;
        font-size: 16px;
        color: #2e3d49;
        margin-bottom: 5px;
    }

    .input-group input {
        width: 100%;
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ddd;
        border-radius: 8px;
        transition: border-color 0.3s;
    }

    .input-group input:focus {
        border-color: #4CAF50;
    }

    button {
        width: 100%;
        padding: 14px;
        font-size: 18px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #45a049;
    }

    .login-link {
        text-align: center;
        margin-top: 15px;
    }

    .login-link a {
        color: #4CAF50;
        text-decoration: none;
        font-weight: bold;
    }

    .login-link a:hover {
        text-decoration: underline;
    }

    .error-msg {
        color: #ff4d4d;
        text-align: center;
        background-color: #ffe6e6;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
    }

    .success-msg {
        color: #4CAF50;
        text-align: center;
        background-color: #e6ffee;
        padding: 10px;
        border-radius: 5px;
        margin-bottom: 15px;
    }

    /* Mobile Responsive */
    @media (max-width: 600px) {
        .form-container {
            margin: 20px auto;
            padding: 20px;
        }

        button {
            padding: 12px;
        }
    }
</style>
