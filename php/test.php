<?php
session_start();

require 'connection.php';
require 'operations.php';
require 'auth.php';

// Create Bank Branch
$bank_id = createBank($pdo, "1234567890", "Bank of America", "123 Main St", "San Francisco", "CA", "94105");
echo "Created Bank Id: " . $bank_id ." \n";

// Register customer
$customer_id = registerCustomer($pdo, "John", "Doe", "123 Main St", "San Francisco", "CA", "94105", "1234567890", "1990-01-01", "johndoe", "testpassword");
echo "Registered Customer: " . $customer_id ." \n";

$admin_username = "admin";
$admin_password = "adminpassword";
$account_id = NULL;
$recipient_id = NULL;

if (loginUser($pdo, $admin_username, $admin_password)) {
    echo "Admin Login successful.\n";
    // create bank account
    $account_id = createAccount($pdo, $customer_id, $bank_id, "Checking", 1000);
    echo "Created Account: " . $account_id ." \n";
    echo "Current Balance: " . get_account_balance($pdo, $account_id) ." \n";

    echo "Depositing 150 to account: " . $account_id ." \n";
    deposit($pdo, $account_id, 150);
    echo "New Account Balance: " . get_account_balance($pdo, $account_id) ." \n";

    echo "Withdrawing 650 from account: " . $account_id ." \n";
    withdraw($pdo, $account_id, 650);
    echo "New Account Balance: " . get_account_balance($pdo, $account_id) ." \n";

    // Simulate a transfer
    $recipient_id = createAccount($pdo, $customer_id, $bank_id, "Savings", 250);
    $amount = 250;
    echo "Recipient: " . $recipient_id ."\n";
    echo "Recipient Balance: " . get_account_balance($pdo, $recipient_id) ." \n";
}

    // Simulate user login
$username = "johndoe";
$password = "testpassword";

if (loginUser($pdo, $username, $password)) {
    echo "User Login successful.\n";

    echo "Transferring $amount from $account_id to account: " . $recipient_id ." \n";

    if (isUserAuthorizedForAccount($pdo, $_SESSION['customer_id'], $account_id)) {
        try {
            transfer($pdo, $account_id, $recipient_id, $amount);
            echo "Transfer of $amount to account $recipient_id completed successfully.\n";
        } catch (Exception $e) {
            echo "Error during transfer: " . $e->getMessage();
        }
    } else {
        echo "User not authorized to perform this transfer.";
    }

    echo "Sender Balance: " . get_account_balance($pdo, $account_id) ." \n";
    echo "Recipient Balance: " . get_account_balance($pdo, $recipient_id) ." \n";

} else {
    echo "Login failed.";
}

logoutUser();
?>
