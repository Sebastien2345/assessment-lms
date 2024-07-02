<?php
require "db.php";

$assessmentName = $_POST['assessmentName'];
$questions = $_POST['questions'];
$creatorID = '1'; // Replace with actual creator ID
$subjectCode = 'SUB123'; // Replace with actual subject code
$assessmentType = 'Q';
$timeLimit = '30';
$noOfItems = count($questions);
$assessmentID = uniqid('A');
$date = date('Y-m-d');

$sql = "INSERT INTO ASSESSMENT (assessment_ID, assessment_Name,  date, creator_ID, subject_Code, assessment_Type, time_Limit, no_Of_Items) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssssssss', $assessmentID, $assessmentName, $date, $creatorID, $subjectCode, $assessmentType, $timeLimit, $noOfItems);

if ($stmt->execute()) {
    $insertQuestionSql = "INSERT INTO EXAMINATION_BANK (assessment_ID, question_ID, question_No, question, points, question_Type, choice1, choice2, choice3, choice4, boolean, fill_Blank, match1, match2, match3, match4, match5, match6, match7, match8, match9, match10) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsertQuestion = $conn->prepare($insertQuestionSql);

    $insertAnswerSql = "INSERT INTO EXAM_ANSWER (assessment_ID, question_ID, answer, m_Ans1, m_Ans2, m_Ans3, m_Ans4, m_Ans5, m_Ans6, m_Ans7, m_Ans8, m_Ans9, m_Ans10) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtInsertAnswer = $conn->prepare($insertAnswerSql);

    foreach ($questions as $index => $question) {
        $questionID = $index + 1;
        $questionNo = $index + 1;
        $questionText = $question['text'];
        $points = 1;
        $questionType = $question['type'];

        // Optional fields
        $choice1 = isset($question['options'][0]) ? $question['options'][0] : null;
        $choice2 = isset($question['options'][1]) ? $question['options'][1] : null;
        $choice3 = isset($question['options'][2]) ? $question['options'][2] : null;
        $choice4 = isset($question['options'][3]) ? $question['options'][3] : null;
        $boolean = isset($question['boolean']) ? $question['boolean'] : null;
        $fillBlank = isset($question['fillBlank']) ? $question['fillBlank'] : null;

        // Matching columns
        $match1 = isset($question['match'][0]) ? $question['match'][0] : null;
        $match2 = isset($question['match'][1]) ? $question['match'][1] : null;
        $match3 = isset($question['match'][2]) ? $question['match'][2] : null;
        $match4 = isset($question['match'][3]) ? $question['match'][3] : null;
        $match5 = isset($question['match'][4]) ? $question['match'][4] : null;
        $match6 = isset($question['match'][5]) ? $question['match'][5] : null;
        $match7 = isset($question['match'][6]) ? $question['match'][6] : null;
        $match8 = isset($question['match'][7]) ? $question['match'][7] : null;
        $match9 = isset($question['match'][8]) ? $question['match'][8] : null;
        $match10 = isset($question['match'][9]) ? $question['match'][9] : null;

        $stmtInsertQuestion->bind_param('siisssssssssssssssssss', $assessmentID, $questionID, $questionNo, $questionText, $points, $questionType, $choice1, $choice2, $choice3, $choice4, $boolean, $fillBlank, $match1, $match2, $match3, $match4, $match5, $match6, $match7, $match8, $match9, $match10);

        if (!$stmtInsertQuestion->execute()) {
            echo "Error inserting question: " . $stmtInsertQuestion->error;
            exit;
        }

        $correctAnswer = isset($question['correctAnswer']) ? $question['correctAnswer'] : null;
        $m_Ans1 = isset($question['m_Ans1']) ? $question['m_Ans1'] : null;
        $m_Ans2 = isset($question['m_Ans2']) ? $question['m_Ans2'] : null;
        $m_Ans3 = isset($question['m_Ans3']) ? $question['m_Ans3'] : null;
        $m_Ans4 = isset($question['m_Ans4']) ? $question['m_Ans4'] : null;
        $m_Ans5 = isset($question['m_Ans5']) ? $question['m_Ans5'] : null;
        $m_Ans6 = isset($question['m_Ans6']) ? $question['m_Ans6'] : null;
        $m_Ans7 = isset($question['m_Ans7']) ? $question['m_Ans7'] : null;
        $m_Ans8 = isset($question['m_Ans8']) ? $question['m_Ans8'] : null;
        $m_Ans9 = isset($question['m_Ans9']) ? $question['m_Ans9'] : null;
        $m_Ans10 = isset($question['m_Ans10']) ? $question['m_Ans10'] : null;

        $stmtInsertAnswer->bind_param('sisssssssssss', $assessmentID, $questionID, $correctAnswer, $m_Ans1, $m_Ans2, $m_Ans3, $m_Ans4, $m_Ans5, $m_Ans6, $m_Ans7, $m_Ans8, $m_Ans9, $m_Ans10);

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
