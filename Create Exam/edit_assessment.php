<?php
require "db.php";

$assessmentID = $_GET['id']; // Assuming assessmentID is passed in the URL

$sqlSelectAssessment = "SELECT * FROM ASSESSMENT WHERE assessment_ID = ?";
$stmtSelectAssessment = $conn->prepare($sqlSelectAssessment);
$stmtSelectAssessment->bind_param('s', $assessmentID);
$stmtSelectAssessment->execute();
$resultAssessment = $stmtSelectAssessment->get_result();
$assessment = $resultAssessment->fetch_assoc();

$sqlSelectQuestions = "SELECT * FROM EXAMINATION_BANK WHERE assessment_ID = ?";
$stmtSelectQuestions = $conn->prepare($sqlSelectQuestions);
$stmtSelectQuestions->bind_param('s', $assessmentID);
$stmtSelectQuestions->execute();
$resultQuestions = $stmtSelectQuestions->get_result();

function fetchCorrectAnswers($questionID, $questionType, $conn) {
    $sql = "";
    switch ($questionType) {
        case 'M':
        case 'T':
        case 'S':
            $sql = "SELECT answer FROM EXAM_ANSWER WHERE question_ID = ?";
            break;
        case 'F':
            $sql = "SELECT m_Ans1, m_Ans2, m_Ans3, m_Ans4, m_Ans5, m_Ans6, m_Ans7, m_Ans8, m_Ans9, m_Ans10 FROM EXAM_ANSWER WHERE question_ID = ?";
            break;
        default:
            return ''; // Handle unknown question types
    }

    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $questionID); 
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row; // Return the row for all question types
    } else {
        return ''; // Handle case where no answer is found
    }
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
        input[type="text"], input[type="number"], input[type="date"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 5px;
        }
        button {
            padding: 10px 20px;
            font-size: 16px;
        }
    </style>
    <script>
        let questionCount = <?php echo $resultQuestions->num_rows; ?>;

        function addQuestion() {
            questionCount++;
            const questionsContainer = document.getElementById('new-questions-container');

            const questionDiv = document.createElement('div');
            questionDiv.className = 'form-group';

            questionDiv.innerHTML = 
                '<label for="new-question-' + questionCount + '">New Question ' + questionCount + ':</label>' +
                '<input type="text" id="new-question-' + questionCount + '" name="newQuestions[' + questionCount + '][text]" required>' +
                
                '<label for="new-question-type-' + questionCount + '">Question Type:</label>' +
                '<select id="new-question-type-' + questionCount + '" name="newQuestions[' + questionCount + '][type]" onchange="displayOptions(this, ' + questionCount + ')" required>' +
                    '<option value="">Select Type</option>' +
                    '<option value="M">Multiple Choice</option>' +
                    '<option value="T">True or False</option>' +
                    '<option value="S">Short Answer (Fill-in-the-Blank)</option>' +
                    '<option value="F">Match</option>' +
                '</select>' +

                '<label for="new-question-points-' + questionCount + '">Points:</label>' +
                '<input type="number" id="new-question-points-' + questionCount + '" name="newQuestions[' + questionCount + '][points]" required>' +

                '<div id="options-new-' + questionCount + '" class="options-container"></div>';

            questionsContainer.appendChild(questionDiv);
        }

        function displayOptions(selectElement, count) {
            const selectedType = selectElement.value;
            const optionsContainer = document.getElementById('options-new-' + count);
            optionsContainer.innerHTML = '';

            if (selectedType === 'M') {
                optionsContainer.innerHTML = 
                    '<label for="option1-' + count + '">Option 1:</label>' +
                    '<input type="text" id="option1-' + count + '" name="newQuestions[' + count + '][options][0]" required>' +
                    '<label for="option2-' + count + '">Option 2:</label>' +
                    '<input type="text" id="option2-' + count + '" name="newQuestions[' + count + '][options][1]" required>' +
                    '<label for="option3-' + count + '">Option 3:</label>' +
                    '<input type="text" id="option3-' + count + '" name="newQuestions[' + count + '][options][2]" required>' +
                    '<label for="option4-' + count + '">Option 4:</label>' +
                    '<input type="text" id="option4-' + count + '" name="newQuestions[' + count + '][options][3]" required>' +

                    '<label for="correct-answer-' + count + '">Correct Answer:</label>' +
                    '<select id="correct-answer-' + count + '" name="newQuestions[' + count + '][correctAnswer]" required>' +
                        '<option value="1">Option 1</option>' +
                        '<option value="2">Option 2</option>' +
                        '<option value="3">Option 3</option>' +
                        '<option value="4">Option 4</option>' +
                    '</select>';
            } else if (selectedType === 'T') {
                optionsContainer.innerHTML = 
                    '<label for="boolean-' + count + '">Correct Answer (True/False):</label>' +
                    '<select id="boolean-' + count + '" name="newQuestions[' + count + '][correctAnswer]" required>' +
                        '<option value="T">True</option>' +
                        '<option value="F">False</option>';
                    '</select>';
            } else if (selectedType === 'S') {
                optionsContainer.innerHTML = 
                    '<label for="fill-blank-' + count + '">Correct Answer:</label>' +
                    '<input type="text" id="fill-blank-' + count + '" name="newQuestions[' + count + '][correctAnswer]" required>';
            } else if (selectedType === 'F') {
                for (let i = 1; i <= 10; i++) {
                    optionsContainer.innerHTML += 
                        '<label for="match' + i + '-' + count + '">Match ' + i + ':</label>' +
                        '<input type="text" id="match' + i + '-' + count + '" name="newQuestions[' + count + '][match][' + (i - 1) + ']" ' + (i <= 4 ? 'required' : '') + '>' +
                        
                        '<label for="m-ans' + i + '-' + count + '">Match Correct Answer ' + i + ':</label>' +
                        '<select id="m-ans' + i + '-' + count + '" name="newQuestions[' + count + '][m_Ans' + i + ']" ' + (i <= 4 ? 'required' : '') + '>' +
                            Array.from({ length: 10 }, (_, index) => '<option value="' + (index + 1) + '">Match ' + (index + 1) + '</option>').join('') +
                        '</select>';
                }
            }
        }

        function displayExistingOptions(selectElement, questionID) {
            const selectedType = selectElement.value;
            const optionsContainer = document.getElementById('options-existing-' + questionID);
            optionsContainer.innerHTML = '';

            if (selectedType === 'M') {
                optionsContainer.innerHTML = 
                    '<label for="option1-' + questionID + '">Option 1:</label>' +
                    '<input type="text" id="option1-' + questionID + '" name="questions[' + questionID + '][options][0]" required>' +
                    '<label for="option2-' + questionID + '">Option 2:</label>' +
                    '<input type="text" id="option2-' + questionID + '" name="questions[' + questionID + '][options][1]" required>' +
                    '<label for="option3-' + questionID + '">Option 3:</label>' +
                    '<input type="text" id="option3-' + questionID + '" name="questions[' + questionID + '][options][2]" required>' +
                    '<label for="option4-' + questionID + '">Option 4:</label>' +
                    '<input type="text" id="option4-' + questionID + '" name="questions[' + questionID + '][options][3]" required>' +

                    '<label for="correct-answer-' + questionID + '">Correct Answer:</label>' +
                    '<select id="correct-answer-' + questionID + '" name="questions[' + questionID + '][correctAnswer]" required>' +
                        '<option value="1">Option 1</option>' +
                        '<option value="2">Option 2</option>' +
                        '<option value="3">Option 3</option>' +
                        '<option value="4">Option 4</option>' +
                    '</select>';
            } else if (selectedType === 'T') {
                optionsContainer.innerHTML = 
                    '<label for="boolean-' + questionID + '">Correct Answer (True/False):</label>' +
                    '<select id="boolean-' + questionID + '" name="questions[' + questionID + '][correctAnswer]" required>' +
                        '<option value="T">True</option>' +
                        '<option value="F">False</option>' +
                    '</select>';
            } else if (selectedType === 'S') {
                optionsContainer.innerHTML = 
                    '<label for="fill-blank-' + questionID + '">Correct Answer:</label>' +
                    '<input type="text" id="fill-blank-' + questionID + '" name="questions[' + questionID + '][correctAnswer]" required>';
            } else if (selectedType === 'F') {
                for (let i = 1; i <= 10; i++) {
                    optionsContainer.innerHTML += 
                        '<label for="match' + i + '-' + questionID + '">Match ' + i + ':</label>' +
                        '<input type="text" id="match' + i + '-' + questionID + '" name="questions[' + questionID + '][match][' + (i - 1) + ']" ' + (i <= 4 ? 'required' : '') + '>' +
                        
                        '<label for="m-ans' + i + '-' + questionID + '">Match Correct Answer ' + i + ':</label>' +
                        '<select id="m-ans' + i + '-' + questionID + '" name="questions[' + questionID + '][m_Ans' + i + ']" ' + (i <= 4 ? 'required' : '') + '>' +
                            Array.from({ length: 10 }, (_, index) => '<option value="' + (index + 1) + '">Match ' + (index + 1) + '</option>').join('') +
                        '</select>';
                }
            }
        }
    </script>
</head>
<body>
    <h2>Edit Assessment</h2>
    <form action="update_assessment.php" method="post">
             <input type="hidden" name="assessmentID" value="<?php echo $assessmentID; ?>">

        <div class="form-group">
            <label for="assessmentName">Assessment Name:</label>
            <input type="text" id="assessmentName" name="assessmentName" value="<?php echo isset($assessment['assessment_Name']) ? htmlspecialchars($assessment['assessment_Name']) : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="assessmentDesc">Instructions:</label>
            <input type="text" id="assessmentDesc" name="assessmentDesc" value="<?php echo isset($assessment['assessment_desc']) ? htmlspecialchars($assessment['assessment_desc']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="timeLimit">Time Limit (minutes):</label>
            <input type="number" id="timeLimit" name="timeLimit" value="<?php echo isset($assessment['time_Limit']) ? htmlspecialchars($assessment['time_Limit']) : ''; ?>" required>
        </div>
        <div class="form-group">
            <label for="closingDate">Closing Date:</label>
            <input type="datetime" id="closingDate" name="closingDate" value="<?php echo isset($assessment['closing_date']) ? htmlspecialchars($assessment['closing_date']) : ''; ?>">
        </div>
        <div class="form-group">
            <label for="allowedAttempts">Allowed Attempt:</label> 
            <input type="number" id="allowedAttempts" name="allowedAttempts" value="<?php echo isset($assessment['allowed_attempts']) ? htmlspecialchars($assessment['allowed_attempts']) : ''; ?>" required>
        </div>
        <div id="existing-questions-container">
            <?php while ($question = $resultQuestions->fetch_assoc()): ?>
                <div class="form-group">
                    <label for="question-<?php echo $question['question_ID']; ?>">Question <?php echo $question['question_No']; ?>:</label>
                    <input type="text" id="question-<?php echo $question['question_ID']; ?>" name="questions[<?php echo $question['question_ID']; ?>][text]" value="<?php echo htmlspecialchars($question['question']); ?>" required>
                    
                    <label for="question-type-<?php echo $question['question_ID']; ?>">Question Type:</label>
                    <select id="question-type-<?php echo $question['question_ID']; ?>" name="questions[<?php echo $question['question_ID']; ?>][type]" onchange="displayExistingOptions(this, <?php echo $question['question_ID']; ?>)" required>
                        <option value="M" <?php echo $question['question_Type'] === 'M' ? 'selected' : ''; ?>>Multiple Choice</option>
                        <option value="T" <?php echo $question['question_Type'] === 'T' ? 'selected' : ''; ?>>True or False</option>
                        <option value="S" <?php echo $question['question_Type'] === 'S' ? 'selected' : ''; ?>>Short Answer</option>
                        <option value="F" <?php echo $question['question_Type'] === 'F' ? 'selected' : ''; ?>>Match</option>
                    </select>

                    <!-- Add input field for points -->
                    <label for="points-<?php echo $question['question_ID']; ?>">Points:</label>
                    <input type="number" id="points-<?php echo $question['question_ID']; ?>" name="questions[<?php echo $question['question_ID']; ?>][points]" value="<?php echo htmlspecialchars($question['points']); ?>" required>

                    <div id="options-existing-<?php echo $question['question_ID']; ?>" class="options-container">
                        <?php 
                        $correctAnswers = fetchCorrectAnswers($question['question_ID'], $question['question_Type'], $conn); 
                        if ($question['question_Type'] === 'M'): ?>
                            <label for="option1-<?php echo $question['question_ID']; ?>">Option 1:</label>
                            <input type="text" id="option1-<?php echo $question['question_ID']; ?>" name="questions[<?php echo $question['question_ID']; ?>][options][0]" value="<?php echo htmlspecialchars($question['choice1']); ?>" required>
                            <label for="option2-<?php echo $question['question_ID']; ?>">Option 2:</label>
                            <input type="text" id="option2-<?php echo $question['question_ID']; ?>" name="questions[<?php echo $question['question_ID']; ?>][options][1]" value="<?php echo htmlspecialchars($question['choice2']); ?>" required>
                            <label for="option3-<?php echo $question['question_ID']; ?>">Option 3:</label>
                            <input type="text" id="option3-<?php echo $question['question_ID']; ?>" name="questions[<?php echo $question['question_ID']; ?>][options][2]" value="<?php echo htmlspecialchars($question['choice3']); ?>" required>
                            <label for="option4-<?php echo $question['question_ID']; ?>">Option 4:</label>
                            <input type="text" id="option4-<?php echo $question['question_ID']; ?>" name="questions[<?php echo $question['question_ID']; ?>][options][3]" value="<?php echo htmlspecialchars($question['choice4']); ?>" required>

                            <label for="correct-answer-<?php echo $question['question_ID']; ?>">Correct Answer:</label>
                            <select id="correct-answer-<?php echo $question['question_ID']; ?>" name="questions[<?php echo $question['question_ID']; ?>][correctAnswer]" required>
                                <option value="1" <?php echo $correctAnswers['answer'] == 1 ? 'selected' : ''; ?>>Option 1</option>
                                <option value="2" <?php echo $correctAnswers['answer'] == 2 ? 'selected' : ''; ?>>Option 2</option>
                                <option value="3" <?php echo $correctAnswers['answer'] == 3 ? 'selected' : ''; ?>>Option 3</option>
                                <option value="4" <?php echo $correctAnswers['answer'] == 4 ? 'selected' : ''; ?>>Option 4</option>
                            </select>
                        <?php elseif ($question['question_Type'] === 'T'): ?>
                            <label for="boolean-<?php echo $question['question_ID']; ?>">Correct Answer (True/False):</label>
                            <select id="boolean-<?php echo $question['question_ID']; ?>" name="questions[<?php echo $question['question_ID']; ?>][correctAnswer]" required>
                                <option value="T" <?php echo $correctAnswers['answer'] == 'T' ? 'selected' : ''; ?>>True</option>
                                <option value="F" <?php echo $correctAnswers['answer'] == 'F' ? 'selected' : ''; ?>>False</option>
                            </select>
                        <?php elseif ($question['question_Type'] === 'S'): ?>
                            <label for="fill-blank-<?php echo $question['question_ID']; ?>">Correct Answer:</label>
                            <input type="text" id="fill-blank-<?php echo $question['question_ID']; ?>" name="questions[<?php echo $question['question_ID']; ?>][correctAnswer]" value="<?php echo htmlspecialchars($correctAnswers['answer']); ?>" required>
                        <?php elseif ($question['question_Type'] === 'F'): ?>
                            <?php for ($i = 1; $i <= 10; $i++): ?>
                                <label for="match<?php echo $i; ?>-<?php echo $question['question_ID']; ?>">Match <?php echo $i; ?>:</label>
                                <input type="text" id="match<?php echo $i; ?>-<?php echo $question['question_ID']; ?>" name="questions[<?php echo $question['question_ID']; ?>][match][<?php echo $i - 1; ?>]" value="<?php echo htmlspecialchars($correctAnswers['m_Ans'.$i]); ?>" required>
                                <br>
                            <?php endfor; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <div id="new-questions-container"></div>
        <button type="button" onclick="addQuestion()">Add New Question</button>
        <button type="submit">Save</button>
    </form>
</body>
</html>


