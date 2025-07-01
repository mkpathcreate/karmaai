<?php
require_once 'DatabaseService.php';

$databaseService = new DatabaseService();
$action = $_GET['action'] ?? 'stats';

$stats = $databaseService->getStatistics();
$recentMatches = $databaseService->getRecentMatches(10);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Horoscope Matching Admin | Database</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 10px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: linear-gradient(135deg, #667eea, #764ba2); color: white; padding: 20px; border-radius: 10px; text-align: center; }
        .match-card { background: #f8f9fa; padding: 15px; margin-bottom: 15px; border-radius: 8px; border-left: 4px solid #667eea; }
        .match-header { display: flex; justify-content: between; align-items: center; margin-bottom: 10px; }
        .match-details { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; font-size: 0.9rem; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        .nav { margin-bottom: 20px; }
        .nav a { margin-right: 15px; padding: 8px 16px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; }
        .nav a.active { background: #764ba2; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📊 Horoscope Matching Database Admin</h1>
        
        <div class="nav">
            <a href="?action=stats" class="<?= $action === 'stats' ? 'active' : '' ?>">Statistics</a>
            <a href="?action=recent" class="<?= $action === 'recent' ? 'active' : '' ?>">Recent Matches</a>
            <a href="index.php">← Back to Matcher</a>
        </div>

        <?php if ($action === 'stats'): ?>
            <h2>📈 Database Statistics</h2>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?= $stats['total_matches'] ?></h3>
                    <p>Total Matches</p>
                </div>
                <div class="stat-card">
                    <h3><?= $stats['average_vedic_score'] ?>%</h3>
                    <p>Avg Vedic Score</p>
                </div>
                <div class="stat-card">
                    <h3><?= $stats['average_tamil_score'] ?>%</h3>
                    <p>Avg Tamil Score</p>
                </div>
                <div class="stat-card">
                    <h3><?= $stats['excellent_matches'] ?></h3>
                    <p>Excellent Matches</p>
                </div>
            </div>

            <h3>🏙️ Popular Prefectures</h3>
            <table>
                <tr><th>Prefecture</th><th>Usage Count</th></tr>
                <?php foreach ($stats['popular_prefectures'] as $prefecture => $count): ?>
                    <tr><td><?= htmlspecialchars($prefecture) ?></td><td><?= $count ?></td></tr>
                <?php endforeach; ?>
            </table>

        <?php elseif ($action === 'recent'): ?>
            <h2>⏰ Recent Matches (Last 10)</h2>
            
            <?php foreach ($recentMatches as $match): ?>
                <div class="match-card">
                    <div class="match-header">
                        <strong>Match ID: <?= htmlspecialchars($match['id']) ?></strong>
                        <span><?= $match['timestamp'] ?></span>
                    </div>
                    
                    <div class="match-details">
                        <div>
                            <h4>👥 Couple</h4>
                            <p><strong>Groom:</strong> <?= htmlspecialchars($match['input']['groom']['name_hash'] ?? $match['input']['groom']['name'] ?? 'Unknown') ?></p>
                            <p><strong>Bride:</strong> <?= htmlspecialchars($match['input']['bride']['name_hash'] ?? $match['input']['bride']['name'] ?? 'Unknown') ?></p>
                            <p><strong>Locations:</strong> <?= htmlspecialchars($match['input']['groom']['prefecture']) ?> / <?= htmlspecialchars($match['input']['bride']['prefecture']) ?></p>
                        </div>
                        
                        <div>
                            <h4>🔮 Results</h4>
                            <p><strong>Vedic:</strong> <?= $match['results']['vedic_compatibility']['score'] ?>/<?= $match['results']['vedic_compatibility']['max_score'] ?> (<?= round($match['results']['vedic_compatibility']['percentage'], 1) ?>%)</p>
                            <?php if (isset($match['results']['tamil_compatibility'])): ?>
                                <p><strong>Tamil:</strong> <?= $match['results']['tamil_compatibility']['score'] ?>/<?= $match['results']['tamil_compatibility']['max_score'] ?> (<?= round($match['results']['tamil_compatibility']['percentage'], 1) ?>%)</p>
                            <?php endif; ?>
                            <p><strong>Astrology:</strong> <?= htmlspecialchars($match['results']['groom_astrology']['nakshatra']) ?> + <?= htmlspecialchars($match['results']['bride_astrology']['nakshatra']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
            
            <?php if (empty($recentMatches)): ?>
                <p>No matches found in database.</p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>