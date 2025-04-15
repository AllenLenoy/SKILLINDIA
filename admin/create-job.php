<?php
require_once '../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Job Vacancies</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .container {
            margin-left: 250px;
            padding: 2rem;
        }
        .job-listing {
            background: #fff;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #4CAF50;
        }
        .job-listing h3 {
            color: #2c3e50;
            margin-top: 0;
            margin-bottom: 0.5rem;
        }
        .job-listing p {
            margin: 0.5rem 0;
            color: #555;
        }
        .job-listing strong {
            color: #2c3e50; /* Darker color for better visibility */
            font-weight: 600;
        }
        .job-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
            margin-top: 1rem;
        }
        .job-meta div {
            background: #f5f5f5;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.9rem;
            color: #333; /* Ensure text is visible */
        }
        .job-meta div strong {
            color: #2c3e50; /* Match the dark color */
        }
        .create-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 1.5rem;
            font-size: 16px;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .create-btn:hover {
            background-color: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .modal2 {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            justify-content: center;
            align-items: center;
        }
        .modal2.show {
            display: flex;
        }
        .modal-content1 {
            background-color: #fefefe;
            margin: auto;
            padding: 30px;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            position: relative;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .close-btn {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #aaa;
            transition: color 0.3s;
        }
        .close-btn:hover {
            color: #333;
        }
        .form-group {
            margin-bottom: 1.2rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        input, textarea, select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 16px;
            transition: border 0.3s;
            color: #333; /* Ensure input text is visible */
        }
        input:focus, textarea:focus, select:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
        }
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        button[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 14px;
            width: 100%;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            font-weight: 600;
            transition: background-color 0.3s;
        }
        button[type="submit"]:hover {
            background-color: #45a049;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            border-left: 4px solid #28a745;
        }
        /* Specific fixes for date input */
        input[type="date"] {
            color: #333 !important; /* Force dark color for date text */
        }
        input[type="date"]::-webkit-calendar-picker-indicator {
            filter: invert(0.5); /* Make calendar icon visible on light background */
        }
    </style>
</head>
<body>
<?php include('./sidebar.php'); ?>

<div class="container">
    <h2>Job Vacancies</h2>
    <button class="create-btn" onclick="openModal()">
        <i class="fas fa-plus"></i> Create Job Vacancy
    </button>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $salary = $_POST['salary'];
        $last_date = $_POST['last_date'];
        $domain = $_POST['domain'];

        $stmt = $pdo->prepare("INSERT INTO jobs (job_title, description, salary_estimated, last_date_to_apply, domain) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $salary, $last_date, $domain]);

        echo '<div class="success-message">Job Vacancy Created Successfully!</div>';
    }

    $stmt = $pdo->query("SELECT * FROM jobs ORDER BY job_id DESC");
    while ($job = $stmt->fetch()) {
        echo "<div class='job-listing'>
            <h3>{$job['job_title']}</h3>
            <p>{$job['description']}</p>
            <div class='job-meta'>
                <div><strong>Domain:</strong> <span style='color:#2c3e50'>{$job['domain']}</span></div>
                <div><strong>Salary:</strong> <span style='color:#2c3e50'>₹" . number_format($job['salary_estimated']) . "</span></div>
                <div><strong>Apply Before:</strong> <span style='color:#2c3e50'>" . date('M d, Y', strtotime($job['last_date_to_apply'])) . "</span></div>
            </div>
        </div>";
    }
    ?>
</div>

<!-- Modal -->
<div class="modal2" id="jobModal">
    <div class="modal-content1">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <h3>Create New Job Vacancy</h3>
        <form method="POST" action="">
            <div class="form-group">
                <label for="title">Job Title</label>
                <input type="text" id="title" name="title" placeholder="e.g., Senior Web Developer" required>
            </div>
            
            <div class="form-group">
                <label for="description">Job Description</label>
                <textarea id="description" name="description" placeholder="Detailed job responsibilities and requirements" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="salary">Estimated Salary (₹)</label>
                <input type="number" id="salary" name="salary" placeholder="e.g., 50000" required style="color:#333">
            </div>
            
            <div class="form-group">
                <label for="last_date">Last Date to Apply</label>
                <input type="date" id="last_date" name="last_date" required style="color:#333">
            </div>
            
            <div class="form-group">
                <label for="domain">Domain</label>
                <input type="text" id="domain" name="domain" placeholder="e.g., Web Development, Data Science" required style="color:#333">
            </div>
            
            <button type="submit">Post Job</button>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('jobModal').classList.add('show');
        // Set default date to today + 30 days
        const today = new Date();
        const futureDate = new Date(today);
        futureDate.setDate(today.getDate() + 30);
        
        // Format date as YYYY-MM-DD for the input field
        const formattedDate = futureDate.toISOString().split('T')[0];
        document.getElementById('last_date').value = formattedDate;
    }

    function closeModal() {
        document.getElementById('jobModal').classList.remove('show');
    }

    // Close modal when clicking outside of it
    window.onclick = function(event) {
        const modal = document.getElementById('jobModal');
        if (event.target === modal) {
            closeModal();
        }
    }

    // Close modal with Escape key
    document.addEventListener('keydown', function(evt) {
        evt = evt || window.event;
        if (evt.key === "Escape") {
            closeModal();
        }
    });
</script>
</body>
</html>