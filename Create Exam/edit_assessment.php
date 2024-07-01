<?php
require "db.php"; 

// Retrieve assessment ID from the query string
$assessmentID = isset($_GET['id']) ? $_GET['id'] : '';

if ($assessmentID) {
    // Fetch assessment details
    $assessmentSql = "SELECT * FROM ASSESSMENT WHERE assessment_ID = ?";
    $stmtAssessment = $conn->prepare($assessmentSql);
    $stmtAssessment->bind_param('s', $assessmentID);
    $stmtAssessment->execute();
    $assessmentResult = $stmtAssessment->get_result();
    $assessment = $assessmentResult->fetch_assoc();

    // Fetch questions related to the assessment
    $questionsSql = "SELECT * FROM EXAMINATION_BANK WHERE assessment_ID = ?";
    $stmtQuestions = $conn->prepare($questionsSql);
    $stmtQuestions->bind_param('s', $assessmentID);
    $stmtQuestions->execute();
    $questionsResult = $stmtQuestions->get_result();
    $questions = [];
    while ($row = $questionsResult->fetch_assoc()) {
        
        // Fetch the correct answer for each question
        $answerSql = "SELECT answer FROM EXAM_ANSWER WHERE assessment_ID = ? AND question_ID = ?";
        $stmtAnswer = $conn->prepare($answerSql);
        $stmtAnswer->bind_param('si', $assessmentID, $row['question_ID']);
        $stmtAnswer->execute();
        $answerResult = $stmtAnswer->get_result();
        $answer = $answerResult->fetch_assoc();
        $row['answer'] = $answer['answer'];
        $questions[] = $row;
    }
} else {
    echo "No assessment ID provided.";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Assessment</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .form-group {
            margin-bottom: 10px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="number"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 5px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <h1>Edit Assessment</h1>
    <form id="assessment-form" action="update_assessment.php" method="POST">
        <input type="hidden" name="assessmentID" value="<?php echo htmlspecialchars($assessmentID); ?>">
        <div class="form-group">
            <label for="assessment-name">Assessment Name:</label>
            <input type="text" id="assessment-name" name="assessmentName" value="<?php echo htmlspecialchars($assessment['assessment_Name']); ?>" required>
        </div>
        <div id="questions-container">
            <?php foreach ($questions as $question): ?>
                <div class="form-group">
                    <label for="question-<?php echo $question['question_ID']; ?>">Question:</label>
                    <input type="text" id="question-<?php echo $question['question_ID']; ?>" name="questions[<?php echo $question['question_ID']; ?>][text]" value="<?php echo htmlspecialchars($question['question']); ?>" required>
                    <label for="answer-<?php echo $question['question_ID']; ?>">Correct Answer:</label>
                    <input type="text" id="answer-<?php echo $question['question_ID']; ?>" name="questions[<?php echo $question['question_ID']; ?>][answer]" value="<?php echo htmlspecialchars($question['answer']); ?>" required>
                    <label for="option1-<?php echo $question['question_ID']; ?>">Option 1:</label>
                    <input type="text" id="option1-<?php echo $question['question_ID']; ?>" name="questions[<?php echo $question['question_ID']; ?>][options][1]" value="<?php echo htmlspecialchars($question['choice1']); ?>" required>
                    <label for="option2-<?php echo $question['question_ID']; ?>">Option 2:</label>
                    <input type="text" id="option2-<?php echo $question['question_ID']; ?>" name="questions[<?php echo $question['question_ID']; ?>][options][2]" value="<?php echo htmlspecialchars($question['choice2']); ?>" required>
                    <label for="option3-<?php echo $question['question_ID']; ?>">Option 3:</label>
                    <input type="text" id="option3-<?php echo $question['question_ID']; ?>" name="questions[<?php echo $question['question_ID']; ?>][options][3]" value="<?php echo htmlspecialchars($question['choice3']); ?>">
                    <label for="option4-<?php echo $question['question_ID']; ?>">Option 4:</label>
                    <input type="text" id="option4-<?php echo $question['question_ID']; ?>" name="questions[<?php echo $question['question_ID']; ?>][options][4]" value="<?php echo htmlspecialchars($question['choice4']); ?>">
                </div>
            <?php endforeach; ?>
        </div>
        <button type="button" onclick="addQuestion()">Add Question</button>
        <button type="submit">Save Changes</button>
    </form>

    <script>
        let questionCount = <?php echo count($questions); ?>;

        function addQuestion() {
            questionCount++;
            const questionsContainer = document.getElementById('questions-container');

            const questionDiv = document.createElement('div');
            questionDiv.className = 'form-group';

            questionDiv.innerHTML = `
                <label for="new-question-${questionCount}">New Question ${questionCount}:</label>
                <input type="text" id="new-question-${questionCount}" name="newQuestions[${questionCount}][text]" required>
                <label for="new-answer-${questionCount}">Correct Answer:</label>
                <input type="text" id="new-answer-${questionCount}" name="newQuestions[${questionCount}][answer]" required>
                <label for="new-option1-${questionCount}">Option 1:</label>
                <input type="text" id="new-option1-${questionCount}" name="newQuestions[${questionCount}][options][1]" required>
                <label for="new-option2-${questionCount}">Option 2:</label>
                <input type="text" id="new-option2-${questionCount}" name="newQuestions[${questionCount}][options][2]" required>
                <label for="new-option3-${questionCount}">Option 3:</label>
                <input type="text" id="new-option3-${questionCount}" name="newQuestions[${questionCount}][options][3]" required>
                <label for="new-option4-${questionCount}">Option 4:</label>
                <input type="text" id="new-option4-${questionCount}" name="newQuestions[${questionCount}][options][4]" required>
            `;

            questionsContainer.appendChild(questionDiv);
        }
    </script>
</body>
</html>









