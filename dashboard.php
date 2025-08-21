<?php
include('templates/navbar.php');
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch tasks for user
$stmt = $conn->prepare("SELECT * FROM tasks WHERE user_id = ? ORDER BY due_date ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$tasks_result = $stmt->get_result();
$stmt->close();

// Fetch notes with files
$notes_query = $conn->prepare("SELECT * FROM notes WHERE user_id = ? AND file_path IS NOT NULL");
$notes_query->bind_param("i", $user_id);
$notes_query->execute();
$notes_result = $notes_query->get_result();
$notes_query->close();

// Fetch journals count
$journal_count = $conn->query("SELECT COUNT(*) FROM journal WHERE user_id = $user_id")->fetch_row()[0];

// Stats data
$stats = [
    ['icon' => 'fas fa-file-alt', 'title' => 'Notes', 'count' => $conn->query("SELECT COUNT(*) FROM notes WHERE user_id = $user_id")->fetch_row()[0], 'link' => 'notes.php', 'emoji' => '📄'],
    ['icon' => 'fas fa-tasks', 'title' => 'Tasks', 'count' => $conn->query("SELECT COUNT(*) FROM tasks WHERE user_id = $user_id")->fetch_row()[0], 'link' => 'tasks.php', 'emoji' => '📝'],
    ['icon' => 'fas fa-book-open', 'title' => 'Journal', 'count' => $journal_count, 'link' => 'journal.php', 'emoji' => '📘'],
];

// Fun/Interesting info snippets to show when no tasks or journals
$funFacts = [
    "Did you know? The average person learns better with organized notes. Keep yours updated!",
    "Tip: Break your tasks into smaller steps — it makes progress more motivating.",
    "Motivation: \"Success is the sum of small efforts repeated day in and day out.\" — Robert Collier",
    "Fun Fact: Writing journals can improve memory and comprehension by 20%.",
    "Quote: \"The secret of getting ahead is getting started.\" — Mark Twain",
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard | Academic Toolkit</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(135deg, #6f7bfc, #e9d2fc);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      min-height: 100vh;
      color: #2c2c54;
    }

    nav {
      background: #3b3f5c;
      color: white;
      padding: 1rem 2rem;
      text-align: center;
      font-size: 1.2rem;
      font-weight: bold;
      box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .container {
      width: 95%;
      max-width: 1200px;
      margin: 2rem auto;
    }

    h1 {
      text-align: center;
      margin-bottom: 2rem;
      font-size: 2.5rem;
      letter-spacing: 2px;
    }

    .dashboard-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
      gap: 1.5rem;
      margin-bottom: 2rem;
    }

    .card {
      background: rgba(255, 255, 255, 0.2);
      border-radius: 1.2rem;
      backdrop-filter: blur(10px);
      -webkit-backdrop-filter: blur(10px);
      padding: 2rem 1.5rem;
      text-align: center;
      color: #2c2c54;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
      transition: transform 0.3s ease, box-shadow 0.3s ease;
      cursor: pointer;
      position: relative;
      overflow: hidden;
    }

    .card:hover {
      transform: translateY(-8px);
      box-shadow: 0 14px 32px rgba(0, 0, 0, 0.2);
    }

    .card i {
      font-size: 2.8rem;
      margin-bottom: 1rem;
      animation: floatIcon 4s ease-in-out infinite;
    }

    .notes-icon { color: #3f51b5; }
    .tasks-icon { color: #2e7d32; }
    .journals-icon { color: #fbc02d; }

    .card h3 {
      font-size: 1.4rem;
      margin-bottom: 0.5rem;
      font-weight: 600;
      letter-spacing: 1px;
    }

    .card p {
      font-size: 0.95rem;
      line-height: 1.5;
      color: #333;
    }

    .info-section {
      background: white;
      padding: 2rem;
      border-radius: 1.2rem;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .info-section h2 {
      font-size: 1.8rem;
      margin-bottom: 1rem;
      color: #3b3f5c;
    }

    .info-section p {
      font-size: 1rem;
      line-height: 1.6;
      max-width: 800px;
      margin: 0 auto;
      color: #555;
    }

    @keyframes floatIcon {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-6px); }
    }

    @media (max-width: 768px) {
      h1 { font-size: 2rem; }
      .card { padding: 1.5rem; }
      .card h3 { font-size: 1.2rem; }
      nav { font-size: 1rem; padding: 1rem; }
    }
  </style>
</head>
<body>

  <!-- Main Navbar -->
  <nav>
    Academic Toolkit Dashboard
  </nav>

  <div class="container">
    <h1>Welcome to Your Academic Toolkit</h1>

    <div class="dashboard-grid">

      <!-- Notes -->
      <a href="notes.php" class="card text-decoration-none">
        <i class="fas fa-file-lines notes-icon"></i>
        <h3>View Notes</h3>
        <p>Access and organize your notes with ease.</p>
      </a>

      <!-- Tasks -->
      <a href="tasks.php" class="card text-decoration-none">
        <i class="fas fa-check-square tasks-icon"></i>
        <h3>View Tasks</h3>
        <p>Stay ahead of all your assignments and upcoming deadlines.</p>
      </a>

      <!-- Journals -->
      <a href="journal.php" class="card text-decoration-none">
        <i class="fas fa-book-journal-whills journals-icon"></i>
        <h3>View Journals</h3>
        <p>Document your reflections and academic journey.</p>
      </a>

    </div>

    <!-- Additional Info Section -->
    <div class="info-section">
      <h2>Your Central Hub for Academic Success</h2>
      <p>
        This toolkit is designed to empower students to manage their learning materials efficiently.
        Upload class notes, track progress on your assignments, and reflect on your academic experiences.
        With tools like smart search, journal logs, and task alerts, your academic organization just got smarter and simpler.
      </p>
    </div>
  </div>

</body>
</html>
