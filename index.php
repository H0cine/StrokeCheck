<?php
session_start();
include 'db.php';

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
    <title>Stroke Risk Predictor</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="center-content">
    <div class="user-bar">
        <a href="learn.php" class="Learn">Learn</a>
        <a href="Emergency.php" class="emergency">Emergency</a>
        <a href="history.php" class="nav-btn">History</a>
        <span class="welcome-text">Welcome back,</span>
        <button id="theme-toggle" class="theme-toggle" aria-label="Toggle Dark Mode">
            <span class="icon">🌙</span>
        </button>
        <span class="username">👤 <?= htmlspecialchars($username) ?></span>
        <a href="login.php" class="logout-btn">Logout</a>
    </div>

    <div class="predictor-card">
        <header>
            <h1>🏥 Stroke Risk Predictor</h1>
            <p class="subtitle">Advanced Health Assessment Tool</p>
        </header>

        <?php
        $result_data = null;
        $ai_advice = null;

        // --- NEW: Handle results from process.php ---
        if (isset($_GET['prediction'])) {
            $result_data = json_decode(urldecode($_GET['prediction']), true);
        }
        if (isset($_GET['response'])) {
            $ai_advice = urldecode($_GET['response']);
        }
        ?>
        <form action="process.php" method="POST">
            <div class="section-title">Health Metrics</div>
            <div class="form-row">
                <div class="form-group">
                    <label for="age">Age (years)</label>
                    <input type="number" id="age" name="age" required>
                </div>
                <div class="form-group">
                    <label for="glucose">Glucose (mg/dL)</label>
                    <input type="text" id="glucose" name="glucose" required>
                </div>
            </div>
            <div class="form-group">
                <label for="bmi">BMI</label>
                <input type="text" id="bmi" name="bmi" required>
            </div>

            <div class="divider"></div>
            <div class="section-title">Demographics</div>
            <div class="select-group">
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender">
                        <option value="0">Female</option>
                        <option value="1">Male</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="residence">Residence Type</label>
                    <select id="residence" name="residence">
                        <option value="0">Rural</option>
                        <option value="1">Urban</option>
                    </select>
                </div>
            </div>

            <div class="divider"></div>
            <div class="section-title">Medical History</div>
            <div class="select-group">
                <div class="form-group">
                    <label for="hypertension">Hypertension</label>
                    <select id="hypertension" name="hypertension">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="disease">Heart Disease</label>
                    <select id="disease" name="disease">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
            </div>

            <div class="select-group">
                <div class="form-group">
                    <label for="married">Ever Married</label>
                    <select id="married" name="married">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="work">Work Type</label>
                    <select id="work" name="work">
                        <option value="0">Never Worked</option>
                        <option value="1">Private</option>
                        <option value="2">Self-employed</option>
                        <option value="3">Children</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="smoking">Smoking Status</label>
                <select id="smoking" name="smoking">
                    <option value="0">Unknown</option>
                    <option value="1">Formerly Smoked</option>
                    <option value="2">Never Smoked</option>
                    <option value="3">Smokes</option>
                </select>
            </div>

            <div class="select-group">
                <div class="form-group">
                    <label for="action">AI Action</label>
                    <select id="action" name="action">
                        <option value="predict">Predict Risk Only</option>
                        <option value="advice">Predict & Get AI Advice</option>
                    </select>
                </div>
            </div>

            <button type="submit">Process Request</button>
        </form>

        <?php if ($result_data): ?>
            <?php
                $prob = $result_data['probability'] * 100;
                
                if (isset($result_data['error'])) {
                    $risk_class = 'error';
                    $risk_level = 'error';
                    $label = "⚠️ " . $result_data['error'];
                } elseif ($prob >= 60) {
                    $risk_class = 'high-risk';
                    $risk_level = 'high';
                    $label = "🔴 HIGH Stroke Risk";
                } elseif ($prob >= 30) {
                    $risk_class = 'medium-risk';
                    $risk_level = 'medium';
                    $label = "🟡 MEDIUM Stroke Risk";
                } else {
                    $risk_class = 'low-risk';
                    $risk_level = 'low';
                    $label = "🟢 LOW Stroke Risk";
                }
            ?>
            <div class="result show <?= $risk_class; ?>">
                <div class="risk-label <?= $risk_level; ?>"><?= htmlspecialchars($label) ?></div>
                <?php if (!isset($result_data['error'])): ?>
                    <div class="probability">Probability: <?= number_format($prob, 2) ?>%</div>
                    
                    <?php if (!empty($result_data['top_features'])): ?>
                        <div class="explanation-section">
                            <div class="explanation-title">📊 Key Factors Influencing This Prediction</div>
                            <?php foreach ($result_data['top_features'] as $feature): ?>
                                <div class="feature-item <?= $feature['impact'] ?>">
                                    <div class="feature-name">
                                        <?= htmlspecialchars($feature['name']) ?>
                                        <span class="feature-value">(Value: <?= htmlspecialchars($feature['value']) ?>)</span>
                                    </div>
                                    <div class="feature-impact <?= $feature['impact'] ?>">
                                        <?= $feature['impact'] === 'increases' ? '↑ Increases Risk' : '↓ Decreases Risk' ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($ai_advice): ?>
            <div class="result show ai-advice-card">
                <div class="explanation-title">🤖 Personal AI Health Advice</div>
                <div class="ai-content">
                    <?= nl2br(htmlspecialchars($ai_advice)) ?>
                </div>
            </div>
        <?php endif; ?>

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