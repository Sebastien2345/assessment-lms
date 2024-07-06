<?php
$servername = "localhost";
$username = "root";
$password = " ";
$dbname = "pup_lms";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$assessmentID = $_POST['assessmentID'];
$userID = $_POST['userID']; // Assuming userID is passed in the form

foreach ($_POST as $key => $value) {
    if (strpos($key, 'question-') !== false) {
        $questionID = explode('-', $key)[1];
        $answer = $value;

        $stmt = $conn->prepare("INSERT INTO exam_answer (assessment_ID, question_ID, answer) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE answer = VALUES(answer)");
        $stmt->bind_param("sis", $assessmentID, $questionID, $answer);
        $stmt->execute();
    }
}

$stmt->close();
$conn->close();

echo "Assessment submitted successfully.";
?>
