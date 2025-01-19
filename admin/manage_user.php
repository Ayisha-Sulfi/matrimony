<?php
include '../config.php';
session_start();

if(!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../login.php");
}

if(isset($_GET['action']) && isset($_GET['user_id'])) {
    $action = $_GET['action'];
    $user_id = $_GET['user_id'];
    
    if($action == 'delete') {
        $query = "DELETE FROM users WHERE id=$user_id";
        mysqli_query($conn, $query);
    }
}

header("Location: dashboard.php");
?>