<?php
session_start();
include 'config.php';

// Redirect if not logged in or role not set
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit();
}

// Sorting
$sort_by = isset($_GET['sort']) ? $_GET['sort'] : 'expiry_time';
$allowed_sorts = ['expiry_time', 'created_at', 'food_type'];

if (!in_array($sort_by, $allowed_sorts)) {
    $sort_by = 'expiry_time';
}

// Fetch available food listings
$food_listings = [];
$error_message = ''; // only for SQL errors

try {
    $sql = "SELECT fl.*, u.name as donor_name, u.phone as donor_phone 
            FROM food_listings fl 
            JOIN users u ON fl.donor_id = u.id 
            WHERE fl.status = 'available' AND fl.expiry_time > NOW() 
            ORDER BY fl." . $sort_by;

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $food_listings = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch(PDOException $e) {
    $error_message = "Error loading food listings.";
}

// Urgency calculation
function getUrgencyLevel($expiry_time) {
    $now = new DateTime();
    $expiry = new DateTime($expiry_time);
    $diff = $now->diff($expiry);
    $hours = ($diff->days * 24) + $diff->h;

    if ($hours <= 2) return 'high';
    if ($hours <= 6) return 'medium';
    return 'low';
}

function timeUntilExpiry($expiry_time) {
    $now = new DateTime();
    $expiry = new DateTime($expiry_time);
    $diff = $now->diff($expiry);

    if ($diff->days > 0) {
        return $diff->days . ' day' . ($diff->days > 1 ? 's' : '') . ' left';
    } elseif ($diff->h > 0) {
        return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' left';
    } else {
        return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' left';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Browse Food - ShareFood</title>
<style>
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
    padding: 20px;
    border-radius: 15px;
    margin-bottom: 20px;
    text-align: center;
    box-shadow: 0 8px 32px rgba(0,0,0,0.1);
    position: relative;
}
.logout {
    position: absolute;
    top: 20px;
    right: 20px;
    font-weight: bold;
}
.logout a {
    color: #667eea;
    text-decoration: none;
}
.logout a:hover {
    text-decoration: underline;
}
table {
    width: 100%;
    background: #fff;
    border-collapse: collapse;
    border-radius: 10px;
    overflow: hidden;
}
table th, table td {
    padding: 12px;
    border: 1px solid #ddd;
    text-align: left;
}
table th {
    background: #667eea;
    color: white;
}
.urgency-high { color: red; font-weight: bold; }
.urgency-medium { color: orange; font-weight: bold; }
.urgency-low { color: green; font-weight: bold; }
button.claim-btn {
    padding: 8px 12px;
    border: none;
    border-radius: 5px;
    background-color: #667eea;
    color: white;
    cursor: pointer;
}
button.claim-btn:hover {
    background-color: #764ba2;
}
.alert {
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}
.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Browse Available Food</h1>
        <div class="logout">
            Logged in as: <?php echo htmlspecialchars($_SESSION['user_name']); ?> 
            (<?php echo htmlspecialchars($_SESSION['role']); ?>) |
            <a href="logout.php">Logout</a>
            <a href="index.php">Home</a>
        </div>
    </div>

    <!-- Display claim messages from session -->
    <?php if (!empty($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo htmlspecialchars($_SESSION['success']); 
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <?php 
                echo htmlspecialchars($_SESSION['error']); 
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <!-- Display SQL error if fetching food fails -->
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error_message); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($food_listings)): ?>
        <table>
            <thead>
                <tr>
                    <th>Food Type</th>
                    <th>Quantity</th>
                    <th>Expiry Time</th>
                    <th>Urgency</th>
                    <th>Donor</th>
                    <th>Phone</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($food_listings as $food): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($food['food_type']); ?></td>
                        <td><?php echo htmlspecialchars($food['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($food['expiry_time']); ?> (<?php echo timeUntilExpiry($food['expiry_time']); ?>)</td>
                        <td class="urgency-<?php echo getUrgencyLevel($food['expiry_time']); ?>">
                            <?php echo ucfirst(getUrgencyLevel($food['expiry_time'])); ?>
                        </td>
                        <td><?php echo htmlspecialchars($food['donor_name']); ?></td>
                        <td><?php echo htmlspecialchars($food['donor_phone']); ?></td>
                        <td>
                            <?php if ($_SESSION['role'] === 'receiver'): ?>
                                <form action="claim_food.php" method="post" style="margin:0;">
                                    <input type="hidden" name="listing_id" value="<?php echo $food['id']; ?>">
                                    <button type="submit" class="claim-btn">Claim</button>
                                </form>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p style="color:white; text-align:center;">No food listings available right now.</p>
    <?php endif; ?>
</div>
</body>
</html>
