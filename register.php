<?php
include 'config.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $religion = $_POST['religion'];
    $caste = $_POST['caste'];
    $profession = $_POST['profession'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $preferred_language = $_POST['preferred_language'];
    $city = $_POST['city'];

    $profile_pic = "";
    if(isset($_FILES['profile_pic'])) {
        $target_dir = "uploads/";
        $profile_pic = $target_dir . basename($_FILES["profile_pic"]["name"]);
        move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $profile_pic);
    }

    $query = "INSERT INTO users (full_name, email, password, gender, age, religion, caste, profession, phone, address, preferred_language, city, profile_pic) 
              VALUES ('$full_name', '$email', '$password', '$gender', $age, '$religion', '$caste', '$profession', '$phone', '$address', '$preferred_language', '$city', '$profile_pic')";

    if(mysqli_query($conn, $query)) {
        header("Location: login.php");
    } else {
        $error_message = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register - Matrimony</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f5f6fa;
        }

        .register-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .register-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .register-header h2 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .register-header p {
            color: #7f8c8d;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1.5rem;
        }

        .input-group {
            margin-bottom: 1rem;
        }

        .input-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
            font-weight: 500;
        }

        .input-group input,
        .input-group select,
        .input-group textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .input-group input:focus,
        .input-group select:focus,
        .input-group textarea:focus {
            outline: none;
            border-color: #3498db;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .submit-btn {
            width: 100%;
            padding: 1rem;
            background: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 1rem;
        }

        .submit-btn:hover {
            background: #2980b9;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 0.8rem;
            border-radius: 5px;
            margin-bottom: 1rem;
            text-align: center;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #7f8c8d;
        }

        .login-link a {
            color: #3498db;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .profile-upload {
            border: 2px dashed #ddd;
            padding: 1.5rem;
            text-align: center;
            border-radius: 5px;
            cursor: pointer;
            transition: border-color 0.3s;
        }

        .profile-upload:hover {
            border-color: #3498db;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>Matrimony</h1>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="about.php">About</a>
            <a href="login.php">Login</a>
        </div>
    </nav>

    <div class="register-container">
        <div class="register-header">
            <h2>Create Your Account</h2>
            <p>Find your perfect match by joining our community</p>
        </div>

        <?php if(isset($error_message)): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" required>
                </div>
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required>
                </div>
                <div class="input-group">
                    <label>Password</label>
                    <input type="password" name="password" required>
                </div>
                <div class="input-group">
                    <label>Gender</label>
                    <select name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
                <div class="input-group">
                    <label>Age</label>
                    <input type="number" name="age" required>
                </div>
                <div class="input-group">
                    <label>Religion</label>
                    <input type="text" name="religion" required>
                </div>
                <div class="input-group">
                    <label>Caste</label>
                    <input type="text" name="caste" required>
                </div>
                <div class="input-group">
                    <label>Profession</label>
                    <input type="text" name="profession" required>
                </div>
                <div class="input-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" required>
                </div>
                <div class="input-group">
                    <label>City</label>
                    <input type="text" name="city" required>
                </div>
                <div class="input-group">
                    <label>Preferred Language</label>
                    <input type="text" name="preferred_language" required>
                </div>
                <div class="input-group profile-upload">
                    <label>Profile Picture</label>
                    <input type="file" name="profile_pic" accept="image/*">
                </div>
                <div class="input-group full-width">
                    <label>Address</label>
                    <textarea name="address" required></textarea>
                </div>
            </div>
            <button type="submit" class="submit-btn">Create Account</button>
        </form>

        <div class="login-link">
            Already have an account? <a href="login.php">Login Now</a>
        </div>
    </div>
</body>
</html>