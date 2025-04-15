<?php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Skill India</title>
    <link rel="stylesheet" href="css/auth.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

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
                    <a href="login.php" class="btn btn-login">Login</a>
                    <!--  -->
                <?php endif; ?>
            </div>

        </div>
    </nav>

    <!-- Auth Container -->
    <section class="auth-container">
        <div class="container">
            <div class="auth-card">
                <h2>Create Account</h2>
                <p>Join Skill India and start your learning journey today.</p>
                
                <div id="errorMessage" class="alert alert-danger" style="display: none;"></div>
                <div id="successMessage" class="alert alert-success" style="display: none;"></div>
                
                <form id="signupForm">
                    <div class="form-group">
                        <label for="fullName">Full Name</label>
                        <input type="text" id="fullName" name="fullName" placeholder="Enter your full name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="password-field">
                            <input type="password" id="password" name="password" placeholder="Enter your password" required>
                            <div class="password-toggle">
                                <i class="fas fa-eye"></i>
                            </div>
                        </div>
                        <div class="password-strength">
                            <div class="strength-bars">
                                <div class="strength-bar"></div>
                                <div class="strength-bar"></div>
                                <div class="strength-bar"></div>
                                <div class="strength-bar"></div>
                                <div class="strength-bar"></div>
                            </div>
                            <span class="strength-text">Password strength</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword">Confirm Password</label>
                        <div class="password-field">
                            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
                            <div class="password-toggle">
                                <i class="fas fa-eye"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="userType">Account Type</label>
                        <select id="userType" name="userType" required>
                            <option value="user">Student</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    
                    <div class="form-options">
                        <div class="remember-me">
                            <input type="checkbox" id="remember" name="remember">
                            <label for="remember">Remember me</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="terms">
                            <input type="checkbox" id="terms" name="terms" required>
                            <label for="terms">I agree to the <a href="#">Terms & Conditions</a></label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Create Account</button>
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
                    <p>Already have an account? <a href="login.php">Login</a></p>
                </div>
            </div>
            
            <div class="auth-image">
                <img src="images/img6.png" alt="Sign up to Skill India">
            </div>
        </div>
    </section>

    
    <script>
        // Check for any stored messages on page load
        document.addEventListener('DOMContentLoaded', () => {
            // Check for stored messages
            const storedMessage = sessionStorage.getItem('authMessage');
            if (storedMessage) {
                const message = JSON.parse(storedMessage);
                if (message.type === 'error') {
                    Auth.showError(message.text);
                } else if (message.type === 'success') {
                    Auth.showSuccess(message.text);
                }
                sessionStorage.removeItem('authMessage');
            }

            // If user is already logged in, redirect to dashboard
            if (Auth.isLoggedIn()) {
                const user = Auth.getUser();
                if (user.role === 'admin') {
                    window.location.href = 'admin/dashboard.php';
                } else {
                    window.location.href = 'dashboard.php';
                }
            }
        });

        // Password toggle functionality
        document.querySelectorAll('.password-toggle').forEach(toggle => {
            toggle.addEventListener('click', function() {
                const passwordInput = this.previousElementSibling;
                const icon = this.querySelector('i');
                
                if (passwordInput.type === 'password') {
                    passwordInput.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    passwordInput.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Password strength checker
        const passwordInput = document.getElementById('password');
        const strengthBars = document.querySelectorAll('.strength-bar');
        const strengthText = document.querySelector('.strength-text');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength++;
            
            // Uppercase check
            if (/[A-Z]/.test(password)) strength++;
            
            // Lowercase check
            if (/[a-z]/.test(password)) strength++;
            
            // Numbers check
            if (/[0-9]/.test(password)) strength++;
            
            // Special characters check
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            // Update strength bars
            strengthBars.forEach((bar, index) => {
                if (index < strength) {
                    bar.style.backgroundColor = getStrengthColor(strength);
                } else {
                    bar.style.backgroundColor = '#ddd';
                }
            });

            // Update strength text
            strengthText.textContent = getStrengthText(strength);
            strengthText.style.color = getStrengthColor(strength);
        });

        function getStrengthColor(strength) {
            switch(strength) {
                case 1: return '#ff4444';
                case 2: return '#ffbb33';
                case 3: return '#ffbb33';
                case 4: return '#00C851';
                case 5: return '#007E33';
                default: return '#ddd';
            }
        }

        function getStrengthText(strength) {
            switch(strength) {
                case 1: return 'Very Weak';
                case 2: return 'Weak';
                case 3: return 'Medium';
                case 4: return 'Strong';
                case 5: return 'Very Strong';
                default: return 'Password strength';
            }
        }

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Check for logged in user
            const userData = localStorage.getItem('userData');
            if (userData) {
                const user = JSON.parse(userData);
                if (user.role === 'admin') {
                    window.location.replace('admin/dashboard.php');
                } else {
                    window.location.replace('dashboard.php');
                }
                return;
            }
        });
        </script>
    </script>
    <script src="js/auth.js"></script>
    <script src="js/script.js"></script>
</body>
</html>