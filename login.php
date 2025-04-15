<?php
require_once 'config.php';


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Skill India</title>
    <link rel="stylesheet" href="css/auth.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Navbar -->
        <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo" style="font-size: 38px; font-weight: 900; text-transform: uppercase; letter-spacing: 2px; background: linear-gradient(45deg, #6c5ce7, #00cec9); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">Skill<span style="font-style: italic; -webkit-text-fill-color: #00cec9; text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">India</span></a>
            <div class="nav-links">
            <a href="index.php" class="active nav" style="margin-left: 5px; margin-right: 5px;">Home</a>
            <a href="index.php#courses" class="nav" style="margin-left: 5px; margin-right: 5px;">Courses</a>
            <a href="index.php#about" class="nav" style="margin-left: 5px; margin-right: 5px;">About</a>
            <a href="index.php#contact" class="nav" style="margin-left: 5px; margin-right: 5px;">Contact</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php" class="btn btn-primary">Dashboard</a>
                    <a href="?logout=1" class="btn btn-secondary">Logout</a>
                <?php else: ?>
                    <a href="signup.php" class="btn btn-signup">Sign Up</a>
                <?php endif; ?>
            </div>

        </div>
    </nav>

    <!-- Auth Container -->
    <section class="auth-container">
        <div class="container">
            <div class="auth-card">
                <h2>Welcome Back!</h2>
                <p>Login to access your dashboard and continue learning.</p>
                
                <div class="error-message" style="display: none;"></div>
                <div class="success-message" style="display: none;"></div>
                
                <form id="loginForm" method="POST">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <div class="password-toggle">
                            <i class="fas fa-eye"></i>
                        </div>
                    </div>
                    
                    <div class="form-options">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember me</label>
                        </div>
                        <a href="#" class="forgot-password">Forgot password?</a>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Login</button>
                </form>
                
                <div class="auth-divider">
                    <span>or</span>
                </div>
                
                <div class="social-login">
                    <button class="btn btn-google">
                        <i class="fab fa-google"></i> Continue with Google
                    </button>
                    <button class="btn btn-linkedin">
                        <i class="fab fa-linkedin-in"></i> Continue with LinkedIn
                    </button>
                </div>
                
                <div class="auth-footer">
                    <p>Don't have an account? <a href="signup.php">Sign up</a></p>
                </div>
            </div>
            
            <div class="auth-image">
                <img src="images/img5.png" alt="Login to Skill India">
            </div>
        </div>
    </section>

    <script src="js/auth.js"></script>
</body>
</html>