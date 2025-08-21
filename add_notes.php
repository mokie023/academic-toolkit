<?php
session_start();
require_once 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Only process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_note'])) {
    // Validate required fields
    if (empty($_POST['document_name']) || empty($_FILES['document']['name'])) {
        echo "<script>alert('Please provide a document name and select a file.'); window.history.back();</script>";
        exit();
    }

    $user_id       = $_SESSION['user_id'];
    $document_name = trim($_POST['document_name']);
    $folder_id     = !empty($_POST['folder_id']) ? intval($_POST['folder_id']) : null;

    // File handling
    $uploads_dir   = $_SERVER['DOCUMENT_ROOT'] . "/StudentAcademicToolkit/uploads/";
    if (!is_dir($uploads_dir)) {
        mkdir($uploads_dir, 0777, true); // Create uploads folder if missing
    }

    $original_name = basename($_FILES['document']['name']);
    $extension     = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));
    $allowed_ext   = ['pdf', 'doc', 'docx', 'txt'];

    if (!in_array($extension, $allowed_ext)) {
        echo "<script>alert('Invalid file type. Allowed: PDF, DOC, DOCX, TXT'); window.history.back();</script>";
        exit();
    }

    $new_filename  = uniqid("note_", true) . "." . $extension;
    $target_path   = $uploads_dir . $new_filename;

    if (move_uploaded_file($_FILES['document']['tmp_name'], $target_path)) {
        // Insert into DB
        $stmt = $conn->prepare("INSERT INTO notes (user_id, document_name, document_path, folder_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("issi", $user_id, $document_name, $new_filename, $folder_id);
        if ($stmt->execute()) {
            echo "<script>alert('Note uploaded successfully!'); window.location='notes.php';</script>";
        } else {
            echo "<script>alert('Database error while saving note.'); window.history.back();</script>";
        }
        $stmt->close();
    } else {
        echo "<script>alert('File upload failed. Check folder permissions.'); window.history.back();</script>";
    }
} else {
    // If accessed directly without form
    echo "<script>alert('Invalid request.'); window.location='notes.php';</script>";
    exit();
}
?>
