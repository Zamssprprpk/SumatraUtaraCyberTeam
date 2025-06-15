<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Host Checked</title>
  <style>
    body {
      background-color: #111;
      color: #00ffcc;
      font-family: monospace;
      text-align: center;
      padding-top: 50px;
    }
    input[type=text] {
      padding: 10px;
      width: 300px;
      background: #222;
      color: #0f0;
      border: 1px solid #0f0;
      border-radius: 5px;
    }
    input[type=submit] {
      padding: 10px 20px;
      background: #00ffcc;
      color: #000;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }
    pre {
      text-align: left;
      background: #000;
      padding: 20px;
      margin: 30px auto;
      width: 80%;
      border: 1px solid #00ffcc;
      border-radius: 10px;
    }
  </style>
</head>
<body>

<h1>Host Checked</h1>
<form method="GET">
  <input type="text" name="host" placeholder="Enter hostname or IP..." required>
  <input type="submit" value="Check">
</form>

<?php
if (isset($_GET['host'])) {
    $host = $_GET['host'];
    echo "<h2>Result for: <code>$host</code></h2>";
    echo "<pre>";
    // Command Injection Vulnerability (for demo purposes only!)
    system("ping -c 2 " . $host);
    echo "</pre>";
}
?>

</body>
</html>