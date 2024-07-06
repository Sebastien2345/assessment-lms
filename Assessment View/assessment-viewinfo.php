<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment View</title>
    <link rel="stylesheet" href="style.css">

</head>
<body>
    <div class="assessment-section">
        <h2>Assessment Data</h2>

        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "pup_lms";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);

        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }


        $subject_Code = "COMP 20054"; 
        $assessment_ID= "A1116"; 

       
        $sql_select = "SELECT * FROM assessment WHERE subject_Code = '$subject_Code' AND assessment_ID = '$assessment_ID'";
        $result = $conn->query($sql_select);

        // Check if any rows were returned
        if ($result->num_rows > 0) {
            echo "<div class='assessment-list'>";
            while ($row = $result->fetch_assoc()) {
                echo "<p class='assessment_ID'>Assessment ID: " . $row["assessment_ID"] . "</p>";
                echo "<div class='assessment-item'>";
                echo "<h3>Subject Code: " . $row["subject_Code"] . "</h3>";
                echo "<p>Creator ID: " . $row["creator_ID"] . "</p>";
                echo "<p>Date Created: " . $row["date_created"] . "</p>";
                echo "<p>Closing Date: " . $row["closing_date"] . "</p>";
                echo "<hr/>";
                echo "<p>Allowed Attempts: " . $row["allowed_attempts"] . "</p>";
                echo "<p>Assessment Type: " . $row["assessment_Type"] . "</p>";
                echo "<p>No. of Items: " . $row["no_Of_Items"] . "</p>";
                echo "</div>";

                // Check if the exam is still open
                $current_date = date("Y-m-d H:i:s");
                $closing_date = $row["closing_date"];

                if ($current_date <= $row["closing_date"]) {
                    echo "<button>Start</button>";
                } else {
                    echo "<p>The assessment is now closed. Contact your professor to reopen the exam.</p>";
                }
                
            }
            echo "</div>";
        } else {
            echo "No records found for Subject Code: $subject_Code and Assessment ID: $assessment_ID";
        }
                

        
        $conn->close();
        ?>

    </div>
</body>
</html>