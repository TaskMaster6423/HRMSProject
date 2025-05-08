<?php
// Generate a new hashed password
$newPassword = "admin123"; // This will be the new password
$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
echo "New password: " . $newPassword . "\n";
echo "Hashed password: " . $hashedPassword;
?> 