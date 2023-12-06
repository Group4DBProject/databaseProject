<?php

    session_start();

    require 'connection.php';
    require 'operations.php';  

    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $phone_number = $_POST["phone_number"];
        $first_name = $_POST["first_name"];
        $last_name = $_POST["last_name"];
        $street = $_POST["street"];
        $city = $_POST["city"];
        $state = $_POST["state"];
        $zip = $_POST["zip"];
        $dob = $_POST["dob"];
        // $email = $_POST["email"];
        $username = $_POST["username"];
        $password = $_POST["password"];

        try{
            $bank_id = registerCustomer($pdo, $first_name, $last_name, $street, $city, $state, $zip, $phone_number, $dob, $username, $password);
            echo "User registered successfully.";
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }

    }
    else {
        echo "Invalid request.";
    }
    ?>