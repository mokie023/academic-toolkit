<?php
include('templates/navbar.php');
session_start();
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic validation on email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Optional: block certain domains
        $blocked_domains = ['example.com', 'spamdomain.com'];
        $email_domain = substr(strrchr($email, "@"), 1);
        if (in_array(strtolower($email_domain), $blocked_domains)) {
            $error = "Email domain is not allowed.";
        }
    }

    if (!isset($error)) {
        // Sanitize for database query
        $email_safe = mysqli_real_escape_string($conn, $email);

        // Fetch user by email
        $query = "SELECT * FROM users WHERE email = '$email_safe'";
        $result = mysqli_query($conn, $query);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            // Verify password using password_verify()
            if (password_verify($password, $user['password'])) {
                // Set session and redirect
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Invalid email or password. Please try again.";
            }
        } else {
            $error = "Invalid email or password. Please try again.";
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Student Academic Toolkit - Login</title>
<style>
    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #6f7bfc, #e9d2fc);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 70px;
    }

    nav {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 60px;
        background-color: #2e3d49;
        display: flex;
        align-items: center;
        padding: 0 30px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        z-index: 1000;
    }

    nav .brand {
        color: #4caf50;
        font-weight: 700;
        font-size: 22px;
        margin-right: 40px;
        white-space: nowrap;
    }

    nav ul {
        list-style: none;
        display: flex;
        gap: 25px;
        flex-wrap: wrap;
        align-items: center;
        margin-left: auto;
    }

    nav ul li {
        color: #ffffff;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
        transition: color 0.3s ease;
    }

    nav ul li:hover {
        color: #4caf50;
        text-decoration: underline;
    }

    nav ul li.logout {
        color: #e57373;
        font-weight: 700;
        cursor: pointer;
    }
    nav ul li.logout:hover {
        color: #ef5350;
    }

    .form-container {
        background-color: #fff;
        max-width: 420px;
        width: 100%;
        padding: 40px 35px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        text-align: center;
        transition: transform 0.3s ease;
    }
    .form-container:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.25);
    }

    .logo {
        margin-bottom: 20px;
    }
    .logo img {
        width: 60px;
        filter: drop-shadow(0 0 3px #4caf50);
    }

    h2 {
        color: #2a3a4c;
        font-weight: 700;
        font-size: 28px;
        margin-bottom: 30px;
        letter-spacing: 1.1px;
    }

    .input-group {
        margin-bottom: 22px;
        text-align: left;
    }

    .input-group label {
        display: block;
        font-size: 16px;
        color: #3b4a63;
        margin-bottom: 8px;
        font-weight: 600;
    }

    .input-group input {
        width: 100%;
        padding: 14px 18px;
        font-size: 16px;
        border-radius: 12px;
        border: 1.5px solid #cfd8dc;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }
    .input-group input:focus {
        outline: none;
        border-color: #4caf50;
        box-shadow: 0 0 8px rgba(76,175,80,0.5);
    }
    .input-group input::placeholder {
        color: #a0aec0;
    }

    button {
        width: 100%;
        padding: 16px 0;
        font-size: 18px;
        font-weight: 700;
        color: white;
        background: linear-gradient(45deg, #4caf50, #388e3c);
        border: none;
        border-radius: 12px;
        cursor: pointer;
        box-shadow: 0 5px 15px rgba(76,175,80,0.4);
        transition: background 0.4s ease, transform 0.2s ease;
    }
    button:hover {
        background: linear-gradient(45deg, #43a047, #2e7d32);
        transform: translateY(-3px);
    }
    button:active {
        transform: translateY(1px);
        box-shadow: 0 2px 8px rgba(76,175,80,0.3);
    }

    .register-link {
        margin-top: 28px;
        font-size: 15px;
        color: #455a64;
    }
    .register-link a {
        color: #4caf50;
        font-weight: 600;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    .register-link a:hover {
        color: #388e3c;
        text-decoration: underline;
    }

    .error-msg {
        background-color: #ffebee;
        border: 1px solid #f44336;
        color: #b71c1c;
        padding: 10px 15px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-weight: 600;
    }

    @media (max-width: 600px) {
        nav {
            padding: 0 15px;
            height: 55px;
        }
        nav .brand {
            font-size: 18px;
            margin-right: 20px;
        }
        nav ul {
            gap: 15px;
        }
        nav ul li {
            font-size: 14px;
        }
        .form-container {
            padding: 30px 25px;
            max-width: 95%;
        }
        h2 {
            font-size: 22px;
        }
        button {
            font-size: 16px;
            padding: 14px 0;
        }
    }
</style>
</head>
<body>

<nav>
    <div class="brand">Student Academic Toolkit</div>
    <ul>
        <li>Dashboard</li>
        <li>Notes</li>
        <li>Tasks</li>
        <li>Journal</li>
        <li class="logout">Logout</li>
    </ul>
</nav>

<div class="form-container">
    <div class="logo">
        <img src="https://img.icons8.com/ios-filled/50/4caf50/login-rounded-right.png" alt="Login Icon" />
    </div>
    <h2>Login to Your Account</h2>

    <?php if (isset($error)): ?>
        <p class="error-msg"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
        <div class="input-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required>
        </div>
        
        <div class="input-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>
        </div>
        
        <button type="submit">Login</button>
    </form>

    <div class="register-link">
        <p>Don't have an account? <a href="register.php">Create one now!</a></p>
    </div>
</div>

</body>
</html>
