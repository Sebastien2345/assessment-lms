<?php
include "db.php";

$query = "SELECT user_ID, assessment_ID, attempt_Number, score, grade, subject_Code, date FROM user_exam_report";
$result = $conn->query($query);

$reportDetails = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['grade'] = number_format($row['grade'], 2);

        $reportDetails[] = $row;
    }
}

echo json_encode($reportDetails);

$conn->close();
?>
