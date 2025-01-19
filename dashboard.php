<?php
include 'config.php';
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// First check if current user is admin
$user_id = $_SESSION['user_id'];
$check_admin = mysqli_query($conn, "SELECT is_admin FROM users WHERE id = $user_id");
$user_data = mysqli_fetch_assoc($check_admin);

if($user_data['is_admin'] == 1) {
    header("Location: admin/dashboard.php");
    exit();
}

$gender_filter = isset($_GET['gender']) ? $_GET['gender'] : '';
$age_filter = isset($_GET['age']) ? $_GET['age'] : '';
$city_filter = isset($_GET['city']) ? $_GET['city'] : '';

// Modified query to exclude admin profiles and current user
$query = "SELECT * FROM users WHERE id != $user_id AND is_admin = 0";
if($gender_filter) $query .= " AND gender='$gender_filter'";
if($age_filter) $query .= " AND age=$age_filter";
if($city_filter) $query .= " AND city='$city_filter'";

$result = mysqli_query($conn, $query);

// Get current user details (non-admin only)
$user_query = "SELECT * FROM users WHERE id = $user_id AND is_admin = 0";
$user_result = mysqli_query($conn, $user_query);
$user = mysqli_fetch_assoc($user_result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Matrimony</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .profile-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            padding: 2rem;
        }

        .profile-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 2rem;
            position: relative;
        }

        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .profile-pic-wrapper {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            overflow: hidden;
            margin: -1rem auto 1.5rem;
            border: 4px solid white;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            position: relative;
        }

        .profile-pic-wrapper img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .profile-card:hover .profile-pic-wrapper img {
            transform: scale(1.05);
        }

        .profile-info {
            text-align: center;
            width: 100%;
            padding: 0 1rem;
        }

        .profile-name {
            color: #2c3e50;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .profile-basic-info {
            color: #34495e;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .profile-details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin: 1rem 0;
            text-align: left;
        }

        .detail-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #666;
            font-size: 0.95rem;
        }

        .detail-item i {
            width: 20px;
            color: #3498db;
        }

        .profile-actions {
            display: flex;
            gap: 1rem;
            padding: 1.5rem 0 0;
            width: 100%;
            border-top: 1px solid #eee;
            margin-top: 1rem;
        }

        .button {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-weight: 600;
            transition: all 0.3s;
        }

        .connect-btn {
            background: #2ecc71;
            color: white;
        }

        .connect-btn:hover {
            background: #27ae60;
            transform: translateY(-2px);
        }

        .message-btn {
            background: #3498db;
            color: white;
        }

        .message-btn:hover {
            background: #2980b9;
            transform: translateY(-2px);
        }

        .status-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            background: #95a5a6;
            color: white;
            text-transform: capitalize;
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

    <div class="filters">
        <form method="GET">
            <select name="gender">
                <option value="">All Genders</option>
                <option value="Male" <?php echo $gender_filter == 'Male' ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo $gender_filter == 'Female' ? 'selected' : ''; ?>>Female</option>
            </select>
            <input type="number" name="age" placeholder="Age" value="<?php echo $age_filter; ?>">
            <input type="text" name="city" placeholder="City" value="<?php echo $city_filter; ?>">
            <input type="submit" value="Filter">
        </form>
    </div>

    <div class="profile-grid">
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="profile-card">
                <div class="profile-pic-wrapper">
                    <img src="<?php echo $row['profile_pic'] ? $row['profile_pic'] : 'default-profile.jpg'; ?>" alt="Profile Picture">
                </div>
                <div class="profile-info">
                    <h3 class="profile-name"><?php echo $row['full_name']; ?></h3>
                    <div class="profile-basic-info">
                        <span><?php echo $row['age']; ?> years</span>
                        <span>|</span>
                        <span><?php echo $row['profession']; ?></span>
                    </div>
                    <div class="profile-details">
                        <div class="detail-item">
                            <i class="fas fa-pray"></i>
                            <span><?php echo $row['religion']; ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-users"></i>
                            <span><?php echo $row['caste']; ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><?php echo $row['city']; ?></span>
                        </div>
                        <div class="detail-item">
                            <i class="fas fa-language"></i>
                            <span><?php echo $row['preferred_language']; ?></span>
                        </div>
                    </div>
                </div>
                <div class="profile-actions">
                    <?php
                    $check_query = "SELECT * FROM connections WHERE 
                                    (sender_id = $user_id AND receiver_id = {$row['id']}) OR 
                                    (sender_id = {$row['id']} AND receiver_id = $user_id)";
                    $check_result = mysqli_query($conn, $check_query);
                    $connection = mysqli_fetch_assoc($check_result);
                    
                    if(!$connection): ?>
                        <form action="connect.php" method="POST" style="width: 100%;">
                            <input type="hidden" name="receiver_id" value="<?php echo $row['id']; ?>">
                            <button type="submit" class="button connect-btn" style="width: 100%;">
                                <i class="fas fa-heart"></i> Connect
                            </button>
                        </form>
                    <?php elseif($connection['status'] == 'accepted'): ?>
                        <a href="messages.php?receiver_id=<?php echo $row['id']; ?>" class="button message-btn" style="width: 100%;">
                            <i class="fas fa-comment"></i> Message
                        </a>
                    <?php else: ?>
                        <div class="status-badge">
                            <?php echo ucfirst($connection['status']); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>