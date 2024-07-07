<?php
require "db.php";

// Retrieve assessment ID
$assessmentID = $_POST['assessmentID'];
if (empty($assessmentID)) {
    echo "Error: Assessment ID is missing";
    exit();
}

echo "Debug: assessmentID = $assessmentID";  // Debugging statement

$newQuestions = isset($_POST['newQuestions']) ? $_POST['newQuestions'] : [];

$assessmentName = $_POST['assessmentName'];
$date = date('Y-m-d');
$creatorID = '1'; // Replace with actual creator ID
$subjectCode = 'SUB123'; // Replace with actual subject code
$assessmentType = 'Q';
$timeLimit = '30';
$questions = isset($_POST['questions']) ? $_POST['questions'] : [];
$noOfItems = count($questions) + count($newQuestions);

// Update ASSESSMENT
$sqlUpdateAssessment = "UPDATE ASSESSMENT SET assessment_Name = ?, date = ?, creator_ID = ?, subject_Code = ?, assessment_Type = ?, time_Limit = ?, no_Of_Items = ? WHERE assessment_ID = ?";
$stmtUpdateAssessment = $conn->prepare($sqlUpdateAssessment);
$stmtUpdateAssessment->bind_param('ssssssss', $assessmentName, $date, $creatorID, $subjectCode, $assessmentType, $timeLimit, $noOfItems, $assessmentID);
$stmtUpdateAssessment->execute();

// Process each existing question
foreach ($questions as $questionID => $questionData) {
    if (is_numeric($questionID)) {
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
// Adding new questions
foreach ($newQuestions as $newQuestion) {
    $text = $newQuestion['text'];
    $type = $newQuestion['type'];
    $options = $newQuestion['options'] ?? [];
    $correctAnswer = $newQuestion['correctAnswer'] ?? '';

    // Prepare SQL to insert question details
    $query = "INSERT INTO EXAMINATION_BANK (assessment_ID, question, question_Type) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('sss', $assessmentID, $text, $type);
    $stmt->execute();
    $newQuestionID = $stmt->insert_id;

    if ($stmt->error) {
        echo "Error inserting question: " . $stmt->error;
        continue;  // Skip to next question on error
    }

    // Depending on question type, save the correct answer in the EXAM_ANSWER table
    switch ($type) {
        case 'M': // Multiple Choice
            $stmtUpdateQuestion = $conn->prepare("UPDATE EXAMINATION_BANK SET choice1 = ?, choice2 = ?, choice3 = ?, choice4 = ? WHERE assessment_ID = ? AND question_ID = ?");
            $stmtUpdateQuestion->bind_param('sssssi', $options[0], $options[1], $options[2], $options[3], $assessmentID, $newQuestionID);
            $stmtUpdateQuestion->execute();
            
            $query = "INSERT INTO EXAM_ANSWER (assessment_ID, question_ID, answer) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('sis', $assessmentID, $newQuestionID, $correctAnswer);
            $stmt->execute();
            break;
        case 'T': // True or False
            $stmtUpdateQuestion = $conn->prepare("UPDATE EXAMINATION_BANK SET boolean = ? WHERE assessment_ID = ? AND question_ID = ?");
            $stmtUpdateQuestion->bind_param('ssi', $correctAnswer, $assessmentID, $newQuestionID);
            $stmtUpdateQuestion->execute();
            
            $query = "INSERT INTO EXAM_ANSWER (assessment_ID, question_ID, answer) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('sis', $assessmentID, $newQuestionID, $correctAnswer);
            $stmt->execute();
            break;
        case 'S': // Short Answer
            $stmtUpdateQuestion = $conn->prepare("UPDATE EXAMINATION_BANK SET fill_Blank = ? WHERE assessment_ID = ? AND question_ID = ?");
            $stmtUpdateQuestion->bind_param('ssi', $correctAnswer, $assessmentID, $newQuestionID);
            $stmtUpdateQuestion->execute();
            
            $query = "INSERT INTO EXAM_ANSWER (assessment_ID, question_ID, answer) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('sis', $assessmentID, $newQuestionID, $correctAnswer);
            $stmt->execute();
            break;
        case 'F': // Match
            $matches = array_slice($options, 0, 10); // Ensure maximum of 10 matches
            $answers = array_slice($newQuestion['answers'], 0, 10); // Ensure maximum of 10 answers
            
            $stmtUpdateQuestion = $conn->prepare("UPDATE EXAMINATION_BANK SET match1 = ?, match2 = ?, match3 = ?, match4 = ?, match5 = ?, match6 = ?, match7 = ?, match8 = ?, match9 = ?, match10 = ? WHERE assessment_ID = ? AND question_ID = ?");
            $stmtUpdateQuestion->bind_param('sssssssssssi', $matches[0], $matches[1], $matches[2], $matches[3], $matches[4], $matches[5], $matches[6], $matches[7], $matches[8], $matches[9], $assessmentID, $newQuestionID);
            $stmtUpdateQuestion->execute();

            $query = "INSERT INTO EXAM_ANSWER (assessment_ID, question_ID, m_Ans1, m_Ans2, m_Ans3, m_Ans4, m_Ans5, m_Ans6, m_Ans7, m_Ans8, m_Ans9, m_Ans10) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param('isiiiiiiiiii', $assessmentID, $newQuestionID, $answers[0], $answers[1], $answers[2], $answers[3], $answers[4], $answers[5], $answers[6], $answers[7], $answers[8], $answers[9]);
            $stmt->execute();
            break;
        default:
            echo "Unsupported question type.";
            continue 2;
    }

    if ($stmt->error) {
        echo "Error inserting answer: " . $stmt->error;
    } else {
        echo "Answer added successfully.";
    }

    $stmt->close();
}

// Redirect or handle post-save operations
header("Location: edit_assessment.php?id=$assessmentID&success=1");
exit();
?>




