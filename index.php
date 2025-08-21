<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Academic Student Toolkit</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/landing.css">
    <style>
        /* ====== General Styling ====== */
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            overflow-x: hidden;
            font-family: 'Arial', sans-serif;
        }

        /* ====== Hero Section Styling ====== */
        .hero-section {
            height: 100vh;
            background-image: url('assets/background.jpg');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            position: relative;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.6);
            padding: 60px;
            border-radius: 10px;
        }

        .cta a {
            margin: 10px;
        }

        /* ====== Features Section ====== */
        #features {
            margin-top: 50px;
        }

        #features .card {
            border: none;
            transition: transform 0.3s ease;
        }

        #features .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        #features .fa-3x {
            margin-bottom: 15px;
        }

        /* ====== Contact Section ====== */
        #contact {
            background-color: #343a40;
            color: white;
        }

        #contact a {
            color: #4CAF50;
            text-decoration: none;
        }

        #contact a:hover {
            color: #66bb6a;
        }

        /* ====== Footer Styling ====== */
        footer {
            background-color: #2c3e50;
            color: white;
        }
    </style>
</head>
<body>

<!-- ====== Navbar ====== -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">
        <a class="navbar-brand" href="index.php">Academic Toolkit</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- ====== Hero Section ====== -->
<section class="hero-section">
    <div class="overlay">
        <h1>Welcome to Academic Student Toolkit</h1>
        <p>The ultimate platform to organize your academic life efficiently.</p>
        <div class="cta">
            <a href="login.php" class="btn btn-primary btn-lg">Login</a>
            <a href="register.php" class="btn btn-success btn-lg">Register</a>
        </div>
    </div>
</section>

<!-- ====== Features Section ====== -->
<section id="features" class="container my-5">
    <h2 class="text-center mb-5">Key Features</h2>
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <i class="fas fa-book fa-3x text-primary"></i>
                <h4 class="mt-3">Class Notes</h4>
                <p>Upload and manage class notes, PDF documents, and Word files.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <i class="fas fa-tasks fa-3x text-success"></i>
                <h4 class="mt-3">Task Management</h4>
                <p>Organize your tasks, assignments, and deadlines efficiently.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-4 text-center">
                <i class="fas fa-calendar-alt fa-3x text-danger"></i>
                <h4 class="mt-3">Academic Planner</h4>
                <p>Schedule your academic tasks and never miss a deadline.</p>
            </div>
        </div>
    </div>
</section>

<!-- ====== Contact Section ====== -->
<section id="contact" class="text-center py-5">
    <h2>Contact Us</h2>
    <p>Email: <a href="mailto:support@academictoolkit.com">support@academictoolkit.com</a></p>
    <p>Phone: +263 77 696 3900</p>
    <p>Address: 4255 Spitzkop Medium, Gwanda, Zimbabwe</p>
</section>

<!-- ====== Footer ====== -->
<footer class="text-center py-3">
    <p>&copy; 2025 Academic Toolkit. All rights reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>  