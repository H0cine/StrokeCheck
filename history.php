<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
$user_id = $_SESSION['user_id'];

// Fetch history
$sql = "SELECT * FROM predictions WHERE user_id = ? ORDER BY prediction_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$history = [];
$labels = [];
$data_points = [];

while ($row = $result->fetch_assoc()) {
    $history[] = $row;
    // For chart (reverse order for time flow)
}
// Reverse for chart to show oldest to newest
$chart_history = array_reverse($history);
foreach ($chart_history as $row) {
    $labels[] = date("M d, H:i", strtotime($row['prediction_date']));
    $data_points[] = $row['risk_probability'];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results History - Stroke Risk Predictor</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .table-container {
            overflow-x: auto;
            margin-top: 30px;
            background: var(--bg-card);
            border-radius: 15px;
            padding: 20px;
            box-shadow: var(--shadow-card);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            color: var(--text-primary);
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            font-weight: 700;
            text-transform: uppercase;
            font-size: 13px;
        }

        tr:last-child td {
            border-bottom: none;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        .status-High { background: rgba(255, 107, 107, 0.2); color: #ff6b6b; }
        .status-Medium { background: rgba(255, 152, 0, 0.2); color: #ff9800; }
        .status-Low { background: rgba(40, 167, 69, 0.2); color: #28a745; }

        .chart-container {
            background: var(--bg-card);
            border-radius: 15px;
            padding: 20px;
            box-shadow: var(--shadow-card);
            margin-bottom: 30px;
            height: 400px;
        }
    </style>
</head>

<body>
    <div class="user-bar">
        <a href="index.php" class="nav-btn">← Back to Predictor</a>
        <a href="learn.php" class="nav-btn">Learn</a>
        <a href="Emergency.php" class="nav-btn">Emergency</a>
        <span class="welcome-text">Welcome back,</span>
        <button id="theme-toggle" class="theme-toggle" aria-label="Toggle Dark Mode">
            <span class="icon">🌙</span>
        </button>
        <span class="username">👤 <?= htmlspecialchars($username) ?></span>
        <a href="login.php" class="logout-btn">Logout</a>
    </div>

    <div class="page-container">
        <h1 class="page-title">Prediction History</h1>

        <?php if (count($history) > 0): ?>
            <div class="chart-container">
                <canvas id="riskChart"></canvas>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Age</th>
                            <th>BMI</th>
                            <th>Glucose</th>
                            <th>Result</th>
                            <th>Probability</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($history as $row): ?>
                            <tr>
                                <td><?= date("M d, Y H:i", strtotime($row['prediction_date'])) ?></td>
                                <td><?= htmlspecialchars($row['age']) ?></td>
                                <td><?= htmlspecialchars($row['bmi']) ?></td>
                                <td><?= htmlspecialchars($row['glucose']) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $row['risk_level'] ?>">
                                        <?= htmlspecialchars($row['risk_level']) ?>
                                    </span>
                                </td>
                                <td><?= number_format($row['risk_probability'], 1) ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="hero-card" style="background: var(--bg-card); color: var(--text-primary);">
                <h2>No History Yet</h2>
                <p>Make your first prediction to see your history and trends here.</p>
                <br>
                <a href="index.php" class="nav-btn" style="background: var(--button-bg); color: var(--button-text); border: none;">Go to Predictor</a>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Theme Toggle Logic
        const toggleBtn = document.getElementById('theme-toggle');
        const body = document.body;
        const icon = toggleBtn.querySelector('.icon');
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
                updateChartColor('light');
            } else {
                body.setAttribute('data-theme', 'dark');
                localStorage.setItem('theme', 'dark');
                icon.textContent = '☀️';
                updateChartColor('dark');
            }
        });

        // Chart.js Logic
        <?php if (count($history) > 0): ?>
        const ctx = document.getElementById('riskChart').getContext('2d');
        
        let chartColor = (currentTheme === 'dark') ? '#ffffff' : '#1a1a1a';
        let gridColor = (currentTheme === 'dark') ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';

        const riskChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($labels) ?>,
                datasets: [{
                    label: 'Stroke Risk Probability (%)',
                    data: <?= json_encode($data_points) ?>,
                    borderColor: '#5b4cd3',
                    backgroundColor: 'rgba(91, 76, 211, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#5b4cd3',
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        grid: { color: gridColor },
                        ticks: { color: chartColor }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: chartColor }
                    }
                },
                plugins: {
                    legend: {
                        labels: { color: chartColor }
                    }
                }
            }
        });

        function updateChartColor(theme) {
            const color = (theme === 'dark') ? '#ffffff' : '#1a1a1a';
            const grid = (theme === 'dark') ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
            
            riskChart.options.scales.y.ticks.color = color;
            riskChart.options.scales.x.ticks.color = color;
            riskChart.options.scales.y.grid.color = grid;
            riskChart.options.plugins.legend.labels.color = color;
            riskChart.update();
        }
        <?php endif; ?>
    </script>
</body>
</html>
