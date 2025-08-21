<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logout</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="logout-wrapper">
        <div class="logout-container">
            <h2>You have successfully logged out.</h2>
            <p>What would you like to do next?</p>
            <div class="logout-buttons">
                <a href="login.php" class="btn">🔄 Login Again</a>
            </div>
        </div>
    </div>

    <style>
        /* Background Styling */
        body {
            background: linear-gradient(135deg, #6f7bfc, #e9d2fc);
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Flex wrapper to center the content */
        .logout-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            width: 100%;
        }

        /* Logout container styling */
        .logout-container {
            background-color: rgba(0, 0, 0, 0.75);
            padding: 40px 30px;
            border-radius: 12px;
            text-align: center;
            width: 400px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            color: white;
        }

        h2 {
            margin-bottom: 15px;
            color: #ff9f00;
        }

        p {
            margin-bottom: 25px;
        }

        /* Button Styling */
        .logout-buttons {
            display: flex;
            justify-content: center;
        }

        .btn {
            background-color: #2980b9;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            color: white;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #2573a6;
        }
    </style>
</body>
</html>
