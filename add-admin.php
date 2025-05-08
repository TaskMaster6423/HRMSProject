<?php
include_once("includes/config.php");

// Generate hashed password
$password = "admin";
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// SQL to insert new admin user
$sql = "INSERT INTO users (FirstName, LastName, UserName, Email, Password, Phone, Address, Picture, dateTime) 
        VALUES ('Admin', 'User', 'admin', 'admin@example.com', :password, '1234567890', 'Admin Address', 'avatar-01.jpg', NOW())";

try {
    $query = $dbh->prepare($sql);
    $query->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
    $query->execute();
    
    echo "New admin user created successfully!<br>";
    echo "Username: admin<br>";
    echo "Password: admin";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 