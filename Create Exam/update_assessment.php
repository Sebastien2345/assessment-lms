<?php
require "db.php";

// Retrieve assessment ID
$assessmentID = $_POST['assessmentID'];

if (!empty($assessmentID)) {
    $questions = $_POST['questions'];
    $assessmentName = $_POST['assessmentName'];
    $date = date('Y-m-d');
    $creatorID = '1'; // Replace with actual creator ID
    $subjectCode = 'SUB123'; // Replace with actual subject code
    $assessmentType = 'Q';
    $timeLimit = '30';
    $noOfItems = count($questions);

    // Update ASSESSMENT
    $sqlUpdateAssessment = "UPDATE ASSESSMENT SET assessment_Name = ?, date = ?, creator_ID = ?, subject_Code = ?, assessment_Type = ?, time_Limit = ?, no_Of_Items = ? WHERE assessment_ID = ?";
    $stmtUpdateAssessment = $conn->prepare($sqlUpdateAssessment);
    $stmtUpdateAssessment->bind_param('ssssssss', $assessmentName, $date, $creatorID, $subjectCode, $assessmentType, $timeLimit, $noOfItems, $assessmentID);
    $stmtUpdateAssessment->execute();

    // Process each question
    foreach ($questions as $questionID => $questionData) {
        if (is_numeric($questionID)) {
            // Existing question, update it
            $text = $questionData['text'];
            $type = $questionData['type'];

            // Update EXAMINATION_BANK table
            switch ($type) {
                case 'M': // Multiple Choice
                    $option1 = $questionData['options'][0];
                    $option2 = $questionData['options'][1];
                    $option3 = $questionData['options'][2];
                    $option4 = $questionData['options'][3];
                    $correctAnswer = $questionData['correctAnswer'];

                    $sqlUpdateQuestion = "UPDATE EXAMINATION_BANK SET question = ?, question_Type = ?, choice1 = ?, choice2 = ?, choice3 = ?, choice4 = ? WHERE assessment_ID = ? AND question_ID = ?";
                    $stmtUpdateQuestion = $conn->prepare($sqlUpdateQuestion);
                    $stmtUpdateQuestion->bind_param('sssssssi', $text, $type, $option1, $option2, $option3, $option4, $assessmentID, $questionID);
                    $stmtUpdateQuestion->execute();

                    // Update EXAM_ANSWER table for Multiple Choice
                    $sqlUpdateAnswer = "UPDATE EXAM_ANSWER SET answer = ? WHERE assessment_ID = ? AND question_ID = ?";
                    $stmtUpdateAnswer = $conn->prepare($sqlUpdateAnswer);
                    $stmtUpdateAnswer->bind_param('sii', $correctAnswer, $assessmentID, $questionID);
                    $stmtUpdateAnswer->execute();
                    break;

                case 'T': // True or False
                    $boolean = $questionData['boolean'];

                    $sqlUpdateQuestion = "UPDATE EXAMINATION_BANK SET question = ?, question_Type = ?, boolean = ? WHERE assessment_ID = ? AND question_ID = ?";
                    $stmtUpdateQuestion = $conn->prepare($sqlUpdateQuestion);
                    $stmtUpdateQuestion->bind_param('ssssi', $text, $type, $boolean, $assessmentID, $questionID);
                    $stmtUpdateQuestion->execute();

                    // Update EXAM_ANSWER table for True or False
                    $sqlUpdateAnswer = "UPDATE EXAM_ANSWER SET answer = ? WHERE assessment_ID = ? AND question_ID = ?";
                    $stmtUpdateAnswer = $conn->prepare($sqlUpdateAnswer);
                    $stmtUpdateAnswer->bind_param('sii', $boolean, $assessmentID, $questionID);
                    $stmtUpdateAnswer->execute();
                    break;

                case 'S': // Short Answer
                    $fillBlank = $questionData['fillBlank'];

                    $sqlUpdateQuestion = "UPDATE EXAMINATION_BANK SET question = ?, question_Type = ?, fill_Blank = ? WHERE assessment_ID = ? AND question_ID = ?";
                    $stmtUpdateQuestion = $conn->prepare($sqlUpdateQuestion);
                    $stmtUpdateQuestion->bind_param('ssssi', $text, $type, $fillBlank, $assessmentID, $questionID);
                    $stmtUpdateQuestion->execute();

                    // Update EXAM_ANSWER table for Short Answer
                    $sqlUpdateAnswer = "UPDATE EXAM_ANSWER SET answer = ? WHERE assessment_ID = ? AND question_ID = ?";
                    $stmtUpdateAnswer = $conn->prepare($sqlUpdateAnswer);
                    $stmtUpdateAnswer->bind_param('sii', $fillBlank, $assessmentID, $questionID);
                    $stmtUpdateAnswer->execute();
                    break;

                case 'F': // Match
                    $match1 = $questionData['match'][0];
                    $match2 = $questionData['match'][1];
                    $match3 = $questionData['match'][2];
                    $match4 = $questionData['match'][3];
                    $m_Ans1 = $questionData['m_Ans1'];
                    $m_Ans2 = $questionData['m_Ans2'];
                    $m_Ans3 = $questionData['m_Ans3'];
                    $m_Ans4 = $questionData['m_Ans4'];

                    $sqlUpdateQuestion = "UPDATE EXAMINATION_BANK SET question = ?, question_Type = ?, match1 = ?, match2 = ?, match3 = ?, match4 = ? WHERE assessment_ID = ? AND question_ID = ?";
                    $stmtUpdateQuestion = $conn->prepare($sqlUpdateQuestion);
                    $stmtUpdateQuestion->bind_param('ssssssi', $text, $type, $match1, $match2, $match3, $match4, $assessmentID, $questionID);
                    $stmtUpdateQuestion->execute();

                    // Update EXAM_ANSWER table for Match
                    $sqlUpdateAnswer = "UPDATE EXAM_ANSWER SET answer = ? WHERE assessment_ID = ? AND question_ID = ?";
                    $stmtUpdateAnswer = $conn->prepare($sqlUpdateAnswer);
                    $stmtUpdateAnswer->bind_param('sii', json_encode([$m_Ans1, $m_Ans2, $m_Ans3, $m_Ans4]), $assessmentID, $questionID);
                    $stmtUpdateAnswer->execute();
                    break;

                default:
                    continue 2; // Skip unknown question types
            }

            $stmtUpdateQuestion->close();
            $stmtUpdateAnswer->close();
        }
    }

    // Insert new questions
    $newQuestions = $_POST['newQuestions'];
    foreach ($newQuestions as $newQuestionData) {
        $text = $newQuestionData['text'];
        $type = $newQuestionData['type'];

        switch ($type) {
            case 'M': // Multiple Choice
                $option1 = $newQuestionData['options'][0];
                $option2 = $newQuestionData['options'][1];
                $option3 = $newQuestionData['options'][2];
                $option4 = $newQuestionData['options'][3];
                $correctAnswer = isset($newQuestionData['correctAnswer']) ? $newQuestionData['correctAnswer'] : null;

                $sqlInsertQuestion = "INSERT INTO EXAMINATION_BANK (assessment_ID, question, question_Type, choice1, choice2, choice3, choice4) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmtInsertQuestion = $conn->prepare($sqlInsertQuestion);
                $stmtInsertQuestion->bind_param('issssss', $assessmentID, $text, $type, $option1, $option2, $option3, $option4);
                $stmtInsertQuestion->execute();

                $newQuestionID = $stmtInsertQuestion->insert_id;

                // Insert into EXAM_ANSWER table for Multiple Choice
                $sqlInsertAnswer = "INSERT INTO EXAM_ANSWER (assessment_ID, question_ID, answer) VALUES (?, ?, ?)";
                $stmtInsertAnswer = $conn->prepare($sqlInsertAnswer);
                $stmtInsertAnswer->bind_param('iis', $assessmentID, $newQuestionID, $correctAnswer);
                $stmtInsertAnswer->execute();
                break;

            case 'T': // True or False
                $boolean = isset($newQuestionData['boolean']) ? $newQuestionData['boolean'] : null;

                $sqlInsertQuestion = "INSERT INTO EXAMINATION_BANK (assessment_ID, question, question_Type, boolean) VALUES (?, ?, ?, ?)";
                $stmtInsertQuestion = $conn->prepare($sqlInsertQuestion);
                $stmtInsertQuestion->bind_param('isss', $assessmentID, $text, $type, $boolean);
                $stmtInsertQuestion->execute();

                $newQuestionID = $stmtInsertQuestion->insert_id;

                // Insert into EXAM_ANSWER table for True or False
                $sqlInsertAnswer = "INSERT INTO EXAM_ANSWER (assessment_ID, question_ID, answer) VALUES (?, ?, ?)";
                $stmtInsertAnswer = $conn->prepare($sqlInsertAnswer);
                $stmtInsertAnswer->bind_param('iis', $assessmentID, $newQuestionID, $boolean);
                $stmtInsertAnswer->execute();
                break;

            case 'S': // Short Answer
                $fillBlank = isset($newQuestionData['fillBlank']) ? $newQuestionData['fillBlank'] : null;

                $sqlInsertQuestion = "INSERT INTO EXAMINATION_BANK (assessment_ID, question, question_Type, fill_Blank) VALUES (?, ?, ?, ?)";
                $stmtInsertQuestion = $conn->prepare($sqlInsertQuestion);
                $stmtInsertQuestion->bind_param('isss', $assessmentID, $text, $type, $fillBlank);
                $stmtInsertQuestion->execute();

                $newQuestionID = $stmtInsertQuestion->insert_id;

                // Insert into EXAM_ANSWER table for Short Answer
                $sqlInsertAnswer = "INSERT INTO EXAM_ANSWER (assessment_ID, question_ID, answer) VALUES (?, ?, ?)";
                $stmtInsertAnswer = $conn->prepare($sqlInsertAnswer);
                $stmtInsertAnswer->bind_param('iis', $assessmentID, $newQuestionID, $fillBlank);
                $stmtInsertAnswer->execute();
                break;

            case 'F': // Match
                $match1 = $newQuestionData['match'][0];
                $match2 = $newQuestionData['match'][1];
                $match3 = $newQuestionData['match'][2];
                $match4 = $newQuestionData['match'][3];
                $m_Ans1 = isset($newQuestionData['m_Ans1']) ? $newQuestionData['m_Ans1'] : null;
                $m_Ans2 = isset($newQuestionData['m_Ans2']) ? $newQuestionData['m_Ans2'] : null;
                $m_Ans3 = isset($newQuestionData['m_Ans3']) ? $newQuestionData['m_Ans3'] : null;
                $m_Ans4 = isset($newQuestionData['m_Ans4']) ? $newQuestionData['m_Ans4'] : null;

                $sqlInsertQuestion = "INSERT INTO EXAMINATION_BANK (assessment_ID, question, question_Type, match1, match2, match3, match4) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmtInsertQuestion = $conn->prepare($sqlInsertQuestion);
                $stmtInsertQuestion->bind_param('issssss', $assessmentID, $text, $type, $match1, $match2, $match3, $match4);
                $stmtInsertQuestion->execute();

                $newQuestionID = $stmtInsertQuestion->insert_id;

                // Insert into EXAM_ANSWER table for Match
                $sqlInsertAnswer = "INSERT INTO EXAM_ANSWER (assessment_ID, question_ID, answer) VALUES (?, ?, ?)";
                $stmtInsertAnswer = $conn->prepare($sqlInsertAnswer);
                $stmtInsertAnswer->bind_param('iis', $assessmentID, $newQuestionID, json_encode([$m_Ans1, $m_Ans2, $m_Ans3, $m_Ans4]));
                $stmtInsertAnswer->execute();
                break;

            default:
                continue 2; // Skip unknown question types
        }

        $stmtInsertQuestion->close();
        $stmtInsertAnswer->close();
    }

    // Close update statement for ASSESSMENT
    $stmtUpdateAssessment->close();

    // Close connection
    $conn->close();

    // Redirect or provide success message
    header("Location: edit_assessment.php?id={$assessmentID}&success=1");
    exit();
} else {
    echo "Assessment ID is required";
}
?>

