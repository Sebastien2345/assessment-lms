<?php
    require_once '../includes/config_session_inc.php';
    require_once '../includes/dbh_inc.php';
    require_once '../includes/execute_query_inc.php';

    // fetch assessments
    $query_assessment = "SELECT assessment_id, subject_Code, assessment_name, date_opened, date_closed FROM assessment";
    $result = executeQuery($mysqli, $query_assessment);

    $assessments = array();

    while($row = $result['result'] ->fetch_assoc()) {
        $assessments[] = $row;
    }
    echo json_encode($assessments);
?>