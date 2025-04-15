<?php
require_once 'config.php';


$course_id = $_GET['course_id'] ?? null;
$user_id = $_GET['user_id'] ?? null;

if (!$course_id || !$user_id) {
    header('Location: my-courses.php');
    exit;
}

// Get course and user details
// Update the SQL query to include duration
$stmt = $pdo->prepare("
    SELECT c.title as course_title, c.description, c.duration,
           u.name as student_name, e.completion_date, e.test_given
    FROM courses c
    JOIN enrollments e ON c.course_id = e.course_id
    JOIN users u ON e.user_id = u.user_id
    WHERE c.course_id = ? AND e.user_id = ? AND e.status = 'completed' AND e.test_given = 1
");
$stmt->execute([$course_id, $user_id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    header('Location: my-courses.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Completion Certificate</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <style>
        .certificate-container {
            width: 1000px;
            height: 700px;
            margin: 20px auto;
            padding: 40px;
            border: 15px solid #0066cc;
            background: #fff;
            box-shadow: 0 0 20px rgba(0,0,0,0.2);
            position: relative;
        }
        .certificate {
            text-align: center;
            color: #333;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .certificate-header {
            font-size: 42px;
            color: #0066cc;
            margin-bottom: 10px;
            font-weight: bold;
        }
        .certificate-body {
            font-size: 20px;
            line-height: 1.4;
            margin: 10px 0;
        }
        .certificate-body br {
            line-height: 1;
        }
        .certificate-body strong {
            font-size: 24px;
            color: #0066cc;
        }
        .certificate-seal {
            width: 120px;
            height: 120px;
            margin: 10px auto;
            background: #0066cc;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 20px;
            transform: rotate(-15deg);
        }
        .certificate-footer {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            padding: 0 80px;
        }
        .signature {
            border-top: 2px solid #333;
            padding-top: 10px;
            width: 180px;
            font-size: 18px;
        }
    </style>
</head>
<style>
    .certificate-container {
        width: 1000px;
        height: 700px;
        margin: 20px auto;
        padding: 40px;
        border: 15px solid #0066cc;
        background: #fff;
        box-shadow: 0 0 20px rgba(0,0,0,0.2);
        position: relative;
    }
    .certificate {
        text-align: center;
        color: #333;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .certificate-header {
        font-size: 42px;
        color: #0066cc;
        margin-bottom: 10px;
        font-weight: bold;
    }
    .certificate-body {
        font-size: 20px;
        line-height: 1.4;
        margin: 10px 0;
    }
    .certificate-body br {
        line-height: 1;
    }
    .certificate-body strong {
        font-size: 24px;
        color: #0066cc;
    }
    .certificate-seal {
        width: 120px;
        height: 120px;
        margin: 10px auto;
        background: #0066cc;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 20px;
        transform: rotate(-15deg);
    }
    .certificate-footer {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
        padding: 0 80px;
    }
    .signature {
        border-top: 2px solid #333;
        padding-top: 10px;
        width: 180px;
        font-size: 18px;
    }
    .download-btn {
        position: fixed;
        bottom: 20px;
        right: 20px;
        padding: 15px 30px;
        background: #0066cc;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 18px;
        z-index: 1000;
    }
    .download-btn:hover {
        background: #0052a3;
    }
    
    /* Add container for proper spacing */
    .page-container {
        padding-bottom: 80px;
        position: relative;
        min-height: 100vh;
    }
</style>

<body>
    <div class="page-container">
        <div class="certificate-container" id="certificate">
            <div class="certificate">
                <div class="certificate-header">Certificate of Completion</div>
                <div class="certificate-seal">SkillIndia</div>
                <div class="certificate-body">
                    This is to certify that
                    <br><br>
                    <strong><?php echo htmlspecialchars($data['student_name']); ?></strong>
                    <br><br>
                    has successfully completed the course
                    <br><br>
                    <strong><?php echo htmlspecialchars($data['course_title']); ?></strong>
                    <br>
                    <span style="font-size: 20px;">(Duration: <?php echo htmlspecialchars($data['duration']); ?> hours)</span>
                    <br><br>
                    on
                    <br><br>
                    <strong><?php echo date('F d, Y', strtotime($data['completion_date'])); ?></strong>
                </div>
                <div class="certificate-footer">
                    <div class="signature">
                        Date<br>
                        <?php echo date('d/m/Y'); ?>
                    </div>
                    <div class="signature">
                        Director<br>
                        SkillIndia
                    </div>
                </div>
            </div>
        </div>
        <div class="button-container">
            <a href="dashboard.php" class="dashboard-btn">
                <i class="fas fa-tachometer-alt"></i> Back to Dashboard
            </a>
            <button class="download-btn" onclick="downloadPDF()">
                <i class="fas fa-download"></i> Download Certificate
            </button>
        </div>
    </div>
</body>

    <script>
        function downloadPDF() {
            const element = document.getElementById('certificate');
            const options = {
                margin: 0,
                filename: 'certificate.pdf',
                image: { type: 'jpeg', quality: 1 },
                html2canvas: { 
                    scale: 1,
                    useCORS: true,
                    letterRendering: true,
                    width: 1200,
                    height: 1000
                },
                jsPDF: { 
                    unit: 'px', 
                    format: [1400, 1200],
                    orientation: 'landscape',
                    hotfixes: ["px_scaling"]
                }
            };

            html2pdf().set(options).from(element).save();
        }
    </script>
</body>
</html>