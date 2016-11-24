<?php
$conn = new mysqli("localhost", "root", "", "courses_db");
if($conn->connect_error){
	die("connection failed: " . $conn->connect_error);
}
?>