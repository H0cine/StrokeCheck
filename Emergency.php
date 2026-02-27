<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Knowledge Hub - Stroke Risk Predictor</title>
    <link rel="stylesheet" href="style.css">
</head>


<body>
    <div class="user-bar">
        <a href="index.php" class="nav-btn">← Back to Predictor</a>
        <a href="learn.php" class="nav-btn">Learn</a>
        <a href="history.php" class="nav-btn">History</a>
        <span class="welcome-text">Welcome back,</span>
        <button id="theme-toggle" class="theme-toggle" aria-label="Toggle Dark Mode">
            <span class="icon">🌙</span>
        </button>
        <span class="username">👤 <?= htmlspecialchars($username) ?></span>
        <a href="login.php" class="logout-btn">Logout</a>
    </div>

    <div class="page-container">
        <div class="hero-card emergency-hero">
            <div class="hero-icon">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-lightning" viewBox="0 0 16 16">
  <path d="M5.52.359A.5.5 0 0 1 6 0h4a.5.5 0 0 1 .474.658L8.694 6H12.5a.5.5 0 0 1 .395.807l-7 9a.5.5 0 0 1-.873-.454L6.823 9.5H3.5a.5.5 0 0 1-.48-.641zM6.374 1 4.168 8.5H7.5a.5.5 0 0 1 .478.647L6.78 13.04 11.478 7H8a.5.5 0 0 1-.474-.658L9.306 1z"/>
</svg>
            </div>
            <h1 class="hero-title">Think FAST</h1>
            <p class="hero-subtitle">if you suspect a stroke, evry second counts. Act immediately</p>
        </div>

        <h2 class="section-title">Signs to watch for</h2>

        <div class="resources-grid">
            <a class="resource-card">
                <div class="resource-icon blue">
                    <svg width="60" height="100" viewBox="0 0 60 100" xmlns="http://www.w3.org">
    <text x="0" y="75" font-family="Arial, sans-serif" font-size="80" fill="#c70505">F</text>
</svg>

                </div>
                <div class="resource-content">
                    <div class="resource-title">Face Drooping</div>
                    <div class="resource-url">Ask them to smile. Is one side of the face drooping or numb?</div>
                </div>
            </a>

            <a class="resource-card">
                <div class="resource-icon red">
                    <svg width="60" height="100" viewBox="0 0 60 100" xmlns="http://www.w3.org">
    <text x="0" y="75" font-family="Arial, sans-serif" font-size="80" fill="#c70505">A</text>
</svg>
                </div>
                <div class="resource-content">
                    <div class="resource-title">Arm Weakness</div>
                    <div class="resource-url">Ask them to raise both arms. does one arm drift downward?</div>
                </div>
               
            </a>

            <a class="resource-card">
                <div class="resource-icon green">
                    <svg width="60" height="100" viewBox="0 0 60 100" xmlns="http://www.w3.org">
    <text x="0" y="75" font-family="Arial, sans-serif" font-size="80" fill="#c70505">S</text>
</svg>
                </div>
                <div class="resource-content">
                    <div class="resource-title">Speech difficulty</div>
                    <div class="resource-url">Ask them to repeat a simple phrase. Is their speech slurred?</div>
                </div>
             
            </a>

            <a class="resource-card">
                <div class="resource-icon purple">
                    <svg width="60" height="100" viewBox="0 0 60 100" xmlns="http://www.w3.org">
    <text x="0" y="75" font-family="Arial, sans-serif" font-size="80" fill="#c70505">T</text>
</svg>
                </div>
                <div class="resource-content">
                    <div class="resource-title">TIme to call 3016/16</div>
                    <div class="resource-url">If they show any of these signs, call the emergency services away</div>
                </div>
               
            </a>
        </div>
    </div>
    <script>
        const toggleBtn = document.getElementById('theme-toggle');
        const body = document.body;
        const icon = toggleBtn.querySelector('.icon');

        // Check local storage
        const currentTheme = localStorage.getItem('theme');
        if (currentTheme === 'dark') {
            body.setAttribute('data-theme', 'dark');
            icon.textContent = '☀️';
        } else {
            icon.textContent = '🌙';
        }

        toggleBtn.addEventListener('click', () => {
            if (body.hasAttribute('data-theme')) {
                body.removeAttribute('data-theme');
                localStorage.setItem('theme', 'light');
                icon.textContent = '🌙';
            } else {
                body.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                icon.textContent = '☀️';
            }
        });
    </script>
</body>

</html>