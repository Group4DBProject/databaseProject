CREATE DATABASE IF NOT EXISTS BankingSystem;
USE BankingSystem;

-- Table for storing customer information
CREATE TABLE IF NOT EXISTS Customers (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    First_name VARCHAR(100),
    Last_name VARCHAR(100),
    Street VARCHAR(255),
    City VARCHAR(100),
    State VARCHAR(100),
    Type ENUM("ADMIN", "USER"),
    Zip_code VARCHAR(10),
    Phone_number VARCHAR(15) UNIQUE NOT NULL,
    Date_of_birth DATE
);

 -- INserting Admin user into customers table
INSERT INTO Customers (
    First_name,
    Last_name,
    Street,
    City, 
    State, 
    Type, 
    Zip_code, 
    Phone_number, 
    Date_of_birth
    ) VALUES (
        'Admin',
        'Admin',
        '123 Admin Street',
        'Admin City',
        'Admin State',
        'ADMIN',
        '12345',
        '8473838392',
        '1990-01-01'
        );

-- Table for storing bank information
CREATE TABLE IF NOT EXISTS Banks (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    Phone_number VARCHAR(15) UNIQUE NOT NULL,
    Street VARCHAR(255),
    City VARCHAR(100),
    State VARCHAR(100),
    Zip_code VARCHAR(10),
    Bank_Name VARCHAR(100)
);

-- Table for storing bank account information
CREATE TABLE IF NOT EXISTS Bank_Accounts (
    Account_ID INT PRIMARY KEY AUTO_INCREMENT,
    Customer_ID INT,
    Bank_ID INT,
    Type ENUM('Savings', 'Checking'),
    Balance DECIMAL(10, 2),
    FOREIGN KEY (Customer_ID) REFERENCES Customers(ID),
    FOREIGN KEY (Bank_ID) REFERENCES Banks(ID)
);

-- Table for storing transactions
CREATE TABLE IF NOT EXISTS Transactions (
    Transaction_ID INT PRIMARY KEY AUTO_INCREMENT,
    Account_ID INT,
    Date DATETIME,
    Amount DECIMAL(10, 2),
    Transaction_Type ENUM('Deposit', 'Withdrawal', 'Transfer'),
    Recipient_Account_ID INT,
    FOREIGN KEY (Account_ID) REFERENCES Bank_Accounts(Account_ID),
    FOREIGN KEY (Recipient_Account_ID) REFERENCES Bank_Accounts(Account_ID)
);

-- Table for storing user login credentials
CREATE TABLE IF NOT EXISTS User_Credentials (
    Credential_ID INT PRIMARY KEY AUTO_INCREMENT,
    Customer_ID INT,
    Username VARCHAR(50) UNIQUE NOT NULL,
    Password VARCHAR(255) NOT NULL,
    FOREIGN KEY (Customer_ID) REFERENCES Customers(ID)
);

-- Inserting ADMIN credentials into User_Credentials table
INSERT INTO User_Credentials(
    Customer_ID,
    Username,
    Password
    ) VALUES (
        1,
        'admin',
        'adminpassword'
)