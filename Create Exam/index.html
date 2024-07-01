<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assessment List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .assessment-item {
            margin-bottom: 10px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .assessment-item h3 {
            margin-top: 0;
        }
        button {
            padding: 5px 10px;
            font-size: 14px;
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <h1>Assessment List</h1>
    <div id="assessment-list">
        <!-- Existing assessments will be displayed here -->
    </div>
    <button onclick="window.location.href='create-assessment.html'">Create New Assessment</button>

    <script>
        async function fetchAssessments() {
            const response = await fetch('get_assessments.php');
            const assessments = await response.json();

            const assessmentListDiv = document.getElementById('assessment-list');
            assessments.forEach(assessment => {
                const assessmentDiv = document.createElement('div');
                assessmentDiv.className = 'assessment-item';
                assessmentDiv.innerHTML = `
                    <h3>${assessment.assessment_Name}</h3>
                    <button onclick="editAssessment('${assessment.assessment_ID}')">Edit</button>
                `;
                assessmentListDiv.appendChild(assessmentDiv);
            });
        }

        fetchAssessments();

        function editAssessment(assessmentID) {
            // Redirect to edit page passing assessmentID as parameter
            window.location.href = `edit_assessment.php?id=${assessmentID}`;
        }
    </script>
</body>
</html>





