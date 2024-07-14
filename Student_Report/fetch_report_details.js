document.addEventListener('DOMContentLoaded', function() {
    fetch('fetch_report_details.php')
        .then(response => response.json())
        .then(data => {
            const reportDetails = document.getElementById('report-details');
            data.forEach(report => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${report.user_ID}</td>
                    <td>${report.assessment_ID}</td>
                    <td>${report.attempt_Number}</td>
                    <td>${report.score}</td>
                    <td>${report.grade}</td>
                    <td>${report.subject_Code}</td>
                    <td>${report.date}</td>
                    `;
                reportDetails.appendChild(row);
            });
        })
        .catch(error => console.error('Error fetching report details:', error));
});
