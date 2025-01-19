<?php
include '../config.php';
session_start();

if(!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../login.php");
    exit();
}

if(!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$user_id = (int)$_GET['id'];

// Get user details with additional statistics
$query = "SELECT u.*, 
          (SELECT COUNT(*) FROM connections WHERE sender_id = u.id OR receiver_id = u.id) as connection_count,
          (SELECT COUNT(*) FROM messages WHERE sender_id = u.id OR receiver_id = u.id) as message_count,
          (SELECT COUNT(*) FROM connections WHERE (sender_id = u.id OR receiver_id = u.id) AND status = 'accepted') as accepted_connections,
          (SELECT COUNT(*) FROM connections WHERE (sender_id = u.id OR receiver_id = u.id) AND status = 'pending') as pending_connections
          FROM users u 
          WHERE u.id = $user_id AND u.is_admin = 0";

$result = mysqli_query($conn, $query);

if(!$result || mysqli_num_rows($result) == 0) {
    header("Location: dashboard.php");
    exit();
}

$user = mysqli_fetch_assoc($result);

// Get recent connections
$connections_query = "SELECT c.*, 
                     u2.full_name as connected_user,
                     u2.email as connected_email
                     FROM connections c 
                     LEFT JOIN users u2 ON (c.sender_id = u2.id AND c.receiver_id = $user_id) 
                                      OR (c.receiver_id = u2.id AND c.sender_id = $user_id)
                     WHERE c.sender_id = $user_id OR c.receiver_id = $user_id 
                     ORDER BY c.created_at DESC LIMIT 5";

$connections = mysqli_query($conn, $connections_query);

// Get recent messages - Fixed the column name from sent_at to created_at
$messages_query = "SELECT m.*, 
                  CASE 
                      WHEN m.sender_id = $user_id THEN 'Sent'
                      ELSE 'Received'
                  END as direction,
                  u2.full_name as other_user
                  FROM messages m
                  LEFT JOIN users u2 ON (m.sender_id = u2.id AND m.sender_id != $user_id) 
                                   OR (m.receiver_id = u2.id AND m.sender_id = $user_id)
                  WHERE m.sender_id = $user_id OR m.receiver_id = $user_id
                  ORDER BY m.created_at DESC LIMIT 5"; // Changed from sent_at to created_at

$messages = mysqli_query($conn, $messages_query);
// Calculate user status
$status = 'Active';
if($user['status'] == 'suspended') {
    $status = 'suspended';
} elseif(isset($user['last_login']) && strtotime($user['last_login']) >= strtotime('-7 days')) {
    $status = 'active';
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>View User - <?php echo htmlspecialchars($user['full_name']); ?></title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', sans-serif;
        }

        body {
            background: #f0f2f5;
            padding: 2rem;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 1rem;
            color: #3498db;
            text-decoration: none;
        }

        .back-link i {
            margin-right: 0.5rem;
        }

        .user-header {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .user-info h1 {
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }

        .user-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .status-active {
            background: #d4edda;
            color: #155724;
        }

        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }

        .status-suspended {
            background: #fff3cd;
            color: #856404;
        }

        .user-actions {
            display: flex;
            gap: 1rem;
        }

        .action-btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .edit-btn {
            background: #3498db;
        }

        .suspend-btn {
            background: #f39c12;
        }

        .delete-btn {
            background: #e74c3c;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .card {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .card h2 {
            margin-bottom: 1rem;
            color: #2c3e50;
            font-size: 1.2rem;
        }

        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.8rem;
            padding-bottom: 0.8rem;
            border-bottom: 1px solid #eee;
        }

        .detail-label {
            color: #7f8c8d;
            font-weight: 500;
        }

        .detail-value {
            color: #2c3e50;
            font-weight: 600;
        }

        .activity-list {
            list-style: none;
        }

        .activity-item {
            padding: 1rem;
            border-bottom: 1px solid #eee;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.5rem;
        }

        .activity-title {
            font-weight: 600;
            color: #2c3e50;
        }

        .activity-date {
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .activity-content {
            color: #34495e;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .stat-label {
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>

        <div class="user-header">
            <div class="user-info">
                <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>
                <span class="user-status status-<?php echo $status; ?>">
                    <?php echo ucfirst($status); ?>
                </span>
            </div>
            <div class="user-actions">
    <?php if($user['status'] == 'suspended'): ?>
        <a href="dashboard.php?action=activate&user_id=<?php echo $user['id']; ?>" class="action-btn edit-btn">
            <i class="fas fa-user-check"></i> Activate User
        </a>
    <?php else: ?>
        <a href="dashboard.php?action=suspend&user_id=<?php echo $user['id']; ?>" class="action-btn suspend-btn">
            <i class="fas fa-user-slash"></i> Suspend User
        </a>
    <?php endif; ?>
    <a href="dashboard.php?action=delete&user_id=<?php echo $user['id']; ?>" 
       class="action-btn delete-btn"
       onclick="return confirm('Are you sure you want to delete this user? This will also delete all their connections and messages.')">
        <i class="fas fa-trash"></i> Delete User
    </a>
</div>
        </div>

        <div class="grid">
            <div class="card">
                <h2>Basic Information</h2>
                <div class="detail-row">
                    <span class="detail-label">Email</span>
                    <span class="detail-value"><?php echo htmlspecialchars($user['email']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Gender</span>
                    <span class="detail-value"><?php echo htmlspecialchars($user['gender']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Age</span>
                    <span class="detail-value"><?php echo htmlspecialchars($user['age']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">City</span>
                    <span class="detail-value"><?php echo htmlspecialchars($user['city']); ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Last Login</span>
                    <span class="detail-value">
                        <?php echo $user['last_login'] ? date('M j, Y g:i A', strtotime($user['last_login'])) : 'Never'; ?>
                    </span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Joined Date</span>
                    <span class="detail-value">
                        <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                    </span>
                </div>
            </div>

            <div class="card">
                <h2>Activity Statistics</h2>
                <div class="detail-row">
                    <div>
                        <div class="stat-number"><?php echo $user['connection_count']; ?></div>
                        <div class="stat-label">Total Connections</div>
                    </div>
                    <div>
                        <div class="stat-number"><?php echo $user['message_count']; ?></div>
                        <div class="stat-label">Total Messages</div>
                    </div>
                </div>
                <div class="detail-row">
                    <div>
                        <div class="stat-number"><?php echo $user['accepted_connections']; ?></div>
                        <div class="stat-label">Accepted Connections</div>
                    </div>
                    <div>
                        <div class="stat-number"><?php echo $user['pending_connections']; ?></div>
                        <div class="stat-label">Pending Connections</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <h2>Recent Connections</h2>
                <ul class="activity-list">
                    <?php if(mysqli_num_rows($connections) > 0): ?>
                        <?php while($connection = mysqli_fetch_assoc($connections)): ?>
                            <li class="activity-item">
                                <div class="activity-header">
                                    <span class="activity-title">
                                        <?php echo htmlspecialchars($connection['connected_user']); ?>
                                    </span>
                                    <span class="activity-date">
                                        <?php echo date('M j, Y', strtotime($connection['created_at'])); ?>
                                    </span>
                                </div>
                                <div class="activity-content">
                                    Status: <?php echo ucfirst($connection['status']); ?>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li class="activity-item">No connections found</li>
                    <?php endif; ?>
                </ul>
            </div>

            <div class="card">
                <h2>Recent Messages</h2>
                <ul class="activity-list">
                    <?php if(mysqli_num_rows($messages) > 0): ?>
                        <?php while($message = mysqli_fetch_assoc($messages)): ?>
                            <li class="activity-item">
                                <div class="activity-header">
                                    <span class="activity-title">
                                        <?php echo $message['direction']; ?> to <?php echo htmlspecialchars($message['other_user']); ?>
                                    </span>
                                    <span class="activity-date">
                                        <?php echo date('M j, Y g:i A', strtotime($message['created_at'])); ?>
                                    </span>
                                </div>
                                <div class="activity-content">
                                    <?php echo htmlspecialchars(substr($message['message'], 0, 100)) . (strlen($message['message']) > 100 ? '...' : ''); ?>
                                </div>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li class="activity-item">No messages found</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>