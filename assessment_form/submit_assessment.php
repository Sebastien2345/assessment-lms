<?php
require "db.php";

$assessmentID = $_POST['assessmentID'];
$assessmentID = 'A668c22466';
$userID = $_POST['userID']; // Assuming userID is passed in the form
$userID = 'U1234567890';

$totalPoints = 0;
$earnedPoints = 0;

foreach ($_POST as $key => $value) {
    if (strpos($key, 'question-') !== false) {
        $questionID = explode('-', $key)[1];
        $answer = $value;

        // Fetch the correct answer and points from the database
        $stmt = $conn->prepare("SELECT answer, points FROM exam_answer JOIN examination_bank ON exam_answer.question_ID = examination_bank.question_ID WHERE exam_answer.assessment_ID = ? AND exam_answer.question_ID = ?");
        $stmt->bind_param("si", $assessmentID, $questionID);
        $stmt->execute();
        $result = $stmt->get_result();
        $correctAnswer = $result->fetch_assoc();

        $totalPoints += $correctAnswer['points'];

        if ($correctAnswer['answer'] == $answer) {
            $earnedPoints += $correctAnswer['points'];
        }

        $stmt->close();
    }
}

// Save the score and grade
$score = ($earnedPoints / $totalPoints) * 100;
$grade = ($earnedPoints / $totalPoints) * 4; // Assuming grade is out of 4. Adjust as necessary

$stmt = $conn->prepare("INSERT INTO user_exam_report (user_ID, assessment_ID, score, grade, subject_Code, date) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE score = VALUES(score), grade = VALUES(grade)");
$subjectCode = "SUB123"; // Replace with actual subject code
$date = date('Y-m-d');
$stmt->bind_param("sssdss", $userID, $assessmentID, $score, $grade, $subjectCode, $date);
$stmt->execute();
$stmt->close();

$conn->close();

echo "Assessment submitted successfully. You scored $score%.";
?>
