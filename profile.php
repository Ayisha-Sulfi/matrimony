<?php
include 'config.php';
session_start();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$success_msg = '';
$error_msg = '';

// Handle profile update
if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $age = (int)$_POST['age'];
    $profession = mysqli_real_escape_string($conn, $_POST['profession']);
    $religion = mysqli_real_escape_string($conn, $_POST['religion']);
    $caste = mysqli_real_escape_string($conn, $_POST['caste']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $preferred_language = mysqli_real_escape_string($conn, $_POST['preferred_language']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);

    // Handle profile picture upload
    if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_pic']['name'];
        $filetype = pathinfo($filename, PATHINFO_EXTENSION);
        
        if(in_array(strtolower($filetype), $allowed)) {
            $timestamp = time();
            $new_filename = 'profile_' . $user_id . '_' . $timestamp . '.' . $filetype;
            $upload_path = 'uploads/' . $new_filename;
            
            if(move_uploaded_file($_FILES['profile_pic']['tmp_name'], $upload_path)) {
                // Update profile picture path in database
                $pic_query = "UPDATE users SET profile_pic = '$upload_path' WHERE id = $user_id";
                mysqli_query($conn, $pic_query);
            }
        }
    }

    // Update other profile details
    $query = "UPDATE users SET 
              full_name = '$full_name',
              age = $age,
              profession = '$profession',
              religion = '$religion',
              caste = '$caste',
              phone = '$phone',
              address = '$address',
              preferred_language = '$preferred_language',
              city = '$city'
              WHERE id = $user_id";

    if(mysqli_query($conn, $query)) {
        $success_msg = "Profile updated successfully!";
    } else {
        $error_msg = "Error updating profile: " . mysqli_error($conn);
    }
}

// Get current user data
$query = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $query);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Profile - Matrimony</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Profile Page Specific Styles */
.profile-container {
    max-width: 1000px;
    margin: 2rem auto;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}

.profile-header {
    text-align: center;
    margin-bottom: 3rem;
    position: relative;
}

.profile-pic-container {
    position: relative;
    width: 180px;
    height: 180px;
    margin: 0 auto 1.5rem;
}

.profile-pic {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    object-fit: cover;
    border: 4px solid #fff;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.profile-pic-upload {
    position: absolute;
    bottom: 10px;
    right: 10px;
    background: #3498db;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: background 0.3s;
}

.profile-pic-upload:hover {
    background: #2980b9;
}

.profile-pic-upload input[type="file"] {
    display: none;
}

.profile-pic-upload svg {
    width: 20px;
    height: 20px;
    color: white;
}

.profile-header h2 {
    font-size: 2rem;
    color: #2c3e50;
    margin: 0;
}

.profile-form {
    background: #f8f9fa;
    padding: 2rem;
    border-radius: 8px;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 2rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-group label {
    font-weight: 600;
    color: #34495e;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-group input,
.form-group textarea,
.form-group select {
    padding: 1rem;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    font-size: 1rem;
    transition: border-color 0.3s, box-shadow 0.3s;
    background: white;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    outline: none;
}

.form-group textarea {
    resize: vertical;
    min-height: 100px;
}

.form-section {
    margin-bottom: 2rem;
}

.form-section-title {
    font-size: 1.2rem;
    color: #2c3e50;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid #e9ecef;
}

.update-btn {
    background: #3498db;
    color: white;
    border: none;
    padding: 1rem 2rem;
    border-radius: 8px;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    width: 100%;
    margin-top: 2rem;
    transition: background 0.3s, transform 0.2s;
}

.update-btn:hover {
    background: #2980b9;
    transform: translateY(-2px);
}

.update-btn:active {
    transform: translateY(0);
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.alert.success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert.error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert svg {
    width: 24px;
    height: 24px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .profile-container {
        margin: 1rem;
        padding: 1rem;
    }

    .form-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }

    .profile-pic-container {
        width: 150px;
        height: 150px;
    }

    .profile-header h2 {
        font-size: 1.5rem;
    }

    .form-group input,
    .form-group textarea,
    .form-group select {
        padding: 0.8rem;
    }
}

/* Additional Utility Classes */
.required::after {
    content: '*';
    color: #e74c3c;
    margin-left: 4px;
}

.help-text {
    font-size: 0.8rem;
    color: #7f8c8d;
    margin-top: 0.25rem;
}

.input-group {
    display: flex;
    gap: 1rem;
}

.input-group input {
    flex: 1;
}

/* Custom File Input Styling */
.file-input-wrapper {
    position: relative;
    overflow: hidden;
    display: inline-block;
}

.file-input-wrapper input[type=file] {
    position: absolute;
    left: 0;
    top: 0;
    opacity: 0;
    cursor: pointer;
}

.file-input-button {
    background: #3498db;
    color: white;
    padding: 0.8rem 1.5rem;
    border-radius: 6px;
    cursor: pointer;
    display: inline-block;
    transition: background 0.3s;
}

.file-input-button:hover {
    background: #2980b9;
}

.selected-file {
    margin-top: 0.5rem;
    font-size: 0.9rem;
    color: #7f8c8d;
}
    </style>
</head>
<body>
<script>
    // Add JavaScript to show selected file name
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('profile_pic');
        const fileLabel = document.getElementById('file_label');
        
        fileInput.addEventListener('change', function() {
            if(this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.querySelector('.profile-pic').src = e.target.result;
                }
                reader.readAsDataURL(this.files[0]);
            }
        });
    });
    </script>
</head>
<body>
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

    <div class="profile-container">
        <?php if($success_msg): ?>
            <div class="alert success">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                <?php echo $success_msg; ?>
            </div>
        <?php endif; ?>
        
        <?php if($error_msg): ?>
            <div class="alert error">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12" y2="16"></line></svg>
                <?php echo $error_msg; ?>
            </div>
        <?php endif; ?>

        <div class="profile-header">
            <div class="profile-pic-container">
                <img src="<?php echo $user['profile_pic'] ?: 'default-profile.jpg'; ?>" alt="Profile Picture" class="profile-pic" id="profile-preview">
                <label class="profile-pic-upload">
                    <input type="file" name="profile_pic" id="profile-pic-input" accept="image/*">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path><circle cx="12" cy="13" r="4"></circle></svg>
                </label>
            </div>
            <h2><?php echo $user['full_name']; ?></h2>
        </div>

        <form method="POST" enctype="multipart/form-data" class="profile-form" id="profile-form">
            <!-- Hidden input for profile picture -->
            <input type="file" name="profile_pic" id="form-profile-pic" style="display: none;">

            <div class="form-section">
                <h3 class="form-section-title">Personal Information</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="required">Full Name</label>
                        <input type="text" name="full_name" value="<?php echo $user['full_name']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="required">Age</label>
                        <input type="number" name="age" value="<?php echo $user['age']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="required">Profession</label>
                        <input type="text" name="profession" value="<?php echo $user['profession']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="required">Phone</label>
                        <input type="tel" name="phone" value="<?php echo $user['phone']; ?>" required>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="form-section-title">Cultural Background</h3>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="required">Religion</label>
                        <input type="text" name="religion" value="<?php echo $user['religion']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Caste</label>
                        <input type="text" name="caste" value="<?php echo $user['caste']; ?>">
                    </div>

                    <div class="form-group">
                        <label>Preferred Language</label>
                        <input type="text" name="preferred_language" value="<?php echo $user['preferred_language']; ?>">
                    </div>

                    <div class="form-group">
                        <label class="required">City</label>
                        <input type="text" name="city" value="<?php echo $user['city']; ?>" required>
                    </div>

                    <div class="form-group full-width">
                        <label>Address</label>
                        <textarea name="address" rows="3"><?php echo $user['address']; ?></textarea>
                    </div>
                </div>
            </div>

            <button type="submit" class="update-btn">Update Profile</button>
        </form>
    </div>

    <script>
    // Image preview functionality
    document.getElementById('profile-pic-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profile-preview').src = e.target.result;
                
                // Copy the file to the hidden form input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                document.getElementById('form-profile-pic').files = dataTransfer.files;
            }
            reader.readAsDataURL(file);
        }
    });
    </script>
</body>
</html>