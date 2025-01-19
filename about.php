<?php
session_start();
include 'config.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>About Us - Matrimony</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .about-header {
            background: linear-gradient(rgba(44, 62, 80, 0.9), rgba(44, 62, 80, 0.9)),
                        url('couple-bg.jpg') center/cover;
            color: white;
            text-align: center;
            padding: 4rem 2rem;
        }

        .about-header h1 {
            font-size: 2.5rem;
            margin-bottom: 1.5rem;
        }

        .about-header p {
            max-width: 800px;
            margin: 0 auto;
            font-size: 1.1rem;
            line-height: 1.6;
        }

        .about-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }

        .mission-section {
            text-align: center;
            margin-bottom: 4rem;
        }

        .mission-section h2 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }

        .mission-section p {
            color: #34495e;
            max-width: 800px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 4rem 0;
        }

        .value-card {
            background: #f8f9fa;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            transition: transform 0.3s;
        }

        .value-card:hover {
            transform: translateY(-5px);
        }

        .value-card i {
            font-size: 2.5rem;
            color: #e74c3c;
            margin-bottom: 1rem;
        }

        .value-card h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .value-card p {
            color: #7f8c8d;
            line-height: 1.6;
        }

        .team-section {
            text-align: center;
            margin: 4rem 0;
        }

        .team-section h2 {
            color: #2c3e50;
            margin-bottom: 2rem;
            font-size: 2rem;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }

        .team-member {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .team-member img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin-bottom: 1rem;
            object-fit: cover;
        }

        .team-member h3 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .team-member p {
            color: #7f8c8d;
            margin-bottom: 1rem;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .social-links a {
            color: #3498db;
            font-size: 1.2rem;
            transition: color 0.3s;
        }

        .social-links a:hover {
            color: #2980b9;
        }

        .contact-section {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 4rem 2rem;
            margin-top: 4rem;
        }

        .contact-section h2 {
            margin-bottom: 2rem;
            font-size: 2rem;
        }

        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1000px;
            margin: 0 auto;
        }

        .contact-item i {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #e74c3c;
        }

        .contact-item h3 {
            margin-bottom: 0.5rem;
        }

        .contact-item p {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>Matrimony</h1>
        <div class="nav-links">
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="profile.php">Profile</a>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="index.php">Home</a>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif; ?>
        </div>
    </nav>

    <header class="about-header">
        <h1>About Us</h1>
        <p>Connecting Hearts, Creating Happy Marriages</p>
    </header>

    <div class="about-content">
        <section class="mission-section">
            <h2>Our Mission</h2>
            <p>We are dedicated to helping individuals find their perfect life partner through a secure, trusted, and sophisticated matchmaking platform. Our mission is to blend traditional values with modern technology to create meaningful connections that lead to happy marriages.</p>
        </section>

        <section class="values-grid">
            <div class="value-card">
                <i class="fas fa-heart"></i>
                <h3>Trust</h3>
                <p>We maintain the highest standards of trust and authenticity in our platform, ensuring a safe environment for our users.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Privacy</h3>
                <p>Your privacy is our top priority. We implement strict measures to protect your personal information.</p>
            </div>
            <div class="value-card">
                <i class="fas fa-users"></i>
                <h3>Community</h3>
                <p>We foster a respectful community where people can connect and find their perfect match.</p>
            </div>
        </section>

        <section class="team-section">
            <h2>Our Leadership Team</h2>
            <div class="team-grid">
                <div class="team-member">
                    <img src="team1.jpg" alt="CEO">
                    <h3>John Doe</h3>
                    <p>Founder & CEO</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <div class="team-member">
                    <img src="team2.jpg" alt="CTO">
                    <h3>Jane Smith</h3>
                    <p>Chief Technology Officer</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
                <div class="team-member">
                    <img src="team3.jpg" alt="COO">
                    <h3>Mike Johnson</h3>
                    <p>Chief Operating Officer</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                    </div>
                </div>
            </div>
        </section>

        <section class="contact-section">
            <h2>Get in Touch</h2>
            <div class="contact-grid">
                <div class="contact-item">
                    <i class="fas fa-envelope"></i>
                    <h3>Email</h3>
                    <p>contact@matrimony.com</p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-phone"></i>
                    <h3>Phone</h3>
                    <p>+1 (555) 123-4567</p>
                </div>
                <div class="contact-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Location</h3>
                    <p>123 Wedding Street, Love City</p>
                </div>
            </div>
        </section>
    </div>

    <footer style="background: #34495e; color: white; text-align: center; padding: 2rem;">
        <p>&copy; <?php echo date('Y'); ?> Matrimony. All rights reserved.</p>
    </footer>
</body>
</html>