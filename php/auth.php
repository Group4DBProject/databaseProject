<?php
    require_once 'connection.php';

    function loginUser($pdo, $username, $password) {
        // Input validation for username and password
        if (empty($username) || empty($password)) {
            throw new InvalidArgumentException('Username and password are required.');
        }
    
        $stmt = $pdo->prepare("SELECT * FROM User_Credentials WHERE Username = :username");
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($user && $password == $user['Password']) {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['customer_id'] = $user['Customer_ID'];
            return true;
        } else {
            return false;
        }
    }
    
    function logoutUser() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
    }

    function isUserAuthorizedForAccount($pdo, $user_id, $account_id) {
        // Ensure user_id and account_id are numeric and positive
        if (!is_numeric($user_id) || $user_id <= 0 || !is_numeric($account_id) || $account_id <= 0) {
            throw new InvalidArgumentException('Invalid user or account holder ID.');
        }
        
        $stmt = $pdo->prepare("
            SELECT COUNT(*) 
            FROM Bank_Accounts
            WHERE Customer_ID = :user_id AND Account_ID = :account_id
        ");
        
        $stmt->execute([':user_id' => $user_id, ':account_id' => $account_id]);
        $isAuthorized = $stmt->fetchColumn();
    
        return $isAuthorized > 0;
    }
?>