<?php
require_once 'config.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

function generateOTP() {
    return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
}

function sendOTPEmail($email, $otp) {
    error_log("OTP for $email: $otp");
    return true;
}

// Handle login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            echo json_encode([
                'success' => true,
                'user' => $user
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid email or password'
            ]);
        }
    } catch (PDOException $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        ]);
    }
    exit;
}

// Handle registration
else if (isset($_POST['register'])) {
    try {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'] ?? 'user';

        // Check if email exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Email already registered'
            ]);
            exit;
        }

        // Hash password and insert user
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, is_verified) VALUES (?, ?, ?, ?, true)");
        
        if ($stmt->execute([$name, $email, $hashedPassword, $role])) {
            $userId = $pdo->lastInsertId();
            echo json_encode([
                'success' => true,
                'message' => 'Registration successful',
                'user' => [
                    'user_id' => $userId,
                    'name' => $name,
                    'email' => $email,
                    'role' => $role,
                    'is_verified' => true
                ]
            ]);
        } else {
            throw new Exception("Failed to create user");
        }
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Registration failed. Please try again.'
        ]);
    }
    exit;
}

// Handle OTP verification
else if (isset($_POST['verify_otp'])) {
    try {
        $email = $_POST['email'];
        $otp = $_POST['otp'];
        $name = $_POST['name'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        
        if ($otp === $_POST['stored_otp']) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$name, $email, $hashedPassword, $role])) {
                $userId = $pdo->lastInsertId();
                echo json_encode([
                    'success' => true,
                    'message' => 'Email verified successfully',
                    'user' => [
                        'user_id' => $userId,
                        'name' => $name,
                        'email' => $email,
                        'role' => $role
                    ]
                ]);
            } else {
                throw new Exception("Failed to create user");
            }
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid OTP'
            ]);
        }
    } catch (Exception $e) {
        error_log("OTP verification error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Verification failed. Please try again.'
        ]);
    }
    exit;
}

// Handle resend OTP
else if (isset($_POST['resend_otp'])) {
    try {
        $email = $_POST['email'];
        $otp = generateOTP();
        
        echo json_encode([
            'success' => true,
            'message' => 'New OTP sent successfully',
            'otp' => $otp
        ]);
    } catch (Exception $e) {
        error_log("Resend OTP error: " . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => 'Failed to resend OTP. Please try again.'
        ]);
    }
    exit;
}
?>