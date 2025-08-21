<?php
include('templates/navbar.php');
session_start();
require_once 'config.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Search functionality (sanitizing input)
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';

// Using prepared statements for search functionality (more secure)
$query = "SELECT * FROM journals WHERE user_id = ? AND (title LIKE ? OR tags LIKE ?) ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $query);

// Bind parameters and execute the query
$search_param = "%$search%";
mysqli_stmt_bind_param($stmt, 'iss', $user_id, $search_param, $search_param);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Handle form submission for adding a new journal entry
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);

    // Insert new journal entry into the database
    $insert_query = "INSERT INTO journals (user_id, title, description, created_at) VALUES (?, ?, ?, NOW())";
    $insert_stmt = mysqli_prepare($conn, $insert_query);
    mysqli_stmt_bind_param($insert_stmt, 'iss', $user_id, $title, $description);

    if (mysqli_stmt_execute($insert_stmt)) {
        echo "<p>New journal entry added successfully!</p>";
    } else {
        echo "<p>Error adding journal entry.</p>";
    }
}
?>

<!-- Journal Entries Container -->
<div class="journal-container">
    <h1>Your Journal Entries</h1>

    <!-- Add Journal Entry Form -->
    <h2>Add New Journal Entry</h2>
    <form method="POST" action="journal.php" class="add-journal-form">
        <input type="text" name="title" placeholder="Journal Title" required />
        <textarea name="description" placeholder="Journal Description" required></textarea>
        <button type="submit" name="submit">Add Journal Entry</button>
    </form>

    <!-- Search functionality -->
    <form method="GET" action="journal.php" class="search-form">
        <input type="text" name="search" placeholder="Search by title or tags..." value="<?= htmlspecialchars($search) ?>" />
        <button type="submit">Search</button>
    </form>

    <!-- Journal List -->
    <?php if (mysqli_num_rows($result) > 0): ?>
        <ul class="journal-list">
            <?php while ($journal = mysqli_fetch_assoc($result)): ?>
                <li>
                    <a href="view_journal.php?id=<?= $journal['id'] ?>" class="journal-title"><?= htmlspecialchars($journal['title'] ?? 'No Title') ?></a> - 
                    <span class="journal-date"><?= date('d M Y', strtotime($journal['created_at'])) ?></span>
                    <p class="journal-description"><?= nl2br(htmlspecialchars($journal['description'] ?? 'No description available')) ?></p>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No journal entries found. Add a new journal entry above.</p>
    <?php endif; ?>
</div>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Journals | Academic Toolkit</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    /* ====== GLOBAL STYLES (same as dashboard) ====== */
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
      max-width: 1000px;
      margin: 2rem auto;
    }

    h1 {
      text-align: center;
      margin-bottom: 2rem;
      font-size: 2.2rem;
      letter-spacing: 1px;
    }

    /* ====== JOURNAL-SPECIFIC STYLES ====== */
    .journal-container {
      background: rgba(255, 255, 255, 0.8);
      border-radius: 1rem;
      padding: 2rem;
      box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
    }

    /* Forms */
    .add-journal-form, .search-form {
      display: flex;
      flex-direction: column;
      gap: 12px;
      margin-bottom: 1.5rem;
    }

    .add-journal-form input,
    .add-journal-form textarea,
    .add-journal-form button,
    .search-form input,
    .search-form button {
      padding: 12px;
      border-radius: 8px;
      border: 1px solid #ccc;
      font-size: 1rem;
    }

    .add-journal-form button {
      background-color: #2e7d32;
      color: white;
      border: none;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }
    .add-journal-form button:hover {
      background-color: #256428;
    }

    .search-form button {
      background-color: #2980b9;
      color: white;
      border: none;
      cursor: pointer;
      transition: background 0.3s;
    }
    .search-form button:hover {
      background-color: #1d6fa5;
    }

    /* Journal Entries */
    .journal-list {
      list-style-type: none;
      padding-left: 0;
    }

    .journal-list li {
      background: white;
      padding: 1.2rem;
      margin-bottom: 1rem;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
      transition: transform 0.2s ease, background 0.3s;
    }

    .journal-list li:hover {
      background: #f8f8f8;
      transform: translateY(-3px);
    }

    .journal-title {
      font-weight: bold;
      color: #3f51b5;
      font-size: 1.2rem;
      text-decoration: none;
    }
    .journal-title:hover {
      text-decoration: underline;
    }

    .journal-date {
      color: #666;
      font-size: 0.9rem;
    }

    .journal-description {
      margin-top: 10px;
      color: #333;
      font-size: 1rem;
      line-height: 1.5;
    }

    @media (max-width: 768px) {
      .journal-container { padding: 1rem; }
      h1 { font-size: 1.8rem; }
    }
  </style>
</head>
<body>

  <!-- Shared Navbar -->
  <nav>Academic Toolkit - Journals</nav>

  <div class="container">
    <div class="journal-container">
      <h1><i class="fas fa-book-open"></i> My Journal</h1>

      <!-- Example form + list (to test styling) -->
      <form class="add-journal-form">
        <input type="text" placeholder="Journal Title" required>
        <textarea rows="4" placeholder="Write your entry..."></textarea>
        <button type="submit">Add Journal</button>
      </form>

      <ul class="journal-list">
        <li>
          <a href="#" class="journal-title">First Journal Entry</a>
          <div class="journal-date">19 August 2025</div>
          <div class="journal-description">This is a sample journal entry to show styling.</div>
        </li>
      </ul>
    </div>
  </div>

</body>
</html>
