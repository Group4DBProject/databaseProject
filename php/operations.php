<?php
    require_once 'connection.php';

    function createBank($pdo, $phone_number, $bank_name, $street, $city, $state, $zip_code){
        // Validate phone number format
        if (!preg_match('/^\+?\d{10,15}$/', $phone_number)) {
            throw new InvalidArgumentException('Invalid phone number format.');
        }
    
        // Basic input checks for address and bank name
        if (empty($bank_name) || empty($street) || empty($city) || empty($state) || empty($zip_code)) {
            throw new InvalidArgumentException('Full address and bank name are required.');
        }
    
        $stmt = $pdo->prepare("INSERT INTO Banks (Phone_number, Bank_name, Street, City, State, Zip_code) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$phone_number, $bank_name, $street, $city, $state, $zip_code]);
        return $pdo->lastInsertId();
    }

    function registerCustomer($pdo, $first_name, $last_name, $street, $city, $state, $zip_code, $phone_number, $date_of_birth, $username, $password){
        // Validate phone number format
        if (!preg_match('/^\+?\d{10,15}$/', $phone_number)) {
            throw new InvalidArgumentException('Invalid phone number format.');
        }
    
        // Basic input checks for address and bank name
        if (empty($first_name) || empty($last_name) || empty($date_of_birth) || empty($street) || empty($city) || empty($state) || empty($zip_code) || empty($phone_number) || empty($username) || empty($password)){
            throw new InvalidArgumentException('No empty field allowed.');
        }
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("INSERT INTO Customers (First_name, Last_name, Street, City, State, Zip_code, Phone_number, Date_of_birth, Type ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'USER' )");
            $stmt->execute([$first_name, $last_name, $street, $city, $state, $zip_code, $phone_number, $date_of_birth]);
            $customer_id = $pdo->lastInsertId();
            $stmt = $pdo->prepare("INSERT INTO User_Credentials (Customer_ID, Username, Password) VALUES (?, ?, ?)");
            $stmt->execute([$customer_id, $username, $password]);
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollback();
            throw $e;
        }
        return $customer_id;
    }

    function createAccount($pdo, $customer_id, $bank_id, $type, $balance) {
        // Validate account type
        if (!in_array($type, ['Savings', 'Checking'])) {
            throw new InvalidArgumentException('Invalid account type.');
        }
    
        // Check that balance is a number and is not negative
        if (!is_numeric($balance) || $balance < 0) {
            throw new InvalidArgumentException('Balance must be a positive number.');
        }
    
        $stmt = $pdo->prepare("INSERT INTO Bank_Accounts (Customer_ID, Bank_ID, Type, Balance) VALUES (?, ?, ?, ?)");
        $stmt->execute([$customer_id, $bank_id, $type, $balance]);
        return $pdo->lastInsertId();
    }

    function deposit($pdo, $account_id, $amount) {
        // Ensure amount is numeric and greater than zero
        if (!is_numeric($amount) || $amount <= 0) {
            throw new InvalidArgumentException('Deposit amount must be greater than zero.');
        }
    
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("UPDATE Bank_Accounts SET Balance = Balance + ? WHERE Account_Id = ?");
            $stmt->execute([$amount, $account_id]);
    
            $stmt = $pdo->prepare("INSERT INTO Transactions (Account_Id, Date, Amount, Transaction_Type) VALUES (?, NOW(), ?, 'Deposit')");
            $stmt->execute([$account_id, $amount]);
    
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollback();
            throw $e;
        }
    }

    function withdraw($pdo, $account_id, $amount) {
        // Ensure amount is numeric and greater than zero
        if (!is_numeric($amount) || $amount <= 0) {
            throw new InvalidArgumentException('Withdrawal amount must be greater than zero.');
        }
    
        $pdo->beginTransaction();
        try {
            $stmt = $pdo->prepare("SELECT Balance FROM Bank_Accounts WHERE Account_ID = ?");
            $stmt->execute([$account_id]);
            $balance = $stmt->fetchColumn();
    
            if ($balance < $amount) {
                $pdo->rollback();
                throw new Exception('Insufficient funds.');
            }
    
            $stmt = $pdo->prepare("UPDATE Bank_Accounts SET Balance = Balance - ? WHERE Account_ID = ?");
            $stmt->execute([$amount, $account_id]);
    
            $stmt = $pdo->prepare("INSERT INTO Transactions (Account_ID, Date, Amount, Transaction_Type) VALUES (?, NOW(), ?, 'Withdrawal')");
            $stmt->execute([$account_id, $amount]);
    
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollback();
            throw $e;
        }
    }
    
    function transfer($pdo, $from_account_id, $to_account_id, $amount) {
        // Ensure amount is numeric and greater than zero
        if (!is_numeric($amount) || $amount <= 0) {
            throw new InvalidArgumentException('Transfer amount must be greater than zero.');
        }
    
        $pdo->beginTransaction();
        try {
            // Check sender balance
            $stmt = $pdo->prepare("SELECT Balance FROM Bank_Accounts WHERE Account_ID = ?");
            $stmt->execute([$from_account_id]);
            $sender_balance = $stmt->fetchColumn();
    
            if ($sender_balance < $amount) {
                $pdo->rollback();
                throw new Exception('Insufficient funds for transfer.');
            }
    
            // Subtract from sender
            $stmt = $pdo->prepare("UPDATE Bank_Accounts SET Balance = Balance - ? WHERE Account_ID = ?");
            $stmt->execute([$amount, $from_account_id]);
    
            // Add to recipient
            $stmt = $pdo->prepare("UPDATE Bank_Accounts SET Balance = Balance + ? WHERE Account_ID = ?");
            $stmt->execute([$amount, $to_account_id]);
    
            // Insert the transaction record
            $stmt = $pdo->prepare("INSERT INTO Transactions (Account_ID, Recipient_Account_ID, Date, Amount, Transaction_Type) VALUES (?, ?, NOW(), ?, 'Transfer')");
            $stmt->execute([$from_account_id, $to_account_id, $amount]);
    
            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollback();
            throw $e;
        }
    }

    function get_account_balance($pdo, $account_id) {
        $stmt = $pdo->prepare("SELECT Balance FROM Bank_Accounts WHERE Account_ID = ?");
        $stmt->execute([$account_id]);
        $balance = $stmt->fetchColumn();
        return $balance;
    }

    function get_customers_accounts($pdo, $customer_id) {
        $stmt = $pdo->prepare("SELECT * FROM Bank_Accounts WHERE Customer_ID = ?");
        $stmt->execute([$customer_id]);
        $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $accounts;
    }

    function get_customers_transactions($pdo, $customer_id) {
        $stmt = $pdo->prepare("SELECT * FROM Bank_Accounts WHERE Customer_ID = ?");
        $stmt->execute([$customer_id]);
        $accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $transactions = array();
        foreach ($accounts as $account) {
            $stmt = $pdo->prepare("SELECT * FROM Transactions WHERE Account_ID = ? OR Recipient_Account_ID = ?");
            $stmt->execute([$account['Account_ID'], $account['Account_ID']]);
            $transactions[$account['Account_ID']] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        return $transactions;
    }


?>