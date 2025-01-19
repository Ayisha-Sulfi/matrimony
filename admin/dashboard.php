<?php
include '../config.php';
session_start();

if(!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: ../login.php");
    exit();
}

// Statistics - Fixed queries
$total_users = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE is_admin=0"))['total'];
$total_connections = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM connections"))['total'];
$pending_connections = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM connections WHERE status='pending'"))['total'];
$total_messages = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM messages"))['total'];

// Search and filters
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$gender_filter = isset($_GET['gender']) ? mysqli_real_escape_string($conn, $_GET['gender']) : '';
$status_filter = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Base query
$base_query = "FROM users u WHERE is_admin=0";
$conditions = [];

if($search) {
    $conditions[] = "(full_name LIKE '%$search%' OR email LIKE '%$search%' OR city LIKE '%$search%')";
}
if($gender_filter) {
    $conditions[] = "gender='$gender_filter'";
}
if($status_filter == 'active') {
    $conditions[] = "last_login >= NOW() - INTERVAL 7 DAY";
} elseif($status_filter == 'inactive') {
    $conditions[] = "(last_login < NOW() - INTERVAL 7 DAY OR last_login IS NULL)";
}

if(!empty($conditions)) {
    $base_query .= " AND " . implode(" AND ", $conditions);
}

// Count total results
$count_query = "SELECT COUNT(*) as total " . $base_query;
$total_results = mysqli_fetch_assoc(mysqli_query($conn, $count_query))['total'];
$total_pages = ceil($total_results / $per_page);

// Main query with all necessary fields
$query = "SELECT u.*, 
          (SELECT COUNT(*) FROM connections WHERE sender_id = u.id OR receiver_id = u.id) as connection_count,
          (SELECT COUNT(*) FROM messages WHERE sender_id = u.id OR receiver_id = u.id) as message_count
          " . $base_query . " LIMIT $offset, $per_page";

$result = mysqli_query($conn, $query);

// Handle user actions
if(isset($_GET['action']) && isset($_GET['user_id'])) {
    $user_id = (int)$_GET['user_id'];
    
    switch($_GET['action']) {
        case 'delete':
            // First delete related records
            mysqli_query($conn, "DELETE FROM messages WHERE sender_id = $user_id OR receiver_id = $user_id");
            mysqli_query($conn, "DELETE FROM connections WHERE sender_id = $user_id OR receiver_id = $user_id");
            // Then delete user
            mysqli_query($conn, "DELETE FROM users WHERE id = $user_id AND is_admin = 0");
            header("Location: dashboard.php?message=User+deleted+successfully");
            exit();
            break;
            
        case 'suspend':
            mysqli_query($conn, "UPDATE users SET status = 'suspended' WHERE id = $user_id AND is_admin = 0");
            header("Location: dashboard.php?message=User+suspended+successfully");
            exit();
            break;
            
        case 'activate':
            mysqli_query($conn, "UPDATE users SET status = 'active' WHERE id = $user_id AND is_admin = 0");
            header("Location: dashboard.php?message=User+activated+successfully");
            exit();
            break;
    }
}

// Get success/error messages
$message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard - Matrimony</title>
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
        }

        .container {
            padding: 2rem;
        }

        .dashboard-header {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            margin-top: 1.5rem;
        }

        .stat-card {
            background: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
        }

        .stat-card i {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #3498db;
        }

        .stat-number {
            font-size: 1.8rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .stat-label {
            color: #7f8c8d;
            margin-top: 0.5rem;
        }

        .filters {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .filters form {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
        }

        .search-input {
            flex: 1;
            min-width: 200px;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        select {
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-width: 150px;
        }

        .filter-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 4px;
            cursor: pointer;
        }

        .users-table {
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background: #f8f9fa;
            font-weight: 600;
        }

        .action-btns {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 0.9rem;
        }

        .edit-btn {
            background: #3498db;
            color: white;
        }

        .delete-btn {
            background: #e74c3c;
            color: white;
        }

        .suspend-btn {
            background: #f39c12;
            color: white;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 2rem;
        }

        .page-link {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-decoration: none;
            color: #3498db;
        }

        .page-link.active {
            background: #3498db;
            color: white;
            border-color: #3498db;
        }

        .user-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .badge-active {
    background: #d4edda;
    color: #155724;
}

        .badge-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .header-flex {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.logout-btn {
    background: #e74c3c;
    color: white;
    padding: 0.8rem 1.5rem;
    border-radius: 4px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: background-color 0.2s;
}

.logout-btn:hover {
    background: #c0392b;
}
    </style>
</head>
<body>
    <div class="container">
        <?php if($message): ?>
            <div class="alert"><?php echo $message; ?></div>
        <?php endif; ?>

        <div class="dashboard-header">
    <div class="header-flex">
        <h1>Admin Dashboard</h1>
        <a href="../logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
    <div class="stats-grid">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <div class="stat-number"><?php echo $total_users; ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-heart"></i>
                    <div class="stat-number"><?php echo $total_connections; ?></div>
                    <div class="stat-label">Total Connections</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-clock"></i>
                    <div class="stat-number"><?php echo $pending_connections; ?></div>
                    <div class="stat-label">Pending Connections</div>
                </div>
                <div class="stat-card">
                    <i class="fas fa-comment"></i>
                    <div class="stat-number"><?php echo $total_messages; ?></div>
                    <div class="stat-label">Total Messages</div>
                </div>
            </div>
        </div>

        <div class="filters">
            <form method="GET">
                <input type="text" name="search" placeholder="Search users..." class="search-input" value="<?php echo htmlspecialchars($search); ?>">
                <select name="gender">
                    <option value="">All Genders</option>
                    <option value="Male" <?php echo $gender_filter == 'Male' ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo $gender_filter == 'Female' ? 'selected' : ''; ?>>Female</option>
                </select>
                <select name="status">
                    <option value="">All Status</option>
                    <option value="active" <?php echo $status_filter == 'active' ? 'selected' : ''; ?>>Active</option>
                    <option value="inactive" <?php echo $status_filter == 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                </select>
                <button type="submit" class="filter-btn">Apply Filters</button>
                <a href="dashboard.php" class="filter-btn" style="text-decoration: none; margin-left: 10px;">Reset Filters</a>
            </form>
        </div>

        <div class="users-table">
            <?php if(mysqli_num_rows($result) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Gender</th>
                            <th>Age</th>
                            <th>City</th>
                            <th>Connections</th>
                            <th>Messages</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                <td><?php echo htmlspecialchars($row['gender']); ?></td>
                                <td><?php echo htmlspecialchars($row['age']); ?></td>
                                <td><?php echo htmlspecialchars($row['city']); ?></td>
                                <td><?php echo $row['connection_count']; ?></td>
                                <td><?php echo $row['message_count']; ?></td>
                                <td>
    <?php 
    $status_class = 'inactive';
    $status_text = 'Inactive';
    
    if($row['status'] == 'active') {
        $status_class = 'active';
        $status_text = 'Active';
    } else if($row['status'] == 'suspended') {
        $status_class = 'suspended';
        $status_text = 'Suspended';
    }
    ?>
    <span class="user-badge badge-<?php echo $status_class; ?>">
        <?php echo $status_text; ?>
    </span>
</td>
                                <td class="action-btns">
                                    <a href="view_user.php?id=<?php echo $row['id']; ?>" class="action-btn edit-btn" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if($row['status'] == 'suspended'): ?>
                                        <a href="?action=activate&user_id=<?php echo $row['id']; ?>" class="action-btn edit-btn" title="Activate User">
                                            <i class="fas fa-user-check"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="?action=suspend&user_id=<?php echo $row['id']; ?>" class="action-btn suspend-btn" title="Suspend User">
                                            <i class="fas fa-user-slash"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="?action=delete&user_id=<?php echo $row['id']; ?>" 
                                       class="action-btn delete-btn" 
                                       onclick="return confirm('Are you sure you want to delete this user? This will also delete all their connections and messages.')"
                                       title="Delete User">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 2rem;">No users found matching your criteria.</p>
            <?php endif; ?>
        </div>

        <?php if($total_pages > 1): ?>
            <div class="pagination">
                <?php if($page > 1): ?>
                    <a href="?page=1&search=<?php echo urlencode($search); ?>&gender=<?php echo urlencode($gender_filter); ?>&status=<?php echo urlencode($status_filter); ?>" 
                       class="page-link" title="First Page">
                        <i class="fas fa-angle-double-left"></i>
                    </a>
                <?php endif; ?>
                
                <?php for($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&gender=<?php echo urlencode($gender_filter); ?>&status=<?php echo urlencode($status_filter); ?>" 
                       class="page-link <?php echo $page == $i ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if($page < $total_pages): ?>
                    <a href="?page=<?php echo $total_pages; ?>&search=<?php echo urlencode($search); ?>&gender=<?php echo urlencode($gender_filter); ?>&status=<?php echo urlencode($status_filter); ?>" 
                       class="page-link" title="Last Page">
                        <i class="fas fa-angle-double-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
    // Add JavaScript for real-time search (optional)
    document.querySelector('input[name="search"]').addEventListener('input', function(e) {
        if(e.target.value.length >= 3 || e.target.value.length === 0) {
            setTimeout(() => {
                document.querySelector('.filters form').submit();
            }, 500);
        }
    });
    </script>
</body>
</html>