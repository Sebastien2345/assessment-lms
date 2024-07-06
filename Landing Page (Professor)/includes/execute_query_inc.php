<?php
function executeQuery($mysqli, $query, $types = "", $params = [])
{
    $stmt = $mysqli->prepare($query);
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }

    // execute the statement
    $success = $stmt->execute();
    $result = $stmt->get_result();

    $error = $stmt->error;

    return ['success' => $success, 'result' => $result, 'error' => $error];
}