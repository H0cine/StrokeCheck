<?php
session_start();
require 'db.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $password = $_POST["password"];

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password_hash FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if ($password === $user['password_hash']) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid username or password";
            }
        } else {
            $error = "Invalid username or password";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Stroke Risk Predictor</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 60px 50px;
        }

        header {
            text-align: center;
            margin-bottom: 40px;
        }

        h1 {
            color: #000;
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 10px;
            letter-spacing: -0.5px;
        }

        .subtitle {
            color: #666;
            font-size: 16px;
            font-weight: 400;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            color: #000;
            font-weight: 600;
            margin-bottom: 10px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            font-family: inherit;
            background: #f9f9f9;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #000;
            background: white;
            box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.1);
        }

        button {
            width: 100%;
            padding: 16px;
            background: #000;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-top: 10px;
        }

        button:hover {
            background: #333;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        button:active {
            transform: translateY(0);
        }

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 25px;
            font-size: 14px;
            font-weight: 500;
            text-align: center;
        }

        .alert-error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            border: 2px solid #dc3545;
            color: #dc3545;
        }

        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            font-size: 13px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            color: #666;
        }

        .remember-me input {
            margin-right: 8px;
        }

        .footer-note {
            text-align: center;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 2px solid #f0f0f0;
            color: #999;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <div class="container">
        <header>
            <h1>🏥 Welcome Back</h1>
            <p class="subtitle">Login to Stroke Risk Predictor</p>
        </header>

        <?php if ($error): ?>
            <div class="alert alert-error">⚠️ <?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required 
                       value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="remember-forgot">
                <label class="remember-me">
                    <input type="checkbox" name="remember">
                    Remember me
                </label>
            </div>

            <button type="submit">Login</button>
        </form>

        <div class="footer-note">
            Contact administrator for access
        </div>
    </div>
</body>

</html>