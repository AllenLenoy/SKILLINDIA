<?php
require_once 'config.php';
require_once 'vendor/autoload.php';

$course_id = $_GET['course_id'] ?? null;
$user_id = $_GET['user_id'] ?? null;

if (!$course_id || !$user_id) {
    header('Location: my-courses.php');
    exit;
}

// Get course details
$stmt = $pdo->prepare("SELECT title, description FROM courses WHERE course_id = ?");
$stmt->execute([$course_id]);
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    header('Location: my-courses.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Test - <?php echo htmlspecialchars($course['title']); ?></title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/test.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="dashboard-container">
        
        <div class="dashboard-content">
            <div class="test-container">
                <h1><?php echo htmlspecialchars($course['title']); ?> - Test</h1>
                <div id="test-instructions">
                    <h3>Instructions:</h3>
                    <ul>
                        <li>This test contains 10 multiple choice questions</li>
                        <li>Each question carries 5 marks</li>
                        <li>You need 60% marks to pass the test</li>
                        <li>You cannot go back to previous questions</li>
                    </ul>
                    <button id="start-test" class="btn btn-primary">Start Test</button>
                </div>
                <div id="question-container" style="display: none;">
                    <div id="question-text"></div>
                    <div id="options-container"></div>
                    <button id="next-question" class="btn btn-primary">Next Question</button>
                </div>
                <div id="result-container" style="display: none;">
                    <h2>Test Results</h2>
                    <div id="score-display"></div>
                    <div id="pass-fail-message"></div>
                    <button id="finish-test" class="btn btn-primary">Return to Course</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const courseId = <?php echo $course_id; ?>;
        const userId = <?php echo $user_id; ?>;
        let questions = [];
        let currentQuestion = 0;
        let score = 0;

        document.getElementById('start-test').addEventListener('click', startTest);
        document.getElementById('next-question').addEventListener('click', handleNextQuestion);
        document.getElementById('finish-test').addEventListener('click', () => {
            window.location.href = `course-details.php?id=${courseId}`;
        });

        async function startTest() {
            try {
                console.log('Starting test...');
                const response = await fetch('api/generate-questions.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        course_id: courseId,
                        title: <?php echo json_encode($course['title']); ?>,
                        description: <?php echo json_encode($course['description']); ?>
                    })
                });

                console.log('Response received');
                const data = await response.json();
                console.log('Response data:', data);

                if (data.success) {
                    questions = data.questions;
                    document.getElementById('test-instructions').style.display = 'none';
                    document.getElementById('question-container').style.display = 'block';
                    displayQuestion();
                } else {
                    throw new Error(data.error || 'Failed to generate questions');
                }
            } catch (error) {
                console.error('Error in startTest:', error);
                alert('Failed to load test questions: ' + error.message);
            }
        }

        function displayQuestion() {
            const question = questions[currentQuestion];
            document.getElementById('question-text').innerHTML = `
                <h3>Question ${currentQuestion + 1} of 10</h3>
                <p>${question.question}</p>
            `;

            const optionsContainer = document.getElementById('options-container');
            optionsContainer.innerHTML = '';
            question.options.forEach((option, index) => {
                optionsContainer.innerHTML += `
                    <div class="option">
                        <input type="radio" name="answer" value="${index}" id="option${index}">
                        <label for="option${index}">${option}</label>
                    </div>
                `;
            });

            // Change the button text and handler for the last question
            const buttonContainer = document.getElementById('next-question');
            if (currentQuestion === questions.length - 1) {
                buttonContainer.textContent = 'Submit Test';
                buttonContainer.style.backgroundColor = '#28a745';
            } else {
                buttonContainer.textContent = 'Next Question';
                buttonContainer.style.backgroundColor = '#007bff';
            }
            buttonContainer.style.display = 'block';
        }

        function handleNextQuestion() {
            const selectedAnswer = document.querySelector('input[name="answer"]:checked');
            if (!selectedAnswer) {
                alert('Please select an answer');
                return;
            }

            if (parseInt(selectedAnswer.value) === questions[currentQuestion].correct_answer) {
                score += 5;
            }

            currentQuestion++;
            
            if (currentQuestion < questions.length) {
                displayQuestion();
            } else {
                showResults();
            }
        }

        async function showResults() {
            const passed = score >= 30; // 60% of 50 marks
            document.getElementById('question-container').style.display = 'none';
            document.getElementById('result-container').style.display = 'block';
            
            document.getElementById('score-display').innerHTML = `
                <p>Your score: ${score} out of 50</p>
                <p>Percentage: ${(score/50*100).toFixed(1)}%</p>
            `;
            
            document.getElementById('pass-fail-message').innerHTML = passed ?
                '<p class="success">Congratulations! You have passed the test.</p>' :
                '<p class="failure">Unfortunately, you did not pass the test. Please try again.</p>';

            if (passed) {
                try {
                    const response = await fetch('api/update-test-status.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            course_id: courseId,
                            user_id: userId
                        })
                    });
                    
                    const result = await response.json();
                    if (!result.success) {
                        console.error('Failed to update test status:', result.error);
                    }
                } catch (error) {
                    console.error('Error updating test status:', error);
                }
            }
        }
    </script>
</body>
</html>