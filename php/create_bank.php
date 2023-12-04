<!-- creating a bank branch -->
<?php

    session_start();

    require 'connection.php';
    require 'operations.php';  

    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $phone_number = $_POST["phone_number"];
        $bank_name = $_POST["bank_name"];
        $street = $_POST["street"];
        $city = $_POST["city"];
        $state = $_POST["state"];
        $zip = $_POST["zip"];

        try{
            $bank_id = createBank($pdo, $phone_number, $bank_name, $street, $city, $state, $zip);
            echo "Bank created successfully.";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

    }
    else {
        echo "Invalid request.";
    }
    ?>