<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$folder_id = intval($_GET['folder_id']);

$stmt = $conn->prepare("SELECT document_name, document_path FROM notes WHERE user_id = ? AND folder_id = ?");
$stmt->bind_param("ii", $user_id, $folder_id);
$stmt