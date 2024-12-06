<?php
// Start the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: login.php");
    exit;
}

// Access user details from session
$adminId = $_SESSION["id"];
$username = $_SESSION["username"];  
$userAddress = isset($_SESSION["address"]) ? $_SESSION["address"] : "";
$userEmail = isset($_SESSION["email"]) ? $_SESSION["email"] : "";
$userPhone = isset($_SESSION["phone"]) ? $_SESSION["phone"] : "";

// Now, $userId, $userAddress, $userEmail, and $userPhone can be used to fetch user-specific data or perform actions
?>
