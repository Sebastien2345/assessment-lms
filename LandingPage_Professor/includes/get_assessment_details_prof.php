<?php
    require_once '../includes/config_session_inc.php';
    require_once '../includes/dbh_inc.php';
    require_once '../includes/execute_query_inc.php';

    // fetch assessments
    $query_assessment = "SELECT assessment_id, subject_ID, assessment_name, date_opened, date_closed FROM assessment";
    $result = executeQuery($mysqli, $query_assessment);

    $assessments = array();

    while ($row = $result['result']->fetch_assoc()) {
        // Format date_opened
        $date_opened = date('g:iA F j, Y', strtotime($row['date_opened']));
        
        // Format date_closed
        $date_closed = date('g:iA F j, Y', strtotime($row['date_closed']));
    
        // Replace the original date_opened and date_closed with formatted dates
        $row['date_opened'] = $date_opened;
        $row['date_closed'] = $date_closed;
    
        // Add row to assessments array
        $assessments[] = $row;
    }
    echo json_encode($assessments);
?>