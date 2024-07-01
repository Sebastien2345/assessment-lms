<?php
require "db.php";


$assessmentID = $_POST['assessmentID']; 

if (!empty($assessmentID)) {
    $assessmentName = $_POST['assessmentName'];
    $questions = $_POST['questions']; 
    $creatorID = '1'; // Replace with actual creator ID
    $subjectCode = 'SUB123'; // Replace with actual subject code
    $assessmentType = 'Q'; 
    $timeLimit = '30'; // TODO: Add time limit input
    $noOfItems = count($questions); 
    $date = date('Y-m-d');

    $sqlUpdateAssessment = "UPDATE ASSESSMENT SET assessment_Name = ?, date = ?, creator_ID = ?, subject_Code = ?, assessment_Type = ?, time_Limit = ?, no_Of_Items = ? WHERE assessment_ID = ?";
    $stmt = $conn->prepare($sqlUpdateAssessment);
    $stmt->bind_param('ssssssss', $assessmentName, $date, $creatorID, $subjectCode, $assessmentType, $timeLimit, $noOfItems, $assessmentID);

    if ($stmt->execute()) {
        $updateQuestionSql = "UPDATE EXAMINATION_BANK SET question_No = ?, question = ?, points = ?, question_Type = ?, choice1 = ?, choice2 = ?, choice3 = ?, choice4 = ? WHERE assessment_ID = ? AND question_ID = ?";
        $stmtUpdateQuestion = $conn->prepare($updateQuestionSql);
        $stmtUpdateQuestion->bind_param('isisssssss', $questionNo, $questionText, $points, $questionType, $choice1, $choice2, $choice3, $choice4, $assessmentID, $questionID);

        $updateAnswerSql = "UPDATE EXAM_ANSWER SET answer = ? WHERE assessment_ID = ? AND question_ID = ?";
        $stmtUpdateAnswer = $conn->prepare($updateAnswerSql);
        $stmtUpdateAnswer->bind_param('sis', $correctAnswer, $assessmentID, $questionID);

        $insertQuestionSql = "INSERT INTO EXAMINATION_BANK (assessment_ID, question_ID, question_No, question, points, question_Type, choice1, choice2, choice3, choice4) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsertQuestion = $conn->prepare($insertQuestionSql);
        $stmtInsertQuestion->bind_param('siisssssss', $assessmentID, $questionID, $questionNo, $questionText, $points, $questionType, $choice1, $choice2, $choice3, $choice4);

        $insertAnswerSql = "INSERT INTO EXAM_ANSWER (assessment_ID, question_ID, answer) VALUES (?, ?, ?)";
        $stmtInsertAnswer = $conn->prepare($insertAnswerSql);
        $stmtInsertAnswer->bind_param('sis', $assessmentID, $questionID, $correctAnswer);

        foreach ($questions as $questionID => $question) {
            $questionNo = $questionID; 

            $questionText = $question['text'];
            $points = 1;
            $questionType = 'M'; 

            // Choices
            $choice1 = isset($question['options'][1]) ? $question['options'][1] : '';
            $choice2 = isset($question['options'][2]) ? $question['options'][2] : '';
            $choice3 = isset($question['options'][3]) ? $question['options'][3] : '';
            $choice4 = isset($question['options'][4]) ? $question['options'][4] : '';

          
            $correctAnswer = isset($question['answer']) ? $question['answer'] : '';

          
            $checkQuestionSql = "SELECT * FROM EXAMINATION_BANK WHERE assessment_ID = ? AND question_ID = ?";
            $stmtCheckQuestion = $conn->prepare($checkQuestionSql);
            $stmtCheckQuestion->bind_param('si', $assessmentID, $questionID);
            $stmtCheckQuestion->execute();
            $result = $stmtCheckQuestion->get_result();

            if ($result->num_rows > 0) {
                $stmtUpdateQuestion->execute();
                $stmtUpdateAnswer->execute();
            } else {
                $stmtInsertQuestion->execute();
                $stmtInsertAnswer->execute();
            }
        }

        echo "Assessment updated successfully";
    } else {
        echo "Error updating assessment: " . $stmt->error;
    }

    $stmt->close();
    $stmtUpdateQuestion->close();
    $stmtUpdateAnswer->close();
    $stmtInsertQuestion->close();
    $stmtInsertAnswer->close();
} else {
    echo "Assessment ID is required";
}


$conn->close();
?>
