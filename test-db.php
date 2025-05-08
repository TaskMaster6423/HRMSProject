<?php
include_once("includes/config.php");

try {
    // Test database connection
    echo "Testing database connection...<br>";
    $dbh->query("SELECT 1");
    echo "Database connection successful!<br><br>";
    
    // Check users table
    echo "Checking users table...<br>";
    $sql = "SELECT * FROM users";
    $query = $dbh->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    
    echo "Number of users found: " . count($results) . "<br>";
    echo "Users in database:<br>";
    foreach ($results as $user) {
        echo "Username: " . $user->UserName . "<br>";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 