<?php
session_start();
require_once 'db_connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('templates/navbar.php');

$user_id = $_SESSION['user_id'];
$upload_dir_root = $_SERVER['DOCUMENT_ROOT'] . "/StudentAcademicToolkit/uploads/user_$user_id/";

if (!is_dir($upload_dir_root)) {
    mkdir($upload_dir_root, 0777, true);
}

$feedback = "";

// Function to sanitize user input
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

// Create folder logic
if (isset($_POST['create_folder'])) {
    $folder_name = sanitize_input($_POST['folder_name']);
    if ($folder_name !== '') {
        $stmt = $conn->prepare("SELECT id FROM folders WHERE user_id = ? AND folder_name = ?");
        $stmt->bind_param("is", $user_id, $folder_name);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $feedback = ['type' => 'warning', 'msg' => "Folder '<strong>$folder_name</strong>' already exists."];
        } else {
            $stmt->close();
            $stmt = $conn->prepare("INSERT INTO folders (user_id, folder_name) VALUES (?, ?)");
            $stmt->bind_param("is", $user_id, $folder_name);
            if ($stmt->execute()) {
                $feedback = ['type' => 'success', 'msg' => "Folder '<strong>$folder_name</strong>' created successfully!"];
            } else {
                error_log("Folder creation error: " . $stmt->error); // Log the error
                $feedback = ['type' => 'danger', 'msg' => "Error creating folder. Please try again."];
            }
        }
        $stmt->close();
    } else {
        $feedback = ['type' => 'warning', 'msg' => "Folder name cannot be empty."];
    }
}

// Fetch folders
$stmt = $conn->prepare("SELECT id, folder_name FROM folders WHERE user_id = ? ORDER BY folder_name ASC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$folders_result = $stmt->get_result();
$stmt->close();

// Fetch notes
$stmt = $conn->prepare("SELECT n.id, n.document_name, n.document_path, f.folder_name FROM notes n LEFT JOIN folders f ON n.folder_id = f.id WHERE n.user_id = ? ORDER BY n.id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$notes_result = $stmt->get_result();
$stmt->close();

// Upload note logic
if (isset($_POST['upload_note']) && isset($_FILES['file'])) {
    $folder_id = isset($_POST['folder_id']) ? intval($_POST['folder_id']) : null; // Allow null for default upload
    $file = $_FILES['file'];
    $allowed_exts = ['pdf', 'docx'];
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

    if ($file['error'] === UPLOAD_ERR_OK && in_array($file_ext, $allowed_exts)) {
        $safe_name = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', basename($file['name']));
        $unique_file_name = uniqid("note_") . '_' . $safe_name;
        $destination = $upload_dir_root . $unique_file_name;

        if (move_uploaded_file($file['tmp_name'], $destination)) {
            $relative_path = "user_$user_id/" . $unique_file_name;

            // Insert note into the database
            $stmt = $conn->prepare("INSERT INTO notes (user_id, folder_id, document_name, document_path, title, content, file_name, file_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $title = $file['name']; // Using the file name as the title
            $content = ''; // Assuming no content is provided; adjust as necessary
            $file_name = $file['name'];
            $file_path = $relative_path;

            $stmt->bind_param("iissssss", $user_id, $folder_id, $file['name'], $relative_path, $title, $content, $file_name, $file_path);

            if ($stmt->execute()) {
                $feedback = ['type' => 'success', 'msg' => "Note '<strong>" . htmlspecialchars($file['name']) . "</strong>' uploaded successfully!"];
            } else {
                error_log("Database insert error: " . $stmt->error); // Log the error
                $feedback = ['type' => 'danger', 'msg' => "Failed to save note info to database."];
                unlink($destination); // Remove the uploaded file if insertion fails
            }
            $stmt->close();
        } else {
            $feedback = ['type' => 'danger', 'msg' => "Unable to move uploaded file."];
        }
    } else {
        $feedback = ['type' => 'warning', 'msg' => "Only PDF and DOCX files are allowed."];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Your Notes - Student Academic Toolkit</title>

    <!-- Bootstrap CSS & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />

    <style>
        body {
            background: linear-gradient(135deg, #6f7bfc, #e9d2fc);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            color: #2c2c54;
        }

        .navbar, .card-header {
            background-color: #5a63f6 !important; /* matching navbar color */
        }

        .card {
            background: rgba(255 255 255 / 0.85);
            border-radius: 1rem;
            box-shadow: 0 8px 20px rgb(111 123 252 / 0.3);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 28px rgb(111 123 252 / 0.45);
        }

        h1, h3 {
            font-weight: 700;
            letter-spacing: 0.04em;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-info {
            background: #5a63f6;
            border: none;
            transition: background 0.3s ease;
        }
        .btn-info:hover, .btn-info:focus {
            background: #4c52e8;
            box-shadow: 0 0 10px #4c52e8;
        }

        .list-group-item {
            border: none;
            background: transparent;
            padding: 1rem 1.5rem;
            transition: background-color 0.3s ease;
            border-radius: 0.75rem;
        }
        .list-group-item:hover {
            background-color: rgba(111, 123, 252, 0.15);
            cursor: pointer;
        }

        .form-control:focus {
            box-shadow: 0 0 6px #5a63f6;
            border-color: #5a63f6;
        }

        /* Loader for upload button */
        #uploadBtn.loading::after {
            content: "";
            display: inline-block;
            margin-left: 10px;
            border: 2px solid #fff;
            border-top: 2px solid transparent;
            border-radius: 50%;
            width: 16px;
            height: 16px;
            animation: spin 0.8s linear infinite;
            vertical-align: middle;
        }
        @keyframes spin {
            0% { transform: rotate(0deg);}
            100% { transform: rotate(360deg);}
        }

        /* Folder filter */
        #folderFilter {
            max-width: 400px;
            margin-bottom: 1rem;
        }

        /* Notes section */
        .notes-section {
            margin-top: 2rem;
        }
    </style>
</head>
<body>

<div class="container my-5">

    <header class="mb-5 text-center text-white">
        <h1>Your Notes 📚</h1>
        <p class="lead fw-semibold">Organize your academic notes easily and securely</p>
    </header>

    <!-- Feedback Alert -->
    <?php if (!empty($feedback)) : ?>
        <div class="alert alert-<?= $feedback['type'] ?> alert-dismissible fade show" role="alert">
            <?= $feedback['msg'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Create Folder Card -->
    <section aria-labelledby="createFolderTitle" class="mb-5">
        <div class="card p-4">
            <h3 id="createFolderTitle" class="mb-4 text-center text-secondary">Create New Folder</h3>
            <form method="POST" class="row g-3 justify-content-center" novalidate>
                <div class="col-sm-8 col-md-6 col-lg-5">
                    <label for="folderName" class="form-label visually-hidden">Folder Name</label>
                    <input
                        type="text"
                        id="folderName"
                        name="folder_name"
                        class="form-control form-control-lg"
                        placeholder="Enter folder name"
                        minlength="2"
                        maxlength="50"
                        required
                        aria-describedby="folderHelp"
                    />
                    <div id="folderHelp" class="form-text text-center">Min 2, Max 50 characters</div>
                </div>
                <div class="col-sm-4 col-md-2 d-grid">
                    <button type="submit" name="create_folder" class="btn btn-info btn-lg" aria-label="Create new folder">Create</button>
                </div>
            </form>
        </div>
    </section>

    <!-- Folder Search Filter -->
    <input
        type="search"
        id="folderFilter"
        class="form-control form-control-lg"
        placeholder="Filter folders by name..."
        aria-label="Filter folders"
        oninput="filterFolders()"
    />

    <!-- Folders List -->
    <section aria-labelledby="foldersListTitle" class="mb-5">
        <h3 id="foldersListTitle" class="mb-4 text-white">Your Folders</h3>
        <?php if ($folders_result->num_rows === 0) : ?>
            <p class="text-white fst-italic text-center">No folders yet. Create one above to get started.</p>
        <?php else : ?>
            <ul id="folderList" class="list-group list-group-flush">
                <?php while ($folder = $folders_result->fetch_assoc()) : ?>
                    <li class="list-group-item" tabindex="0" role="button" aria-pressed="false" data-folder-name="<?= htmlspecialchars(strtolower($folder['folder_name'])) ?>">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><?= htmlspecialchars($folder['folder_name']) ?></span>
                            <div>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-info"
                                    data-bs-toggle="modal"
                                    data-bs-target="#uploadModal_<?= $folder['id'] ?>"
                                    aria-label="Upload note to folder <?= htmlspecialchars($folder['folder_name']) ?>"
                                >
                                    <i class="bi bi-upload"></i> Upload Note
                                </button>
                            </div>
                        </div>
                    </li>

                    <!-- Upload Modal -->
                    <div
                        class="modal fade"
                        id="uploadModal_<?= $folder['id'] ?>"
                        tabindex="-1"
                        aria-labelledby="uploadModalLabel_<?= $folder['id'] ?>"
                        aria-hidden="true"
                    >
                        <div class="modal-dialog">
                            <form method="POST" enctype="multipart/form-data" class="modal-content" onsubmit="return onUploadSubmit(<?= $folder['id'] ?>)">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="uploadModalLabel_<?= $folder['id'] ?>">Upload Note to '<?= htmlspecialchars($folder['folder_name']) ?>'</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <input type="hidden" name="folder_id" value="<?= $folder['id'] ?>" />
                                    <div class="mb-3">
                                        <label for="fileInput_<?= $folder['id'] ?>" class="form-label">Select PDF or DOCX file</label>
                                        <input
                                            type="file"
                                            class="form-control"
                                            id="fileInput_<?= $folder['id'] ?>"
                                            name="file"
                                            accept=".pdf,.docx"
                                            required
                                        />
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button
                                        type="submit"
                                        id="uploadBtn_<?= $folder['id'] ?>"
                                        name="upload_note"
                                        class="btn btn-info"
                                        aria-live="polite"
                                    >
                                        Upload
                                    </button>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>

                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
    </section>

    <!-- Notes List -->
    <section aria-labelledby="notesListTitle" class="notes-section">
        <h3 id="notesListTitle" class="mb-4 text-white">Your Notes</h3>
        <?php if ($notes_result->num_rows === 0) : ?>
            <p class="text-white fst-italic text-center">No notes uploaded yet.</p>
        <?php else : ?>
            <div class="row g-4">
                <?php while ($note = $notes_result->fetch_assoc()) : ?>
                    <article class="col-sm-6 col-md-4 col-lg-3">
                        <div class="card h-100 shadow-sm" tabindex="0" aria-label="Note <?= htmlspecialchars($note['document_name']) ?>">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title text-truncate" title="<?= htmlspecialchars($note['document_name']) ?>">
                                    <?= htmlspecialchars($note['document_name']) ?>
                                </h5>
                                <p class="card-subtitle mb-3 text-muted fst-italic" title="Folder"><?= htmlspecialchars($note['folder_name'] ?? 'Uncategorized') ?></p>
                                <a
                                    href="uploads/<?= htmlspecialchars($note['document_path']) ?>"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="mt-auto btn btn-outline-info btn-sm"
                                    aria-label="View note <?= htmlspecialchars($note['document_name']) ?>"
                                >
                                    View Document <i class="bi bi-box-arrow-up-right"></i>
                                </a>
                            </div>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<!-- Bootstrap Bundle (JS + Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Folder filter function
    function filterFolders() {
        const filterInput = document.getElementById('folderFilter');
        const filter = filterInput.value.toLowerCase();
        const folderItems = document.querySelectorAll('#folderList li');

        folderItems.forEach(item => {
            const folderName = item.getAttribute('data-folder-name');
            if (folderName.includes(filter)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Upload form submit handler to show loader on button
    function onUploadSubmit(folderId) {
        const uploadBtn = document.getElementById(`uploadBtn_${folderId}`);
        uploadBtn.classList.add('loading');
        uploadBtn.disabled = true;
        return true; // Allow form submission
    }
</script>

</body>
</html>
