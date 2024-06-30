<?php
$servername = "localhost";
$username = "root";
$password = "Littleprince212003*";
$dbname = "pup_lms";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define variables to store form data
$subject_code = $creator_id = $opened_date = $closing_date = $allowed_attempts = $assessment_type = $num_items = $assessment_id = "";

// Process form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $subject_code = $_POST['subject_Code'];
    $creator_id = $_POST['creator_ID'];
    $opened_date = $_POST['date_created'];
    $closing_date = $_POST['closing_date'];
    $allowed_attempts = $_POST['allowed_attempts'];
    $assessment_type = $_POST['assessment_Type'];
    $num_items = $_POST['no_Of_Items'];
    $assessment_id = $_POST['assessment_ID'];

    // Insert data into database
    $sql = "INSERT INTO assessments (course_name, creator_id, opened_date, closing_date, allowed_attempts, assessment_type, num_items, assessment_id)
            VALUES ('$subject_code', '$creator_id', '$opened_date', '$closing_date', '$allowed_attempts', '$assessment_type', '$num_items', '$assessment_id')";

    if ($conn->query($sql) === TRUE) {
        echo "New record created successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment View</title>
</head>
<body>
    <div class="assessment-section">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <h3>Course Name</h3>
            <input type="text" name="subject_Code" placeholder="Subject Code" value="<?php echo htmlspecialchars($subject_code); ?>" required>

            <p>Creator ID:</p>
            <input type="text" name="creator_ID" placeholder="Creator ID" value="<?php echo htmlspecialchars($creator_id); ?>" required>

            <div class="time-section">
                <p class="text-label" style="font-weight: bold;">Date Created:</p>
                <input type="date" name="date_created" value="<?php echo htmlspecialchars($opened_date); ?>" required>

                <p class="text-label" style="font-weight: bold;">Will close on:</p>
                <input type="date" name="closing_date" value="<?php echo htmlspecialchars($closing_date); ?>" required>
            </div>

            <p class="text-label" style="font-weight: bold;">Allowed Attempts:</p>
            <input type="number" name="allowed_attempts" value="<?php echo htmlspecialchars($allowed_attempts); ?>" required>

            <p class="text-label" style="font-weight: bold;">Assessment Type:</p>
            <input type="text" name="assessment_Type" placeholder="Assessment Type" value="<?php echo htmlspecialchars($assessment_type); ?>" required>

            <p class="text-label" style="font-weight: bold;">No. of Items:</p>
            <input type="number" name="no_Of_Items" value="<?php echo htmlspecialchars($num_items); ?>" required>

            <p class="text-label" style="font-weight: bold;">Assessment ID:</p>
            <input type="text" name="assessment_ID" placeholder="Assessment ID" value="<?php echo htmlspecialchars($assessment_id); ?>" required>

            <hr/>

            <button type="submit">Start</button>
            <button type="button" onclick="window.location.href='back_to_course.html';">Back to Course</button>
        </form>
    </div>
</body>
</html>
