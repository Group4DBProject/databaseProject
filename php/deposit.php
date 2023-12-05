<?php
session_start();

require 'connection.php';
require 'operations.php';  

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php'); // Redirect to login page
    exit;
}

// Check if the form data is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $amount = $_POST['amount'];
    $user_id = $_SESSION['customer_id']; // Get the logged-in user's ID
    $account_id = $_POST['account_id'];

    // Authenticate user for the account
    if (isUserAuthorizedForAccount($pdo, $user_id, $account_id, "Deposit")) {
        // Call the transfer function
        try {
            deposit($pdo, $account_id, $amount);
            echo "Funds deposited successfully.";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "You are not authorized to perform this action.";
    }
} else {
    echo "Invalid request.";
}
?>
