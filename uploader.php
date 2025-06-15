<?php
// Buat folder uploads jika belum ada
$uploadDir = __DIR__ . '/uploads/';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$response = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $filename = basename($file['name']);
    $targetFile = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        $fileUrl = '/uploads/' . $filename;
        $response = "Upload Successful: <a href=\"$fileUrl\" target=\"_blank\">$fileUrl</a>";
    } else {
        $response = "Upload failed!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Simple File Uploader</title>
</head>
<body>
    <h2>Upload File</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit">Upload</button>
    </form>

    <?php if ($response): ?>
        <p><?= $response ?></p>
    <?php endif; ?>
</body>
</html>