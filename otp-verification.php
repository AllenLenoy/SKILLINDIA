<?php
// Add this at the very top of the file
if (!isset($_SERVER['HTTP_REFERER']) || strpos($_SERVER['HTTP_REFERER'], 'signup.php') === false) {
    header('Location: signup.php');
    exit;
}
?>
// Remove any PHP session handling
// Just include necessary headers and HTML structure
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Verification</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="container">
        <h2>OTP Verification</h2>
        <form id="otpForm">
            <div id="otpInputs" class="otp-inputs">
                <input type="text" maxlength="1" required>
                <input type="text" maxlength="1" required>
                <input type="text" maxlength="1" required>
                <input type="text" maxlength="1" required>
                <input type="text" maxlength="1" required>
                <input type="text" maxlength="1" required>
            </div>
            <button type="submit">Verify OTP</button>
            <div id="errorMessage" style="display:none; color:red;"></div>
            <div id="successMessage" style="display:none; color:green;"></div>
        </form>
        <button id="resendOTP">Resend OTP</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tempUser = JSON.parse(sessionStorage.getItem('temp_user'));
            console.log('Temporary user data:', tempUser);
            
            if (!tempUser) {
                window.location.replace('signup.php');
                return;
            }
            
            const otpForm = document.getElementById('otpForm');
            
            otpForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                
                // Collect OTP values
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
                    document.getElementById('errorMessage').textContent = 'Please enter a valid 6-digit OTP';
                    document.getElementById('errorMessage').style.display = 'block';
                    return;
                }
            

            try {
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
                    
                    // Store user data in localStorage for persistent session
                    localStorage.setItem('userData', JSON.stringify(data.user));
                    
                    document.getElementById('successMessage').textContent = 'Email verified! Redirecting to dashboard...';
                    document.getElementById('successMessage').style.display = 'block';
                    
                    // Redirect based on role
                    setTimeout(() => {
                        if (data.user.role === 'admin') {
                            window.location.replace('admin/dashboard.php');
                        } else {
                            window.location.replace('dashboard.php');
                        }
                    }, 1500);
                } else {
                    document.getElementById('errorMessage').textContent = data.message || 'OTP verification failed. Please try again.';
                    document.getElementById('errorMessage').style.display = 'block';
                }
            } catch (error) {
                console.error('OTP verification error:', error);
                document.getElementById('errorMessage').textContent = 'An error occurred. Please try again.';
                document.getElementById('errorMessage').style.display = 'block';
            }
        });
    </script>
    <script src="js/auth.js"></script>
</body>
</html>