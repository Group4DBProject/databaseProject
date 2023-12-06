<!-- creating a bank branch -->
<?php

    session_start();

    require 'connection.php';
    require 'operations.php'; 
    require 'auth.php'; 

    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $customer_id = $_POST["customer_id"];
        $bank_id = $_POST["bank_id"];
        $account_type = $_POST["account_type"];
        $balance = $_POST["balance"];

        
        if (isUserAuthorizedForAccount($pdo, $customer_id, -1, "ADMIN")) {
            try{
                $account_id = createAccount($pdo, $customer_id, $bank_id, $account_type, $balance);
                echo "Bank created successfully.";
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }

        }
        else {
            echo "You are not authorized to perform this action.";
        }
    }else {
        echo "Invalid request.";
    }
    ?>