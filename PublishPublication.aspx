<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Website Terkunci 🔐</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .login-box {
            background-color: rgba(0, 0, 0, 0.6);
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            width: 320px;
            box-shadow: 0 0 15px rgba(0,0,0,0.5);
        }

        .login-box img {
            width: 100px;
            margin-bottom: 20px;
            border-radius: 10px;
        }

        h2 {
            margin-bottom: 20px;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
            border: none;
            background-color: #333;
            color: white;
        }

        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .error {
            color: #ff6666;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <div class="login-box">
        <img src="https://i.top4top.io/p_34112gtz80.jpg" alt="Logo" />
        <h2>Login to Continue</h2>
        <input type="password" id="passwordInput" placeholder="Enter password" />
        <button onclick="checkPassword()">Login</button>
        <div id="errorMessage" class="error"></div>
    </div>

    <script>
        const correctPassword = 'ZammLaex';

        function checkPassword() {
            const input = document.getElementById('passwordInput').value;
            const errorBox = document.getElementById('errorMessage');

            if (input === correctPassword) {
                window.location.href = "/Admin/UserFiles/47.aspx";
            } else {
                errorBox.textContent = "Password salah! Coba lagi.";
            }
        }
    </script>

</body>
</html>