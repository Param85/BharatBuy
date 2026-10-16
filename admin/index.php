<?php
/**
 * Admin Dashboard - Analytics Overview
 * KPI stat cards + Chart.js charts
 */
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/includes/admin_header.php';

// ── Fetch KPI Data ──
try {
    $totalUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $totalProducts = $pdo->query("SELECT COUNT(*) FROM product_prices")->fetchColumn();
    $activeTrackers = $pdo->query("SELECT COUNT(*) FROM product_trackers WHERE is_active = 1")->fetchColumn();
    $todaySearches = $pdo->query("SELECT COUNT(*) FROM search_history WHERE DATE(searched_at) = CURDATE()")->fetchColumn();
    $totalChatbotLogs = $pdo->query("SELECT COUNT(*) FROM chatbot_logs")->fetchColumn();

    // Gemini success rate
    $geminiTotal = $pdo->query("SELECT COUNT(*) FROM chatbot_logs WHERE source = 'gemini'")->fetchColumn();
    $geminiErrors = $pdo->query("SELECT COUNT(*) FROM chatbot_logs WHERE source = 'gemini' AND error_message IS NOT NULL AND error_message != ''")->fetchColumn();
    $geminiSuccessRate = $geminiTotal > 0 ? round((($geminiTotal - $geminiErrors) / $geminiTotal) * 100, 1) : 0;

    // User registrations (last 30 days)
    $regStmt = $pdo->query("SELECT DATE(created_at) as reg_date, COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY DATE(created_at) ORDER BY reg_date ASC");
    $registrations = $regStmt->fetchAll();

    // Search volume (last 7 days)
    $searchStmt = $pdo->query("SELECT DATE(searched_at) as search_date, COUNT(*) as count FROM search_history WHERE searched_at >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY DATE(searched_at) ORDER BY search_date ASC");
    $searchVolume = $searchStmt->fetchAll();

    // Provider health
    $providerStmt = $pdo->query("SELECT 
        SUM(CASE WHEN status >= 200 AND status < 300 THEN 1 ELSE 0 END) as success_count,
        SUM(CASE WHEN status >= 400 THEN 1 ELSE 0 END) as error_count
        FROM provider_logs");
    $providerHealth = $providerStmt->fetch();

    // Chatbot source distribution
    $chatSourceStmt = $pdo->query("SELECT source, COUNT(*) as count FROM chatbot_logs GROUP BY source");
    $chatSources = $chatSourceStmt->fetchAll();

} catch (PDOException $e) {
    $totalUsers = $totalProducts = $activeTrackers = $todaySearches = $totalChatbotLogs = 0;
    $geminiSuccessRate = 0;
    $registrations = $searchVolume = $chatSources = [];
    $providerHealth = ['success_count' => 0, 'error_count' => 0];
}
?>

<div class="admin-page-header">
    <h1>Dashboard</h1>
    <p>Real-time overview of BharatBuy system metrics and performance</p>
</div>

<!-- KPI Stat Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary"><i class="fa-solid fa-users"></i></div>
        <div class="stat-value"><?= number_format($totalUsers); ?></div>
        <div class="stat-label">Total Users</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fa-solid fa-box-open"></i></div>
        <div class="stat-value"><?= number_format($totalProducts); ?></div>
        <div class="stat-label">Products Tracked</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success"><i class="fa-solid fa-bell"></i></div>
        <div class="stat-value"><?= number_format($activeTrackers); ?></div>
        <div class="stat-label">Active Trackers</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon info"><i class="fa-solid fa-magnifying-glass"></i></div>
        <div class="stat-value"><?= number_format($todaySearches); ?></div>
        <div class="stat-label">Searches Today</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning"><i class="fa-solid fa-robot"></i></div>
        <div class="stat-value"><?= number_format($totalChatbotLogs); ?></div>
        <div class="stat-label">Chatbot Queries</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon <?= $geminiSuccessRate >= 90 ? 'success' : ($geminiSuccessRate >= 70 ? 'warning' : 'danger'); ?>">
            <i class="fa-solid fa-bolt"></i>
        </div>
        <div class="stat-value"><?= $geminiSuccessRate; ?>%</div>
        <div class="stat-label">Gemini Success Rate</div>
    </div>
</div>

<!-- Charts -->
<div class="charts-grid">
    <div class="chart-card">
        <h3><i class="fa-solid fa-user-plus" style="color: var(--admin-primary-hover); margin-right: 0.5rem;"></i>User Registrations (30 Days)</h3>
        <div class="chart-container">
            <canvas id="regChart"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <h3><i class="fa-solid fa-magnifying-glass-chart" style="color: var(--admin-info); margin-right: 0.5rem;"></i>Search Volume (7 Days)</h3>
        <div class="chart-container">
            <canvas id="searchChart"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <h3><i class="fa-solid fa-server" style="color: var(--admin-success); margin-right: 0.5rem;"></i>Provider Health</h3>
        <div class="chart-container" style="max-width: 280px; margin: 0 auto;">
            <canvas id="providerChart"></canvas>
        </div>
    </div>
    <div class="chart-card">
        <h3><i class="fa-solid fa-comments" style="color: var(--admin-warning); margin-right: 0.5rem;"></i>Chatbot Source Distribution</h3>
        <div class="chart-container" style="max-width: 280px; margin: 0 auto;">
            <canvas id="chatSourceChart"></canvas>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartDefaults = {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { labels: { color: '#94A3B8', font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 } } }
        },
        scales: {
            x: { ticks: { color: '#64748B', font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 } }, grid: { color: 'rgba(51, 65, 85, 0.3)' } },
            y: { ticks: { color: '#64748B', font: { family: "'Plus Jakarta Sans', sans-serif", size: 11 } }, grid: { color: 'rgba(51, 65, 85, 0.3)' }, beginAtZero: true }
        }
    };

    // Registration chart
    const regData = <?= json_encode($registrations); ?>;
    new Chart(document.getElementById('regChart'), {
        type: 'line',
        data: {
            labels: regData.map(r => r.reg_date),
            datasets: [{
                label: 'Registrations',
                data: regData.map(r => r.count),
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#10B981',
                pointRadius: 3
            }]
        },
        options: { ...chartDefaults, plugins: { ...chartDefaults.plugins, legend: { display: false } } }
    });

    // Search volume chart
    const searchData = <?= json_encode($searchVolume); ?>;
    new Chart(document.getElementById('searchChart'), {
        type: 'bar',
        data: {
            labels: searchData.map(s => s.search_date),
            datasets: [{
                label: 'Searches',
                data: searchData.map(s => s.count),
                backgroundColor: 'rgba(59, 130, 246, 0.6)',
                borderColor: '#3B82F6',
                borderWidth: 1,
                borderRadius: 6
            }]
        },
        options: { ...chartDefaults, plugins: { ...chartDefaults.plugins, legend: { display: false } } }
    });

    // Provider health doughnut
    const providerData = <?= json_encode($providerHealth); ?>;
    new Chart(document.getElementById('providerChart'), {
        type: 'doughnut',
        data: {
            labels: ['Success', 'Errors'],
            datasets: [{
                data: [providerData.success_count || 0, providerData.error_count || 0],
                backgroundColor: ['#10B981', '#EF4444'],
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: { legend: { position: 'bottom', labels: { color: '#94A3B8', font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 }, padding: 16 } } }
        }
    });

    // Chatbot source pie
    const chatData = <?= json_encode($chatSources); ?>;
    const sourceColors = { gemini: '#10B981', fallback: '#F59E0B', rate_limit: '#EF4444', rejected: '#64748B' };
    new Chart(document.getElementById('chatSourceChart'), {
        type: 'doughnut',
        data: {
            labels: chatData.map(c => c.source),
            datasets: [{
                data: chatData.map(c => c.count),
                backgroundColor: chatData.map(c => sourceColors[c.source] || '#94A3B8'),
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            cutout: '70%',
            plugins: { legend: { position: 'bottom', labels: { color: '#94A3B8', font: { family: "'Plus Jakarta Sans', sans-serif", size: 12 }, padding: 16 } } }
        }
    });
});
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
