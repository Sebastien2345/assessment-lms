<?php
require "db.php";

$assessmentID = $_POST['assessmentID'];
$userID = $_POST['userID'];

$assessmentID = 'A668c246ea';
$userID = 'U1234567890';

$totalPoints = 0;
$earnedPoints = 0;

// Collect all user answers
$userAnswers = $_POST;

// Fetch all questions related to the assessment
$questionsSQL = "SELECT * FROM examination_bank WHERE assessment_ID = ?";
$stmt = $conn->prepare($questionsSQL);
$stmt->bind_param("s", $assessmentID);
$stmt->execute();
$questionsResult = $stmt->get_result();
$stmt->close();

while ($questionData = $questionsResult->fetch_assoc()) {
    $questionID = $questionData['question_ID'];
    $questionType = $questionData['question_Type'];
    $points = $questionData['points'];

    $totalPoints += $points;

    switch ($questionType) {
        case 'M': // Multiple Choice
        case 'T': // True/False
        case 'S': // Short Answer
            if (isset($userAnswers["question-$questionID"])) {
                $userAnswer = $userAnswers["question-$questionID"];

                // Fetch the correct answer for the question
                $correctAnswerSQL = "SELECT answer FROM exam_answer WHERE assessment_ID = ? AND question_ID = ?";
                $stmt = $conn->prepare($correctAnswerSQL);
                $stmt->bind_param("si", $assessmentID, $questionID);
                $stmt->execute();
                $result = $stmt->get_result();
                $correctAnswerData = $result->fetch_assoc();
                $stmt->close();

                $correctAnswer = $correctAnswerData['answer'];

                if (strcasecmp(trim($userAnswer), trim($correctAnswer)) == 0) {
                    $earnedPoints += $points;
                }
            }
            break;
        case 'F': // Matching
            for ($i = 1; $i <= 10; $i++) {
                if (isset($userAnswers["question-$questionID-match$i"])) {
                    $userAnswer = $userAnswers["question-$questionID-match$i"];

                    // Fetch the correct match answer for the question
                    $correctAnswerSQL = "SELECT m_Ans$i as correct_answer FROM exam_answer WHERE assessment_ID = ? AND question_ID = ?";
                    $stmt = $conn->prepare($correctAnswerSQL);
                    $stmt->bind_param("si", $assessmentID, $questionID);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $correctAnswerData = $result->fetch_assoc();
                    $stmt->close();

                    if ($correctAnswerData && $correctAnswerData['correct_answer'] == $userAnswer) {
                        $earnedPoints += $points / 10; // Assuming each match is worth 1/10th of the question's points
                    }
                }
            }
            break;
    }
}

// Calculate the grade as a percentage
$grade = ($totalPoints > 0) ? ($earnedPoints / $totalPoints) * 100 : 0;

// Save the score (total correct points) and grade (percentage)
$stmt = $conn->prepare("INSERT INTO user_exam_report (user_ID, assessment_ID, score, grade, subject_Code, date) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE score = VALUES(score), grade = VALUES(grade)");
$subjectCode = "SUB123"; // Replace with actual subject code
$date = date('Y-m-d');
$stmt->bind_param("sssdss", $userID, $assessmentID, $earnedPoints, $grade, $subjectCode, $date);
$stmt->execute();
$stmt->close();

$conn->close();

echo "Assessment submitted successfully. You earned $earnedPoints points. Your grade is $grade%.";
?>
