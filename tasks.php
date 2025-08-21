<?php
// tasks.php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tasks - Student Academic Toolkit</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Roboto', sans-serif;
      margin: 0;
      padding: 0;
      background: #f4f6f9;
      color: #333;
    }

    /* Navbar */
    nav {
      background: #2c3e50;
      padding: 15px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    nav h1 {
      color: #fff;
      font-size: 22px;
      margin: 0;
    }
    nav ul {
      list-style: none;
      display: flex;
      margin: 0;
      padding: 0;
    }
    nav ul li {
      margin-left: 20px;
    }
    nav ul li a {
      color: #ecf0f1;
      text-decoration: none;
      font-weight: 500;
      transition: color 0.3s;
    }
    nav ul li a:hover {
      color: #1abc9c;
    }

    /* Container */
    .container {
      max-width: 1100px;
      margin: 30px auto;
      padding: 20px;
    }

    /* Section Cards */
    .card {
      background: #fff;
      border-radius: 10px;
      padding: 20px;
      margin-bottom: 25px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .card h2 {
      margin-top: 0;
      font-size: 20px;
      color: #2c3e50;
      border-bottom: 2px solid #1abc9c;
      padding-bottom: 8px;
    }

    /* Task Manager */
    .task-form input, .task-form button {
      padding: 10px;
      margin: 5px 0;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    .task-form input[type="text"], 
    .task-form input[type="datetime-local"] {
      width: calc(100% - 20px);
    }
    .task-form button {
      background: #1abc9c;
      color: #fff;
      border: none;
      cursor: pointer;
      transition: 0.3s;
    }
    .task-form button:hover {
      background: #16a085;
    }

    .task-list {
      margin-top: 15px;
    }
    .task-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #ecf0f1;
      padding: 10px;
      border-radius: 6px;
      margin-bottom: 8px;
    }
    .task-item span {
      font-weight: 500;
    }
    .task-item .actions i {
      cursor: pointer;
      margin-left: 10px;
    }
    .task-item .actions i:hover {
      color: #e74c3c;
    }

    /* Pomodoro */
    .pomodoro {
      text-align: center;
    }
    .pomodoro h2 {
      margin-bottom: 10px;
    }
    .timer {
      font-size: 40px;
      font-weight: bold;
      margin: 15px 0;
    }
    .pomodoro button {
      margin: 5px;
      padding: 10px 15px;
      border: none;
      border-radius: 6px;
      background: #1abc9c;
      color: #fff;
      cursor: pointer;
      transition: 0.3s;
    }
    .pomodoro button:hover {
      background: #16a085;
    }

    /* Planner */
    .planner {
      text-align: center;
      font-style: italic;
      color: #7f8c8d;
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav>
    <h1>Student Academic Toolkit</h1>
    <ul>
      <li><a href="dashboard.php">Dashboard</a></li>
      <li><a href="notes.php">Notes</a></li>
      <li><a href="tasks.php" style="color:#1abc9c;">Tasks</a></li>
      <li><a href="journal.php">Journal</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>

  <!-- Main Content -->
  <div class="container">

    <!-- Task Manager -->
    <div class="card">
      <h2>Task Manager</h2>
      <form class="task-form">
        <input type="text" placeholder="Enter task title" required>
        <input type="datetime-local" required>
        <button type="submit"><i class="fas fa-plus"></i> Add Task</button>
      </form>
      <div class="task-list">
        <p>No tasks found. Fill the form above to add one.</p>
      </div>
    </div>

    <!-- Pomodoro -->
    <div class="card pomodoro">
      <h2>Pomodoro Timer</h2>
      <div class="timer">25:00</div>
      <button><i class="fas fa-play"></i></button>
      <button><i class="fas fa-pause"></i></button>
      <button><i class="fas fa-redo"></i></button>
    </div>

    <!-- Study Planner -->
    <div class="card planner">
      <h2>Study Planner</h2>
      <p>📅 Coming Soon!</p>
    </div>

  </div>

</body>
</html>
