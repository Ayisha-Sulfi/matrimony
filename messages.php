<?php
include 'config.php';
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$receiver_id = $_GET['receiver_id'];

// Check if there's an approved connection
$connection_query = "SELECT * FROM connections WHERE 
                    ((sender_id = $user_id AND receiver_id = $receiver_id) OR 
                     (sender_id = $receiver_id AND receiver_id = $user_id)) AND 
                    status = 'accepted'";
$connection_result = mysqli_query($conn, $connection_query);

if(mysqli_num_rows($connection_result) == 0) {
    header("Location: dashboard.php?msg=no_connection");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['message'])) {
    $message = mysqli_real_escape_string($conn, $_POST['message']);
    $query = "INSERT INTO messages (sender_id, receiver_id, message) 
              VALUES ($user_id, $receiver_id, '$message')";
    mysqli_query($conn, $query);
}

// Get receiver details
$receiver_query = "SELECT full_name, profile_pic FROM users WHERE id = $receiver_id";
$receiver_result = mysqli_query($conn, $receiver_query);
$receiver = mysqli_fetch_assoc($receiver_result);

// Get messages
$messages_query = "SELECT * FROM messages 
                  WHERE (sender_id = $user_id AND receiver_id = $receiver_id) 
                  OR (sender_id = $receiver_id AND receiver_id = $user_id) 
                  ORDER BY created_at";
$messages_result = mysqli_query($conn, $messages_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Messages - Matrimony</title>
    <link rel="stylesheet" href="styles.css">
  
</head>
<body>
    <nav class="navbar">
        <h1>Matrimony</h1>
        <div class="nav-links">
            <a href="dashboard.php">Home</a>
            <a href="profile.php">My Profile</a>
            <a href="connections.php">Connections</a>
            <a href="logout.php">Logout</a>
        </div>
    </nav>

    <div class="chat-container">
        <div class="chat-header">
            <img src="<?php echo $receiver['profile_pic'] ?: 'default-profile.jpg'; ?>" alt="Profile">
            <h2><?php echo $receiver['full_name']; ?></h2>
        </div>

        <div class="messages">
            <?php while($message = mysqli_fetch_assoc($messages_result)): ?>
                <div class="message <?php echo $message['sender_id'] == $user_id ? 'sent' : 'received'; ?>">
                    <p><?php echo htmlspecialchars($message['message']); ?></p>
                    <small><?php echo date('M d, H:i', strtotime($message['created_at'])); ?></small>
                </div>
            <?php endwhile; ?>
        </div>

        <form method="POST" class="message-form">
            <textarea name="message" required placeholder="Type your message..."></textarea>
            <button type="submit">Send</button>
        </form>
    </div>
</body>
</html>