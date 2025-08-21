<nav>
    <div class="navbar-container">
        <div class="logo">
            <a href="homePage.php">Student Academic Toolkit</a>
        </div>
        <ul class="nav-links">
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="notes.php">Notes</a></li>
            <li><a href="tasks.php">Tasks</a></li>
            <li><a href="journal.php">Journal</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
</nav>

<style>
    nav {
        background-color: #2e3d49;
        padding: 20px;
        color: white;
        font-family: 'Arial', sans-serif;
    }

    .navbar-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .logo a {
        color: white;
        font-size: 26px;
        font-weight: bold;
        text-decoration: none;
    }

    .nav-links {
        list-style: none;
        padding: 0;
        display: flex;
    }

    .nav-links li {
        margin-left: 25px;
    }

    .nav-links a {
        color: white;
        text-decoration: none;
        font-size: 18px;
        padding: 5px;
    }

    .nav-links a:hover {
        color: #4CAF50;
        border-bottom: 2px solid #4CAF50;
    }
</style>
