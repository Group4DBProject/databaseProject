<?php
    session_start();
    if(!isset($_SESSION['customer_id'])) {
        header("Location: index.html");
        exit();
    }
    require '../php/connection.php';
    require '../php/auth.php';
    require '../php/operations.php';

    $user_id = $_SESSION['customer_id'];
    $accounts = get_customers_accounts($pdo, $user_id);
    $transactions = get_customers_transactions($pdo, $user_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Account</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }

        h2 {
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            background-color: #4caf50;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>User Account</h2>
        <?php
            echo "<h3>Accounts:</h3>";
            echo "<ul>";

            foreach($accounts as $account){
                echo "<li>Account ID: " . $account['Account_ID'] . " | Bank ID: " . $account['Bank_ID'] . " | Account Type: " . $account['Type'] . " | Balance: " . $account['Balance'] . "</li>";
            }
            echo "</ul>";
        ?>

        <h2>Transactions: </h2>
        <?php
            echo "<ul>";
            foreach($transactions as $account_id => $account_transactions){
                echo "<li>Account ID: " . $account_id . "</li>";
                echo "<ul>";
                foreach($account_transactions as $transaction){
                    echo "<li>Transaction ID: " . $transaction['Transaction_ID'] . " | Date: " . $transaction['Date'] . " | Amount: " . $transaction['Amount'] . " | Transaction Type: " . $transaction['Transaction_Type'] . "</li>";
                }
                echo "</ul>";
            }
            echo "</ul>";
        ?>

        <h2>Transfer</h2>
        <form action="../php/transfer.php" method="POST">
            <label for="account_id">From Account:</label>
            <select id="fromAccount" name="account_id">
                <?php
                    foreach($accounts as $account){
                        echo "<option value='" . $account['Account_ID'] . "'>" . $account['Account_ID'] . "</option>";
                    }
                ?>
            </select>
            <label for="amount">Transfer Amount:</label>
            <input type="number" id="amount" name="amount" required>

            <label for="recipient_id">Recipient Account:</label>
            <input type="text" id="recipient_id" name="recipient_id" required>

            <button type="submit">Transfer</button>
        </form>
    </div>
</body>
</html>