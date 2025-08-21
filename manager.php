<?php
// Include the config file
include('config.php');
session_start();
include('templates/navbar.php');

// Check if the type exists in the URL, if not, set a default value
$type = isset($_GET['type']) ? $_GET['type'] : 'notes';

// Display appropriate content based on the type
if ($type === 'notes') {
    echo "<h2>Manage Notes</h2>";
    // Your logic for managing notes
} elseif ($type === 'journal') {
    echo "<h2>Manage Journal</h2>";
    // Your logic for managing journal
} elseif ($type === 'tasks') {
    echo "<h2>Manage Tasks</h2>";
    // Your logic for managing tasks
} elseif ($type === 'wiki') {
    echo "<h2>Manage Wiki</h2>";
    // Your logic for managing wiki
} else {
    echo "<h2>Invalid Type</h2>";
}
?>
