<?php
include "db.php";

$query = "SELECT report_ID, user_ID, assessment_ID, grade, time_finished, attempts FROM student_reports";
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
