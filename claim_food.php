<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'receiver') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['listing_id'])) {
    $listing_id = (int) $_POST['listing_id'];
    $receiver_id = $_SESSION['user_id'];

    try {
        $check = $pdo->prepare("SELECT status FROM food_listings WHERE id = ? AND status = 'available'");
        $check->execute([$listing_id]);
        if ($check->rowCount() === 0) {
            $_SESSION['error'] = "Food already claimed or unavailable.";
            header("Location: browse_food.php");
            exit();
        }

        $insert = $pdo->prepare("INSERT INTO food_claims (food_id, receiver_id) VALUES (?, ?)");
        $insert->execute([$listing_id, $receiver_id]);

        $update = $pdo->prepare("UPDATE food_listings SET status = 'claimed' WHERE id = ?");
        $update->execute([$listing_id]);

        $_SESSION['success'] = "Food claimed successfully!";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error claiming food.";
    }
}

header("Location: browse_food.php");
exit();
?>
