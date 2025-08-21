<?php
// Check if folder parameter is given
if (!isset($_GET['folder'])) {
    die("Folder not specified.");
}

$folder = basename($_GET['folder']); // sanitize input

$dirPath = __DIR__ . "/folders/" . $folder; // assuming your folders are inside a 'folders' directory

if (!is_dir($dirPath)) {
    die("Folder does not exist.");
}

$files = scandir($dirPath);

?>

<!DOCTYPE html>
<html>
<head>
    <title>Files in <?php echo htmlspecialchars($folder); ?></title>
</head>
<body>
    <h1>Files in folder: <?php echo htmlspecialchars($folder); ?></h1>
    <ul>
    <?php
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        echo "<li>" . htmlspecialchars($file) . "</li>";
    }
    ?>
    </ul>
    <a href="index.php">Back</a>
</body>
</html>
