<?php
include_once("includes/config.php");

try {
    $sql = "SELECT * FROM users";
    $query = $dbh->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    
    echo "<h2>Users in Database:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Password Hash</th></tr>";
    
    foreach ($results as $user) {
        echo "<tr>";
        echo "<td>" . $user->id . "</td>";
        echo "<td>" . $user->UserName . "</td>";
        echo "<td>" . $user->Email . "</td>";
        echo "<td>" . $user->Password . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 