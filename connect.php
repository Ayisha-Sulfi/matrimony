<?php
include 'config.php';
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if(isset($_POST['receiver_id'])) {
    $sender_id = $_SESSION['user_id'];
    $receiver_id = $_POST['receiver_id'];
    
    // Check if connection already exists
    $check_query = "SELECT * FROM connections WHERE 
                    (sender_id = $sender_id AND receiver_id = $receiver_id) OR 
                    (sender_id = $receiver_id AND receiver_id = $sender_id)";
    $check_result = mysqli_query($conn, $check_query);
    
    if(mysqli_num_rows($check_result) == 0) {
        $query = "INSERT INTO connections (sender_id, receiver_id) VALUES ($sender_id, $receiver_id)";
        if(mysqli_query($conn, $query)) {
            header("Location: dashboard.php?msg=success");
        } else {
            header("Location: dashboard.php?msg=error");
        }
    } else {
        header("Location: dashboard.php?msg=exists");
    }
    exit();
}
?>