<?php
include 'config.php';
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle connection actions
if(isset($_POST['action']) && isset($_POST['connection_id'])) {
    $action = $_POST['action'];
    $connection_id = $_POST['connection_id'];
    
    if($action == 'accept' || $action == 'reject') {
        $query = "UPDATE connections SET status = '$action" . "ed' 
                  WHERE id = $connection_id AND receiver_id = $user_id";
        mysqli_query($conn, $query);
    }
}

// Get received connections
$received_query = "SELECT c.*, u.full_name, u.profile_pic, u.age, u.profession, u.city, u.religion 
                  FROM connections c 
                  JOIN users u ON c.sender_id = u.id 
                  WHERE c.receiver_id = $user_id 
                  ORDER BY c.created_at DESC";
$received_result = mysqli_query($conn, $received_query);

// Get sent connections
$sent_query = "SELECT c.*, u.full_name, u.profile_pic, u.age, u.profession, u.city, u.religion 
              FROM connections c 
              JOIN users u ON c.receiver_id = u.id 
              WHERE c.sender_id = $user_id 
              ORDER BY c.created_at DESC";
$sent_result = mysqli_query($conn, $sent_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Connections - Matrimony</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .connections-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .connections-section {
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            overflow: hidden;
        }

        .section-header {
            background: #f8f9fa;
            padding: 1.5rem;
            border-bottom: 1px solid #eee;
        }

        .section-header h2 {
            color: #2c3e50;
            margin: 0;
            font-size: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .connection-count {
            background: #3498db;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .connection-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            padding: 1.5rem;
        }

        .connection-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .connection-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .profile-pic-wrapper {
            width: 120px;
            height: 120px;
            margin: 1.5rem auto;
            border-radius: 50%;
            overflow: hidden;
            border: 4px solid white;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .profile-pic-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .connection-card:hover .profile-pic-wrapper img {
            transform: scale(1.05);
        }

        .connection-info {
            padding: 1.5rem;
            text-align: center;
        }

        .connection-info h3 {
            color: #2c3e50;
            margin: 0 0 0.5rem;
            font-size: 1.25rem;
        }

        .connection-info p {
            color: #7f8c8d;
            margin: 0.25rem 0;
            font-size: 0.95rem;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        .status-pending {
            background: #f1c40f;
            color: #fff;
        }

        .status-accepted {
            background: #2ecc71;
            color: #fff;
        }

        .status-rejected {
            background: #e74c3c;
            color: #fff;
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            padding: 1rem;
            border-top: 1px solid #eee;
            margin-top: 1rem;
        }

        .action-buttons button {
            flex: 1;
            padding: 0.75rem;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, background 0.3s;
        }

        .action-buttons button:hover {
            transform: translateY(-2px);
        }

        .accept-btn {
            background: #2ecc71;
            color: white;
        }

        .accept-btn:hover {
            background: #27ae60;
        }

        .reject-btn {
            background: #e74c3c;
            color: white;
        }

        .reject-btn:hover {
            background: #c0392b;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1.5rem;
            color: #7f8c8d;
        }

        .empty-state p {
            margin: 0.5rem 0;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .connection-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
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

    <div class="connections-container">
        <div class="connections-section">
            <div class="section-header">
                <h2>
                    Received Requests
                    <span class="connection-count"><?php echo mysqli_num_rows($received_result); ?></span>
                </h2>
            </div>
            <div class="connection-grid">
                <?php if(mysqli_num_rows($received_result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($received_result)): ?>
                        <div class="connection-card">
                            <div class="profile-pic-wrapper">
                                <img src="<?php echo $row['profile_pic'] ?: 'default-profile.jpg'; ?>" alt="Profile">
                            </div>
                            <div class="connection-info">
                                <h3><?php echo $row['full_name']; ?></h3>
                                <p><?php echo $row['age']; ?> years | <?php echo $row['profession']; ?></p>
                                <p><?php echo $row['religion']; ?> | <?php echo $row['city']; ?></p>
                                <div class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </div>
                                <?php if($row['status'] == 'pending'): ?>
                                    <form method="POST" class="action-buttons">
                                        <input type="hidden" name="connection_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="action" value="accept" class="accept-btn">Accept</button>
                                        <button type="submit" name="action" value="reject" class="reject-btn">Reject</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No received requests yet</p>
                        <p>When someone sends you a connection request, it will appear here</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="connections-section">
            <div class="section-header">
                <h2>
                    Sent Requests
                    <span class="connection-count"><?php echo mysqli_num_rows($sent_result); ?></span>
                </h2>
            </div>
            <div class="connection-grid">
                <?php if(mysqli_num_rows($sent_result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($sent_result)): ?>
                        <div class="connection-card">
                            <div class="profile-pic-wrapper">
                                <img src="<?php echo $row['profile_pic'] ?: 'default-profile.jpg'; ?>" alt="Profile">
                            </div>
                            <div class="connection-info">
                                <h3><?php echo $row['full_name']; ?></h3>
                                <p><?php echo $row['age']; ?> years | <?php echo $row['profession']; ?></p>
                                <p><?php echo $row['religion']; ?> | <?php echo $row['city']; ?></p>
                                <div class="status-badge status-<?php echo strtolower($row['status']); ?>">
                                    <?php echo ucfirst($row['status']); ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <p>No sent requests yet</p>
                        <p>When you send connection requests to others, they will appear here</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>