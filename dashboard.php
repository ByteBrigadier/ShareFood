<?php
include 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$user_listings = [];
$user_claims = [];

try {
    if (isDonor()) {
        $stmt = $pdo->prepare("SELECT * FROM food_listings WHERE donor_id = ? ORDER BY created_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $user_listings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->prepare("
            SELECT fl.*, u.name as donor_name, fc.claimed_at 
            FROM food_claims fc 
            JOIN food_listings fl ON fc.food_id = fl.id 
            JOIN users u ON fl.donor_id = u.id 
            WHERE fc.receiver_id = ? 
            ORDER BY fc.claimed_at DESC
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $user_claims = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch(PDOException $e) {
    $error_message = "Error loading dashboard data.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ShareFood</title>
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
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        .header h1 {
            color: #333;
            margin-bottom: 10px;
        }

        .user-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
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
            padding: 12px 24px;
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

        .content-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        .content-section h2 {
            color: #333;
            margin-bottom: 20px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }

        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .item-card {
            background: white;
            border: 1px solid #e1e5e9;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #667eea;
        }

        .item-card h3 {
            color: #333;
            margin-bottom: 15px;
            font-size: 1.3em;
        }

        .item-card p {
            color: #666;
            margin-bottom: 8px;
            line-height: 1.5;
        }

        .item-card p strong {
            color: #333;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-available { 
            background: #d4edda; 
            color: #155724; 
        }
        
        .status-claimed { 
            background: #fff3cd; 
            color: #856404; 
        }
        
        .status-picked_up { 
            background: #f8d7da; 
            color: #721c24; 
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }

        .empty-state img {
            width: 100px;
            height: 100px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .stat-card h3 {
            font-size: 2em;
            margin-bottom: 5px;
        }

        @media (max-width: 768px) {
            .nav-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .items-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Your Dashboard</h1>
            
            <div class="user-info">
                <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</h2>
                <p>Account Type: <strong><?php echo ucfirst($_SESSION['role']); ?></strong></p>
                <p>Email: <?php echo htmlspecialchars($_SESSION['user_email']); ?></p>
            </div>
            
            <div class="nav-buttons">
                <a href="index.php" class="btn">🏠 Home</a>
                <?php if (isDonor()): ?>
                    <a href="post_food.php" class="btn">➕ Post New Food</a>
                <?php endif; ?>
                <a href="browse_food.php" class="btn">🔍 Browse Food</a>
                <a href="logout.php" class="btn btn-secondary">🚪 Logout</a>
            </div>
        </div>

        <div class="content-section">
            <?php if (isDonor()): ?>
                <h2>Your Food Listings</h2>
                
                <?php if (empty($user_listings)): ?>
                    <div class="empty-state">
                        <h3>No food listings yet</h3>
                        <p>Start sharing surplus food with your community!</p>
                        <a href="post_food.php" class="btn" style="margin-top: 20px;">➕ Post Your First Food</a>
                    </div>
                <?php else: ?>
                    <!-- Show statistics -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3><?php echo count($user_listings); ?></h3>
                            <p>Total Listings</p>
                        </div>
                        <div class="stat-card">
                            <h3><?php echo count(array_filter($user_listings, function($item) { return $item['status'] == 'available'; })); ?></h3>
                            <p>Available</p>
                        </div>
                        <div class="stat-card">
                            <h3><?php echo count(array_filter($user_listings, function($item) { return $item['status'] == 'claimed'; })); ?></h3>
                            <p>Claimed</p>
                        </div>
                    </div>
                    
                    <!-- Show all listings -->
                    <div class="items-grid">
                        <?php foreach ($user_listings as $listing): ?>
                            <div class="item-card">
                                <h3><?php echo htmlspecialchars($listing['food_type']); ?></h3>
                                <p><strong>Quantity:</strong> <?php echo htmlspecialchars($listing['quantity']); ?></p>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($listing['description'] ?: 'No description'); ?></p>
                                <p><strong>Expires:</strong> <?php echo date('M j, Y g:i A', strtotime($listing['expiry_time'])); ?></p>
                                <p><strong>Location:</strong> <?php echo htmlspecialchars($listing['pickup_location']); ?></p>
                                <p><strong>Posted:</strong> <?php echo date('M j, Y g:i A', strtotime($listing['created_at'])); ?></p>
                                <p><strong>Status:</strong> <span class="status-badge status-<?php echo $listing['status']; ?>"><?php echo ucfirst($listing['status']); ?></span></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <h2>Your Claimed Food</h2>
                
                <?php if (empty($user_claims)): ?>
                    <div class="empty-state">
                        <h3>No claimed food yet</h3>
                        <p>Browse available food in your area and start claiming!</p>
                        <a href="browse_food.php" class="btn" style="margin-top: 20px;">🔍 Browse Available Food</a>
                    </div>
                <?php else: ?>
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h3><?php echo count($user_claims); ?></h3>
                            <p>Total Claims</p>
                        </div>
                        <div class="stat-card">
                            <h3><?php echo count(array_filter($user_claims, function($item) { return $item['status'] == 'claimed'; })); ?></h3>
                            <p>Pending Pickup</p>
                        </div>
                        <div class="stat-card">
                            <h3><?php echo count(array_filter($user_claims, function($item) { return $item['status'] == 'picked_up'; })); ?></h3>
                            <p>Completed</p>
                        </div>
                    </div>
                    
                    <div class="items-grid">
                        <?php foreach ($user_claims as $claim): ?>
                            <div class="item-card">
                                <h3><?php echo htmlspecialchars($claim['food_type']); ?></h3>
                                <p><strong>Quantity:</strong> <?php echo htmlspecialchars($claim['quantity']); ?></p>
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($claim['description'] ?: 'No description'); ?></p>
                                <p><strong>Donor:</strong> <?php echo htmlspecialchars($claim['donor_name']); ?></p>
                                <p><strong>Pickup Location:</strong> <?php echo htmlspecialchars($claim['pickup_location']); ?></p>
                                <p><strong>Expires:</strong> <?php echo date('M j, Y g:i A', strtotime($claim['expiry_time'])); ?></p>
                                <p><strong>Claimed On:</strong> <?php echo date('M j, Y g:i A', strtotime($claim['claimed_at'])); ?></p>
                                <p><strong>Status:</strong> <span class="status-badge status-<?php echo $claim['status']; ?>"><?php echo ucfirst(str_replace('_', ' ', $claim['status'])); ?></span></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>