<?php
// File: generate_password.php
$password = 'rional123'; // Password yang Anda inginkan
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: " . $password . "<br>";
echo "Hash: " . $hash . "<br><br>";

echo "<strong>Copy query di bawah ini dan jalankan di phpMyAdmin:</strong><br>";
echo "<textarea style='width:100%; height:100px;'>";
echo "DELETE FROM admin;\n";
echo "INSERT INTO admin (username, password, nama) VALUES ('admin', '$hash', 'Administrator');";
echo "</textarea>";
?>
