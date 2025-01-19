<?php
session_start();
include 'config.php';

// Redirect to dashboard if already logged in
if(isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome to Matrimony</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(rgba(44, 62, 80, 0.9), rgba(44, 62, 80, 0.9)),
                        url('wedding-bg.jpg') center/cover;
            color: white;
            text-align: center;
            padding: 6rem 2rem;
        }

        .hero-content h1 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
        }

        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .cta-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
        }

        .cta-button {
            padding: 1rem 2.5rem;
            border-radius: 30px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .cta-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .cta-button.primary {
            background: #e74c3c;
            color: white;
        }

        .cta-button.secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .features-section {
            padding: 4rem 2rem;
            background: #f8f9fa;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1200px;
            margin: 0 auto;
        }

        .feature-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-card i {
            font-size: 2.5rem;
            color: #e74c3c;
            margin-bottom: 1rem;
        }

        .feature-card h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .feature-card p {
            color: #7f8c8d;
        }

        .stats-section {
            padding: 4rem 2rem;
            background: #2c3e50;
            color: white;
            text-align: center;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            max-width: 1000px;
            margin: 0 auto;
        }

        .stat-item h2 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
            color: #e74c3c;
        }

        .stat-item p {
            font-size: 1.1rem;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>Matrimony</h1>
        <div class="nav-links">
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        </div>
    </nav>

    <section class="hero-section">
        <div class="hero-content">
            <h1>Find Your Perfect Life Partner</h1>
            <p>Join thousands of happy couples who found their soulmate through our trusted matrimonial platform. Start your journey to finding love and companionship today.</p>
            <div class="cta-buttons">
                <a href="register.php" class="cta-button primary">Get Started</a>
                <a href="about.php" class="cta-button secondary">Learn More</a>
            </div>
        </div>
    </section>

    <section class="features-section">
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-user-shield"></i>
                <h3>Verified Profiles</h3>
                <p>All profiles are manually verified to ensure authenticity and safety for our members.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-sliders-h"></i>
                <h3>Smart Matching</h3>
                <p>Our advanced algorithm suggests compatible matches based on your preferences.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-lock"></i>
                <h3>Privacy Control</h3>
                <p>You have complete control over your profile visibility and contact preferences.</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-comments"></i>
                <h3>Secure Communication</h3>
                <p>Connect and communicate safely with potential matches through our platform.</p>
            </div>
        </div>
    </section>

    <section class="stats-section">
        <div class="stats-grid">
            <div class="stat-item">
                <h2>10K+</h2>
                <p>Active Users</p>
            </div>
            <div class="stat-item">
                <h2>5K+</h2>
                <p>Success Stories</p>
            </div>
            <div class="stat-item">
                <h2>95%</h2>
                <p>User Satisfaction</p>
            </div>
            <div class="stat-item">
                <h2>24/7</h2>
                <p>Customer Support</p>
            </div>
        </div>
    </section>

    <footer style="background: #34495e; color: white; text-align: center; padding: 2rem;">
        <p>&copy; <?php echo date('Y'); ?> Matrimony. All rights reserved.</p>
    </footer>
</body>
</html>