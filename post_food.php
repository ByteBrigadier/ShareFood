<?php
include 'config.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

if (!isDonor()) {
    redirect('dashboard.php');
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $food_type = trim($_POST['food_type'] ?? '');
    $quantity = trim($_POST['quantity'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $expiry_time = $_POST['expiry_time'] ?? '';
    $pickup_location = trim($_POST['pickup_location'] ?? '');
    
    if (!csrfValid()) {
        $error_message = "Invalid request. Please refresh the page and try again.";
    } elseif (empty($food_type) || empty($quantity) || empty($expiry_time) || empty($pickup_location)) {
        $error_message = "Please fill in all required fields.";
    } elseif (strtotime($expiry_time) <= time()) {
        $error_message = "Expiry time must be in the future.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO food_listings (donor_id, food_type, quantity, description, expiry_time, pickup_location) VALUES (?, ?, ?, ?, ?, ?)");
            
            if ($stmt->execute([$_SESSION['user_id'], $food_type, $quantity, $description, $expiry_time, $pickup_location])) {
                $success_message = "Food posted successfully! It's now visible to receivers.";
                $_POST = array();
            } else {
                $error_message = "Failed to post food. Please try again.";
            }
            
        } catch(PDOException $e) {
            $error_message = "Error posting food. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Food - ShareFood</title>
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
            max-width: 600px;
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

        .form-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
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

        .nav-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .btn-link {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }

        .btn-link:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(240, 147, 251, 0.3);
        }

        .help-text {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }

        .required {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>➕ Post Surplus Food</h1>
            <p>Help reduce food waste by sharing your surplus food with the community</p>
        </div>

        <div class="form-container">
            <?php if ($error_message): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <?php echo csrfField(); ?>
                <div class="form-group">
                    <label for="food_type">Food Type <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="food_type" 
                        name="food_type" 
                        required 
                        value="<?php echo isset($_POST['food_type']) ? htmlspecialchars($_POST['food_type']) : ''; ?>"
                        placeholder="e.g., Cooked Rice, Fresh Bread, Mixed Vegetables"
                    >
                    <div class="help-text">Describe the type of food you're sharing</div>
                </div>
                
                <div class="form-group">
                    <label for="quantity">Quantity <span class="required">*</span></label>
                    <input 
                        type="text" 
                        id="quantity" 
                        name="quantity" 
                        required 
                        value="<?php echo isset($_POST['quantity']) ? htmlspecialchars($_POST['quantity']) : ''; ?>"
                        placeholder="e.g., 10 servings, 5 kg, 20 pieces"
                    >
                    <div class="help-text">Specify how much food is available</div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        placeholder="Additional details about the food (optional)"
                    ><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                    <div class="help-text">Any additional information about the food (ingredients, preparation method, etc.)</div>
                </div>
                
                <div class="form-group">
                    <label for="expiry_time">Safe Until (Date & Time) <span class="required">*</span></label>
                    <input 
                        type="datetime-local" 
                        id="expiry_time" 
                        name="expiry_time" 
                        required 
                        value="<?php echo isset($_POST['expiry_time']) ? htmlspecialchars($_POST['expiry_time']) : ''; ?>"
                        min="<?php echo date('Y-m-d\TH:i'); ?>"
                    >
                    <div class="help-text">When is the food safe to consume until?</div>
                </div>
                
                <div class="form-group">
                    <label for="pickup_location">Pickup Location <span class="required">*</span></label>
                    <textarea 
                        id="pickup_location" 
                        name="pickup_location" 
                        required 
                        placeholder="Enter complete address where food can be picked up"
                    ><?php echo isset($_POST['pickup_location']) ? htmlspecialchars($_POST['pickup_location']) : ''; ?></textarea>
                    <div class="help-text">Provide the complete address where receivers can pickup the food</div>
                </div>
                
                <button type="submit" class="btn">📤 Post Food</button>
            </form>
            
            <div class="nav-buttons">
                <a href="dashboard.php" class="btn-link">📊 Back to Dashboard</a>
                <a href="browse_food.php" class="btn-link">🔍 Browse Food</a>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
            document.getElementById('expiry_time').min = now.toISOString().slice(0, 16);
        });
    </script>
</body>
</html>