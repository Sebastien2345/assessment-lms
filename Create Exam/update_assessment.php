<?php
require "db.php";

$assessmentID = $_POST['assessmentID'];

if (!empty($assessmentID)) {
    $assessmentName = $_POST['assessmentName'];
    $questions = $_POST['questions'];
    $newQuestions = $_POST['newQuestions']; // Add this line to retrieve new questions
    $creatorID = '1'; // Replace with actual creator ID
    $subjectCode = 'SUB123'; // Replace with actual subject code
    $assessmentType = 'Q';
    $timeLimit = '30'; // TODO: Add time limit input
    $date = date('Y-m-d');
    $noOfItems = count($questions) + count($newQuestions); // Adjust the count of items

    // Update the assessment details
    $sqlUpdateAssessment = "UPDATE ASSESSMENT SET assessment_Name = ?, date = ?, creator_ID = ?, subject_Code = ?, assessment_Type = ?, time_Limit = ?, no_Of_Items = ? WHERE assessment_ID = ?";
    $stmt = $conn->prepare($sqlUpdateAssessment);
    $stmt->bind_param('ssssssss', $assessmentName, $date, $creatorID, $subjectCode, $assessmentType, $timeLimit, $noOfItems, $assessmentID);

    if ($stmt->execute()) {
        // Prepare statements for updating and inserting questions
        $updateQuestionSql = "UPDATE EXAMINATION_BANK SET question_No = ?, question = ?, points = ?, question_Type = ?, choice1 = ?, choice2 = ?, choice3 = ?, choice4 = ?, boolean = ?, fill_Blank = ?, match1 = ?, match2 = ?, match3 = ?, match4 = ?, match5 = ?, match6 = ?, match7 = ?, match8 = ?, match9 = ?, match10 = ? WHERE assessment_ID = ? AND question_ID = ?";
        $stmtUpdateQuestion = $conn->prepare($updateQuestionSql);
        $stmtUpdateQuestion->bind_param('isisssssssssssssssssss', $questionNo, $questionText, $points, $questionType, $choice1, $choice2, $choice3, $choice4, $boolean, $fillBlank, $match1, $match2, $match3, $match4, $match5, $match6, $match7, $match8, $match9, $match10, $assessmentID, $questionID);

        $updateAnswerSql = "UPDATE EXAM_ANSWER SET answer = ? WHERE assessment_ID = ? AND question_ID = ?";
        $stmtUpdateAnswer = $conn->prepare($updateAnswerSql);
        $stmtUpdateAnswer->bind_param('sis', $correctAnswer, $assessmentID, $questionID);

        $insertQuestionSql = "INSERT INTO EXAMINATION_BANK (assessment_ID, question_No, question, points, question_Type, choice1, choice2, choice3, choice4, boolean, fill_Blank, match1, match2, match3, match4, match5, match6, match7, match8, match9, match10) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsertQuestion = $conn->prepare($insertQuestionSql);
        $stmtInsertQuestion->bind_param('siissssssssssssssssss', $assessmentID, $questionNo, $questionText, $points, $questionType, $choice1, $choice2, $choice3, $choice4, $boolean, $fillBlank, $match1, $match2, $match3, $match4, $match5, $match6, $match7, $match8, $match9, $match10);

        $insertAnswerSql = "INSERT INTO EXAM_ANSWER (assessment_ID, question_ID, answer) VALUES (?, ?, ?)";
        $stmtInsertAnswer = $conn->prepare($insertAnswerSql);
        $stmtInsertAnswer->bind_param('sis', $assessmentID, $questionID, $correctAnswer);

        // Update existing questions
        foreach ($questions as $questionID => $question) {
            $questionNo = $question['no'] ?? null;
            $questionText = $question['text'] ?? null;
            $points = $question['points'] ?? 1;
            $questionType = $question['type'] ?? null;

            $choice1 = $question['options'][0] ?? null;
            $choice2 = $question['options'][1] ?? null;
            $choice3 = $question['options'][2] ?? null;
            $choice4 = $question['options'][3] ?? null;
            $boolean = $question['boolean'] ?? null;
            $fillBlank = $question['fillBlank'] ?? null;
            $match1 = $question['match'][0] ?? null;
            $match2 = $question['match'][1] ?? null;
            $match3 = $question['match'][2] ?? null;
            $match4 = $question['match'][3] ?? null;
            $match5 = $question['match'][4] ?? null;
            $match6 = $question['match'][5] ?? null;
            $match7 = $question['match'][6] ?? null;
            $match8 = $question['match'][7] ?? null;
            $match9 = $question['match'][8] ?? null;
            $match10 = $question['match'][9] ?? null;

            $stmtUpdateQuestion->execute();
            $stmtUpdateAnswer->execute();
        }

        foreach ($questions as $question) {
            $questionID = isset($question['id']) ? $question['id'] : null;
            $questionNo = $question['no'] ?? null;
            $questionText = $question['text'] ?? null;
            $points = $question['points'] ?? 1;
            $questionType = $question['type'] ?? null;
        
            $choice1 = $question['options'][0] ?? null;
            $choice2 = $question['options'][1] ?? null;
            $choice3 = $question['options'][2] ?? null;
            $choice4 = $question['options'][3] ?? null;
            $boolean = $question['boolean'] ?? null;
            $fillBlank = $question['fillBlank'] ?? null;
            $match1 = $question['match'][0] ?? null;
            $match2 = $question['match'][1] ?? null;
            $match3 = $question['match'][2] ?? null;
            $match4 = $question['match'][3] ?? null;
            $match5 = $question['match'][4] ?? null;
            $match6 = $question['match'][5] ?? null;
            $match7 = $question['match'][6] ?? null;
            $match8 = $question['match'][7] ?? null;
            $match9 = $question['match'][8] ?? null;
            $match10 = $question['match'][9] ?? null;
        
            if ($questionID === null) {
                // Insert new question
                $stmtInsertQuestion->execute();
                $questionID = $conn->insert_id; // Get the new question ID
        
                // Insert answer for the new question
                $correctAnswer = $question['correctAnswer'] ?? null;
                if ($correctAnswer !== null) {
                    $stmtInsertAnswer->execute();
                }
            } else {
                // Update existing question
                $stmtUpdateQuestion->execute();
                $stmtUpdateAnswer->execute();
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
