<?php
// Include config and sidebar
require_once 'config/config.php';
include 'sidebar.php';

// Fetch job vacancies from database
$stmt = $pdo->query("SELECT * FROM jobs ORDER BY last_date_to_apply ASC");
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Vacancies - Skill India</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .content-wrapper {
            margin-left: 250px;
            padding: 20px;
        }
        .job-card {
            background-color: #1f2937;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
            color: white;
        }
        .job-card h3 {
            margin-bottom: 10px;
        }
        .job-card p {
            margin: 5px 0;
        }
        .job-card .meta {
            font-size: 14px;
            color: #ccc;
        }
    </style>
</head>
<body>
    <div class="content-wrapper">
        <h1>Available Job Opportunities</h1>
        <?php if (count($jobs) > 0): ?>
            <?php foreach ($jobs as $job): ?>
                <div class="job-card">
                    <h3><?= htmlspecialchars($job['job_title']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                    <p class="meta">
                        <strong>Domain:</strong> <?= htmlspecialchars($job['domain']) ?> | 
                        <strong>Estimated Salary:</strong> <?= htmlspecialchars($job['salary_estimated']) ?> | 
                        <strong>Apply By:</strong> <?= htmlspecialchars($job['last_date_to_apply']) ?>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No job vacancies available at the moment.</p>
        <?php endif; ?>
    </div>
</body>
</html>