document.addEventListener('DOMContentLoaded', function() {
    fetch('fetch_report_details.php')
        .then(response => response.json())
        .then(data => {
            const reportDetails = document.getElementById('report-details');
            data.forEach(report => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${report.report_ID}</td>
                    <td>${report.user_ID}</td>
                    <td>${report.assessment_ID}</td>
                    <td>${report.grade}</td>
                    <td>${report.time_finished}</td>
                    <td>${report.attempts}</td>
                    `;
                reportDetails.appendChild(row);
            });
        })
        .catch(error => console.error('Error fetching report details:', error));
});
