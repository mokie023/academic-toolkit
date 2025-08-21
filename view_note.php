<?php
session_start();
require_once 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('templates/navbar.php');

$note_id = isset($_GET['note_id']) ? intval($_GET['note_id']) : null;
if (!$note_id) {
    echo "<script>alert('No note ID provided.'); window.location = 'notes.php';</script>";
    exit();
}

// Fetch note info with folder name
$stmt = $conn->prepare("SELECT n.*, f.folder_name FROM notes n 
                        LEFT JOIN folders f ON n.folder_id = f.id 
                        WHERE n.id = ? AND n.user_id = ?");
$stmt->bind_param("ii", $note_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<script>alert('Note not found or you do not have access.'); window.location = 'notes.php';</script>";
    exit();
}

$note = $result->fetch_assoc();
$document_path = $note['document_path'] ?? '';

if (empty($document_path)) {
    echo "<script>alert('No document associated with this note.'); window.location = 'notes.php';</script>";
    exit();
}

// Normalize paths
$uploads_folder = "/StudentAcademicToolkit/uploads/";
$document_path_clean = ltrim($document_path, '/\\'); // Remove leading slashes if any

$file_path = $_SERVER['DOCUMENT_ROOT'] . $uploads_folder . $document_path_clean;
$public_path = "http://localhost" . $uploads_folder . $document_path_clean;
$file_extension = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
$folder_name = $note['folder_name'] ? $note['folder_name'] : 'No Folder';

// Check if file exists on server
if (!file_exists($file_path)) {
    echo "<script>alert('File not found on server. Please check the path and try again.'); window.location = 'notes.php';</script>";
    exit();
}

// Handle delete request
if (isset($_POST['delete_note'])) {
    $stmt_del = $conn->prepare("DELETE FROM notes WHERE id = ? AND user_id = ?");
    $stmt_del->bind_param("ii", $note_id, $_SESSION['user_id']);
    if ($stmt_del->execute()) {
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        echo "<script>alert('Note deleted successfully.'); window.location = 'notes.php';</script>";
        exit();
    } else {
        echo "<script>alert('Error deleting note.');</script>";
    }
    $stmt_del->close();
}
?>

<div class="container-fluid mt-5" style="padding: 0;">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4><i class="fas fa-file-alt"></i> Document Viewer</h4>
        </div>
        <div class="card-body" style="padding: 0;">
            <h5 class="mb-3"><strong>Document Name:</strong> <?= htmlspecialchars($note['document_name']) ?></h5>
            <h6 class="mb-3"><strong>Folder:</strong> <?= htmlspecialchars($folder_name) ?></h6>
            <p><strong>Document Type:</strong> <?= strtoupper($file_extension) ?></p>

            <button id="fullscreen-btn" class="btn btn-primary mb-3"><i class="fas fa-expand"></i> Enter Full Screen</button>

            <div id="document-container">
                <?php
                switch ($file_extension) {
                    case 'pdf':
                        echo "<embed src='" . htmlspecialchars($public_path) . "' type='application/pdf' style='width: 100%; height: 80vh;' />";
                        break;

                    case 'docx':
                    case 'doc':
                        // Use Office online viewer for Word docs
                        $office_url = "https://view.officeapps.live.com/op/embed.aspx?src=" . urlencode($public_path);
                        echo "<iframe src='" . htmlspecialchars($office_url) . "' width='100%' height='80vh' frameborder='0'></iframe>";
                        break;

                    case 'txt':
                        $text_content = file_get_contents($file_path);
                        echo "<pre style='height: 80vh; overflow-y: auto; background-color: #f8f9fa; padding: 10px;'>" . htmlspecialchars($text_content) . "</pre>";
                        break;

                    default:
                        echo "<p class='alert alert-warning'>❌ This file format (<strong>" . htmlspecialchars($file_extension) . "</strong>) is not supported for inline viewing. ";
                        echo "<a href='download.php?file=" . urlencode($document_path_clean) . "'>Click here to download it</a>.</p>";
                        break;
                }
                ?>
            </div>
        </div>
        <div class="card-footer">
            <a href="notes.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Notes</a>
            <a href="download.php?file=<?= urlencode($document_path_clean) ?>" class="btn btn-success"><i class="fas fa-download"></i> Download</a>

            <!-- Delete Note Button -->
            <form method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this note? This action cannot be undone.')">
                <button type="submit" name="delete_note" class="btn btn-danger"><i class="fas fa-trash"></i> Delete Note</button>
            </form>
        </div>
    </div>
</div>

<script>
    const fullscreenBtn = document.getElementById("fullscreen-btn");
    fullscreenBtn.addEventListener("click", function () {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().then(() => {
                fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i> Exit Full Screen';
            }).catch((err) => {
                alert(`Error attempting to enable full-screen mode: ${err.message}`);
            });
        } else {
            document.exitFullscreen().then(() => {
                fullscreenBtn.innerHTML = '<i class="fas fa-expand"></i> Enter Full Screen';
            });
        }
    });
</script>
