<?php
include 'config.php';

if (!isLoggedIn() || !isReceiver()) {
    redirect('login.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['listing_id'])) {
    if (!csrfValid()) {
        $_SESSION['error'] = "Invalid request. Please try again.";
        redirect('browse_food.php');
    }

    $listing_id = (int) $_POST['listing_id'];
    $receiver_id = $_SESSION['user_id'];

    try {
        $pdo->beginTransaction();

        // Atomic claim: only one receiver can flip a listing from 'available' to 'claimed'
        $update = $pdo->prepare("UPDATE food_listings SET status = 'claimed' WHERE id = ? AND status = 'available' AND expiry_time > NOW()");
        $update->execute([$listing_id]);

        if ($update->rowCount() === 0) {
            $pdo->rollBack();
            $_SESSION['error'] = "Food already claimed, expired or unavailable.";
            redirect('browse_food.php');
        }

        $insert = $pdo->prepare("INSERT INTO food_claims (food_id, receiver_id) VALUES (?, ?)");
        $insert->execute([$listing_id, $receiver_id]);

        $pdo->commit();
        $_SESSION['success'] = "Food claimed successfully!";
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        error_log('Claim failed: ' . $e->getMessage());
        $_SESSION['error'] = "Error claiming food.";
    }
}

redirect('browse_food.php');
?>
