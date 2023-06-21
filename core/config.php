<?php 

$servername = "localhost"; // Your mysql hostname
$username = "root"; // Your mysql username
$password = "mysql"; // Your mysql password
$dbname = "urlz"; // You mysql database name

$conn = new PDO("mysql:host=$servername; dbname=$dbname",$username,$password);

?>
