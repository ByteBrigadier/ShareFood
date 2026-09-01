<?php
include 'config.php';

try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM food_listings");
    $total_listings = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM food_listings WHERE status = 'claimed'");
    $total_claimed = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    $total_users = $stmt->fetchColumn();
} catch(PDOException $e) {
    $total_listings = 0;
    $total_claimed = 0;
    $total_users = 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ShareFood - Reduce Food Waste</title>
<style>
* {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .header h1 {
            color: white;
            font-size: 3em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.2em;
            margin-bottom: 20px;
        }

        .nav-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            padding: 15px 30px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        .stat-card h3 {
            font-size: 2.5em;
            color: #667eea;
            margin-bottom: 10px;
        }

        .stat-card p {
            font-size: 1.1em;
            color: #666;
        }

        .welcome-section {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            text-align: center;
        }

        .welcome-section h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 2em;
        }

        .welcome-section p {
            font-size: 1.1em;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .user-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .nav-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .btn {
                width: 200px;
            }
            
            .header h1 {
                font-size: 2em;
            }
        }
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🍽️ ShareFood</h1>
        <p>Connect surplus food with those who need it</p>
        <p><strong>Reduce waste • Fight hunger • Build community</strong></p>

        <?php if (isLoggedIn()): ?>
            <div class="user-info">
                <h3>Welcome, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h3>
                <p>Account Type: <?php echo ucfirst($_SESSION['role']); ?></p>
            </div>

            <div class="nav-buttons">
                <a href="dashboard.php" class="btn">📊 Dashboard</a>
                <?php if (isDonor()): ?>
                    <a href="post_food.php" class="btn">➕ Post Food</a>
                <?php endif; ?>
                <a href="browse_food.php" class="btn">🔍 Browse Food</a>
                <a href="logout.php" class="btn btn-secondary">🚪 Logout</a>
            </div>
        <?php else: ?>
            <div class="nav-buttons">
                <a href="login.php?redirect=index.php" class="btn">🔐 Login</a>
                <a href="register.php" class="btn btn-secondary">📝 Register</a>
                <a href="browse_food.php" class="btn">🔍 Browse Food</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="stats">
        <div class="stat-card">
            <h3><?php echo $total_listings; ?></h3>
            <p>Food Listings</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $total_claimed; ?></h3>
            <p>Items Claimed</p>
        </div>
        <div class="stat-card">
            <h3><?php echo $total_users; ?></h3>
            <p>Registered Users</p>
        </div>
    </div>

    <div class="welcome-section">
        <h2>How ShareFood Works</h2>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-top: 30px;">
            <div>
                <h3 style="color: #667eea;">🏪 For Donors</h3>
                <p>Restaurants, households, and event organizers can post surplus food instead of throwing it away.</p>
            </div>
            <div>
                <h3 style="color: #667eea;">🤝 For Receivers</h3>
                <p>NGOs, charities, and individuals can browse and claim available food in their area.</p>
            </div>
            <div>
                <h3 style="color: #667eea;">🌍 Impact</h3>
                <p>Together we reduce food waste and help feed those in need in our community.</p>
            </div>
        </div>
    </div>
</div>
</body>
</html>