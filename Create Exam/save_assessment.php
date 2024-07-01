<?php
require "db.php";

// Retrieve data from POST request
$assessmentName = $_POST['assessmentName'];
$questions = json_decode($_POST['questions'], true);
$creatorID = '1'; // Replace with actual creator ID
$subjectCode = 'SUB123'; // Replace with actual subject code
$assessmentType = 'Q'; // REPLACE
$timeLimit = '30'; // TODO: Add time limit input
$noOfItems = count($questions); 

// Insert assessment
$assessmentID = uniqid('A');
$date = date('Y-m-d');

$sql = "INSERT INTO ASSESSMENT (assessment_ID, assessment_Name, date, creator_ID, subject_Code, assessment_Type, time_Limit, no_Of_Items) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param('ssssssss', $assessmentID, $assessmentName, $date, $creatorID, $subjectCode, $assessmentType, $timeLimit, $noOfItems);

if ($stmt->execute()) {
    $insertQuestionSql = "INSERT INTO EXAMINATION_BANK (assessment_ID, question_ID, question_No, question, points, question_Type, choice1, choice2, choice3, choice4) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsertQuestion = $conn->prepare($insertQuestionSql);

    $insertAnswerSql = "INSERT INTO EXAM_ANSWER (assessment_ID, question_ID, answer) VALUES (?, ?, ?)";
    $stmtInsertAnswer = $conn->prepare($insertAnswerSql);

    foreach ($questions as $index => $question) {
        $questionID = $index + 1;
        $questionNo = $index + 1;
        $questionText = $question['text'];
        $points = 1;
        $questionType = 'M'; 

        // Choices 
        $choice1 = isset($question['options'][0]) ? $question['options'][0] : '';
        $choice2 = isset($question['options'][1]) ? $question['options'][1] : '';
        $choice3 = isset($question['options'][2]) ? $question['options'][2] : '';
        $choice4 = isset($question['options'][3]) ? $question['options'][3] : '';

        // INSERT INTO EXAMINATION_BANK
        $stmtInsertQuestion->bind_param('siisssssss', $assessmentID, $questionID, $questionNo, $questionText, $points, $questionType, $choice1, $choice2, $choice3, $choice4);
        if (!$stmtInsertQuestion->execute()) {
            echo "Error inserting question: " . $stmtInsertQuestion->error;
            exit;
        }

    
        $correctAnswer = isset($question['correctAnswer']) ? $question['correctAnswer'] : '';

        // INSERT INTO EXAM_ANSWER
        $stmtInsertAnswer->bind_param('sis', $assessmentID, $questionID, $correctAnswer);
        if (!$stmtInsertAnswer->execute()) {
            echo "Error inserting correct answer: " . $stmtInsertAnswer->error;
            exit;
        }
    }

    echo "Assessment created successfully";
} else {
    echo "Error creating assessment: " . $stmt->error;
}

$stmt->close();
$stmtInsertQuestion->close();
$stmtInsertAnswer->close();
$conn->close();
?>
