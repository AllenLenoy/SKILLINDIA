// Create Auth object first so it's immediately available
window.Auth = {
    // Keep all existing methods in one place
    setUser: function(userData) {
        localStorage.setItem('userData', JSON.stringify(userData));
    },

    getUser: function() {
        const userData = localStorage.getItem('userData');
        return userData ? JSON.parse(userData) : null;
    },

    isLoggedIn: function() {
        return this.getUser() !== null;
    },

    isAdmin: function() {
        const user = this.getUser();
        return user && user.role === 'admin';
    },

    logout: function() {
        localStorage.removeItem('userData');
        window.location.href = 'login.php';
    },

    checkAuth: function() {
        const currentPage = window.location.pathname.split('/').pop();
        if (currentPage === 'login.php' || currentPage === 'signup.php') {
            return true;
        }

        const user = this.getUser();
        if (!user) {
            window.location.href = 'login.php';
            return false;
        }
        return true;
    },

    redirectIfLoggedIn: function() {
        const user = this.getUser();
        if (user) {
            if (user.role === 'admin') {
                window.location.replace('admin/dashboard.php');
            } else {
                window.location.replace('dashboard.php');
            }
            return true;
        }
        return false;
    },

    showError: function(message) {
        const errorDiv = document.querySelector('.error-message');
        if (errorDiv) {
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            setTimeout(() => {
                errorDiv.style.display = 'none';
            }, 5000);
        } else {
            alert(message);
        }
    },

    showSuccess: function(message) {
        const successDiv = document.querySelector('.success-message');
        if (successDiv) {
            successDiv.textContent = message;
            successDiv.style.display = 'block';
            setTimeout(() => {
                successDiv.style.display = 'none';
            }, 5000);
        } else {
            alert(message);
        }
    }
};

// Initialize event handlers
(function() {
    if (window.authInitialized) return;
    window.authInitialized = true;

    // DOM Elements
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const otpForm = document.getElementById('otpForm');
    const resendOTPLinks = document.querySelectorAll('#resendOTP');
    const otpModal = document.getElementById('otpModal');

    // Login Form Submission
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Get form values
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            
            // Simple validation
            if (!email || !password) {
                Auth.showError('Please fill in all fields');
                return;
            }
            
            try {
                // Show loading state
                const submitButton = loginForm.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
                
                // Submit the form
                const formData = new FormData(loginForm);
                formData.append('login', '1');
                
                const response = await fetch('auth.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                // Reset button state
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
                
                if (data.success) {
                    // Store user data in localStorage
                    Auth.setUser(data.user);
                    Auth.showSuccess('Login successful! Redirecting...');
                    
                    // Redirect based on role
                    if (data.user.role === 'admin') {
                        window.location.href = '/skillindia/admin/dashboard.php';
                    } else {
                        window.location.href = '/skillindia/dashboard.php';
                    }
                } else {
                    Auth.showError(data.message || 'Login failed. Please try again.');
                }
            } catch (error) {
                console.error('Login error:', error);
                Auth.showError('An error occurred. Please try again.');
            }
        });
    }

    // Signup Form Submission
    if (signupForm) {
        signupForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const name = document.getElementById('fullName').value;
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;
            const role = document.getElementById('userType').value;
            
            try {
                const submitButton = signupForm.querySelector('button[type="submit"]');
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Account...';
                
                const formData = new FormData();
                formData.append('register', '1');
                formData.append('name', name);
                formData.append('email', email);
                formData.append('password', password);
                formData.append('role', role);
                
                const response = await fetch('auth.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Store user data directly in localStorage
                    localStorage.setItem('userData', JSON.stringify(data.user));
                    
                    // Redirect based on role
                    if (data.user.role === 'admin') {
                        window.location.replace('admin/dashboard.php');
                    } else {
                        window.location.replace('dashboard.php');
                    }
                } else {
                    Auth.showError(data.message || 'Registration failed.');
                }
            } catch (error) {
                Auth.showError('An error occurred. Please try again.');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Create Account';
            }
        });
    }

    // OTP Form Submission
    if (otpForm) {
        otpForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Get OTP values
            const otpInputs = otpForm.querySelectorAll('input[type="text"]');
            let otp = '';
            let isValid = true;
            
            otpInputs.forEach(input => {
                if (!input.value || !/^[0-9]$/.test(input.value)) {
                    isValid = false;
                    input.classList.add('error');
                } else {
                    input.classList.remove('error');
                    otp += input.value;
                }
            });
            
            // Validate OTP
            if (!isValid || otp.length !== 6) {
                Auth.showError('Please enter a valid 6-digit OTP');
                return;
            }
            
            try {
                // Get temporary user data
                const tempUser = JSON.parse(sessionStorage.getItem('temp_user'));
                if (!tempUser) {
                    Auth.showError('Session expired. Please register again.');
                    window.location.href = 'signup.php';
                    return;
                }

                // Show loading state
                const submitButton = otpForm.querySelector('button[type="submit"]');
                const originalButtonText = submitButton.innerHTML;
                submitButton.disabled = true;
                submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
                
                // Submit the OTP
                const formData = new FormData();
                formData.append('verify_otp', '1');
                formData.append('otp', otp);
                formData.append('email', tempUser.email);
                formData.append('name', tempUser.name);
                formData.append('password', tempUser.password);
                formData.append('role', tempUser.role);
                formData.append('stored_otp', tempUser.otp);
                
                const response = await fetch('auth.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                // Reset button state
                submitButton.disabled = false;
                submitButton.innerHTML = originalButtonText;
                
                if (data.success) {
                    // Clear temporary data
                    sessionStorage.removeItem('temp_user');
                    
                    // Store user data
                    Auth.setUser(data.user);
                    
                    Auth.showSuccess('Email verified! Redirecting to dashboard...');
                    
                    // Redirect based on role
                    setTimeout(() => {
                        if (data.user.role === 'admin') {
                            window.location.href = 'admin/dashboard.php';
                        } else {
                            window.location.href = 'dashboard.php';
                        }
                    }, 1500);
                } else {
                    Auth.showError(data.message || 'OTP verification failed. Please try again.');
                }
            } catch (error) {
                console.error('OTP verification error:', error);
                Auth.showError('An error occurred. Please try again.');
            }
        });
    }

    // OTP Input Navigation
    const otpInputs = document.querySelectorAll('.otp-inputs input');
    if (otpInputs.length > 0) {
        otpInputs.forEach((input, index) => {
            // Handle input
            input.addEventListener('input', (e) => {
                if (e.target.value.length === 1) {
                    if (index < otpInputs.length - 1) {
                        otpInputs[index + 1].focus();
                    }
                }
            });
            
            // Handle backspace
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });
        });
    }

    // Resend OTP
    const resendOTP = document.getElementById('resendOTP');
    if (resendOTP) {
        resendOTP.addEventListener('click', async (e) => {
            e.preventDefault();
            
            try {
                const tempUser = JSON.parse(sessionStorage.getItem('temp_user'));
                if (!tempUser) {
                    Auth.showError('Session expired. Please register again.');
                    window.location.href = 'signup.php';
                    return;
                }

                // Show loading state
                resendOTP.disabled = true;
                resendOTP.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
                
                const formData = new FormData();
                formData.append('resend_otp', '1');
                formData.append('email', tempUser.email);
                
                const response = await fetch('auth.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                // Reset button state
                resendOTP.disabled = false;
                resendOTP.innerHTML = 'Resend OTP';
                
                if (data.success) {
                    // Update stored OTP
                    tempUser.otp = data.otp;
                    sessionStorage.setItem('temp_user', JSON.stringify(tempUser));
                    Auth.showSuccess('New OTP sent successfully!');
                } else {
                    Auth.showError(data.message || 'Failed to resend OTP. Please try again.');
                }
            } catch (error) {
                console.error('Resend OTP error:', error);
                Auth.showError('An error occurred. Please try again.');
            }
        });
    }

    // Password strength indicator
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', function() {
            const strengthBars = document.querySelectorAll('.strength-bar');
            const strengthText = document.querySelector('.strength-text');
            const password = this.value;
            
            // Reset strength indicators
            strengthBars.forEach(bar => bar.style.backgroundColor = '#ddd');
            strengthText.textContent = 'Password strength';
            
            if (password.length > 0) {
                let strength = 0;
                
                // Check for lowercase letters
                if (/[a-z]/.test(password)) strength++;
                // Check for uppercase letters
                if (/[A-Z]/.test(password)) strength++;
                // Check for numbers
                if (/[0-9]/.test(password)) strength++;
                // Check for special characters
                if (/[^A-Za-z0-9]/.test(password)) strength++;
                // Check for minimum length
                if (password.length >= 8) strength++;
                
                // Update strength indicators
                for (let i = 0; i < strength; i++) {
                    strengthBars[i].style.backgroundColor = '#00b894';
                }
                
                // Update strength text
                const strengthLevels = ['Very Weak', 'Weak', 'Medium', 'Strong', 'Very Strong'];
                strengthText.textContent = strengthLevels[strength - 1] || 'Password strength';
            }
        });
    }

    // Password Toggle
    const passwordToggles = document.querySelectorAll('.password-toggle');
    passwordToggles.forEach(toggle => {
        toggle.addEventListener('click', () => {
            const input = toggle.previousElementSibling;
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            toggle.querySelector('i').classList.toggle('fa-eye');
            toggle.querySelector('i').classList.toggle('fa-eye-slash');
        });
    });

    // User Type Selection Animation
    const userTypeSelect = document.getElementById('userType');
    if (userTypeSelect) {
        userTypeSelect.addEventListener('change', () => {
            userTypeSelect.style.color = 'var(--text-primary)';
        });
    }

    // Check authentication on page load for all pages
    document.addEventListener('DOMContentLoaded', function() {
        // Check authentication on page load
        if (window.location.pathname.includes('login.php')) {
            Auth.redirectIfLoggedIn();
        } else {
            Auth.checkAuth();
        }
    });
})();
