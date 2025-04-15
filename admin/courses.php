<?php
// Include database connection
require_once '../config/config.php';

// Handle AJAX request for courses
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get JSON input
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (isset($data['action']) && $data['action'] === 'get_courses') {
        header('Content-Type: application/json');
        
        try {
            // Enable error reporting for debugging
            error_reporting(E_ALL);
            ini_set('display_errors', 1);
            
            // Fetch all courses
            $stmt = $pdo->prepare("
                SELECT 
                    course_id,
                    title,
                    description,
                    video_count,
                    playlist_link,
                    duration,
                    level,
                    status,
                    created_at,
                    updated_at
                FROM courses
                ORDER BY created_at DESC
            ");
            
            $stmt->execute();
            $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Log the query result for debugging
            error_log("Courses query result: " . print_r($courses, true));
            
            echo json_encode(['success' => true, 'courses' => $courses]);
            exit;
        } catch (PDOException $e) {
            error_log("Error fetching courses: " . $e->getMessage());
            error_log("SQL State: " . $e->errorInfo[0]);
            error_log("Error Code: " . $e->errorInfo[1]);
            error_log("Error Message: " . $e->errorInfo[2]);
            http_response_code(500);
            echo json_encode(['error' => 'Failed to load courses: ' . $e->getMessage()]);
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* General Styles */
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
        }

        /* Content Area */
        .content {
            margin-left: 250px;
            padding: 20px;
            transition: margin-left 0.3s;
        }

        /* Header Actions */
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background: white;
            padding: 15px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .header-actions h1 {
            margin: 0;
            color: #333;
            font-size: 24px;
        }

        /* Table Styles */
        .table-responsive {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            min-width: 800px;
        }

        .table th, .table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #f8f9fa;
            color: #333;
            font-weight: 600;
        }

        .table tr:hover {
            background-color: #f5f5f5;
        }

        /* Button Styles */
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: all 0.3s ease;
        }

        .btn i {
            font-size: 14px;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            width: 50%;
            max-width: 500px;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .modal-actions {
            margin-top: 20px;
            text-align: right;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        /* Utility Classes */
        .text-center {
            text-align: center;
        }

        /* Alert Styles */
        #alertContainer {
            margin-bottom: 20px;
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid transparent;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        /* Loading Spinner */
        .fa-spinner {
            margin-right: 5px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .content {
                margin-left: 0;
            }
            
            .modal-content {
                width: 90%;
                margin: 20% auto;
            }
            
            .header-actions {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }

        .table td, .table th {
            vertical-align: middle;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            text-transform: capitalize;
            background-color: #e9ecef;
        }
        
        .status-badge.published {
            background-color: #28a745;
            color: white;
        }
        
        .status-badge.draft {
            background-color: #ffc107;
            color: #000;
        }
        
        .actions {
            display: flex;
            gap: 5px;
        }
        
        .btn-sm {
            padding: 4px 8px;
            font-size: 12px;
        }
        
        .btn-edit {
            background-color: #007bff;
            color: white;
        }
        
        .btn-delete {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>
<body>
    <?php include './sidebar.php'; ?>
    
    <div class="content">
        <div class="header-actions">
            <h1>Manage Courses</h1>
            <a href="add-course.php" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add New Course
            </a>
        </div>
        
        <!-- Alert container for dynamic messages -->
        <div id="alertContainer"></div>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 20%">Title</th>
                        <th style="width: 25%">Description</th>
                        <th style="width: 8%">Videos</th>
                        <th style="width: 10%">Duration</th>
                        <th style="width: 10%">Level</th>
                        <th style="width: 10%">Status</th>
                        <th style="width: 10%">Created</th>
                        <th style="width: 7%">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="8" class="text-center">
                            <i class="fas fa-spinner fa-spin"></i> Loading courses...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal" id="confirmationModal">
        <div class="modal-content">
            <h3>Confirm Delete</h3>
            <p>Are you sure you want to delete this course? This action cannot be undone.</p>
            <div class="modal-actions">
                <button class="btn btn-secondary" onclick="CourseManager.closeModal()">Cancel</button>
                <button class="btn btn-danger" onclick="CourseManager.confirmDelete()">Delete</button>
            </div>
        </div>
    </div>
    
    <script>
        // Check if user is logged in and is admin
        document.addEventListener('DOMContentLoaded', function() {
            const user = JSON.parse(localStorage.getItem('userData'));
            if (!user || user.role !== 'admin') {
                window.location.href = '../login.php';
                return;
            }
        });
    </script>
    <script src="../js/auth.js"></script>
    <script src="../assets/js/admin/course.js"></script>
</body>
</html> 