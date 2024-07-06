document.addEventListener('DOMContentLoaded', () => {
    fetch('../LandingPage_Professor/includes/get_assessment_details_prof.php')
  .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
      .then(assessments => {
            populateAssessments(assessments);
        })
      .catch(error => console.error('Error:', error));
});

function populateAssessments(assessments) {
    const assessmentsContainer = document.getElementById('container_section_assessment');
    assessmentsContainer.innerHTML = ''; 
    
    assessments.forEach(assessment => {
        const assessmentCard = document.createElement('div');
        assessmentCard.className = 'card_topic';
        assessmentCard.innerHTML = `
            <div class="container_assessment">
                <div class="container_collapsed">
                    <button class="button_collapse hidden">Collapse</button>
                    <button class="button_expand ">Expand</button>
                    <h2>${assessment.assessment_name}</h2>
                </div>

                <div class="container_expanded hidden">
                    <div class="container_buttons">
                        <button>Export</button>
                        <button>Edit</button>
                        <button>Report</button>
                    </div>
                    <p>Opened: ${assessment.date_opened}</p>
                    <p>Due: ${assessment.date_closed}</p>
                    <div class="container_student">
                    </div>
                </div>
            </div>`
        ;
        
        assessmentsContainer.appendChild(assessmentCard);

        const containerStudent = assessmentCard.querySelector('.container_student');
        containerStudent.setAttribute('assessmentID', assessment.assessment_id);
        
        // Add event listeners for collapse and expand buttons
        const buttonCollapse = assessmentCard.querySelector('.button_collapse');
        const buttonExpand = assessmentCard.querySelector('.button_expand');
        const containerExpanded = assessmentCard.querySelector('.container_expanded');

        buttonCollapse.addEventListener('click', () => {
            buttonCollapse.classList.add('hidden');
            buttonExpand.classList.remove('hidden');
            containerExpanded.classList.add('hidden');
        });

        buttonExpand.addEventListener('click', () => {
            buttonExpand.classList.add('hidden');
            buttonCollapse.classList.remove('hidden');
            containerExpanded.classList.remove('hidden');
        });

        // Fetch and display students
        fetchAndDisplayStudents(assessment['assessment_id'], assessment['subject_Code']);
    });
}

function fetchAndDisplayStudents(assessmentID, subjectCode) {
    fetch(`../LandingPage_Professor/includes/assessment_prof_model.php?assessment_ID='${assessmentID}'&subject_Code='${subjectCode}'`)
      .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
      .then(students => {
            const studentContainers = document.querySelectorAll('.container_student');
            studentContainers.forEach(container => {
                if (container.getAttribute('assessmentid') === assessmentID) {
                    displayStudents(container, students, assessmentID);
                }
            });
        })
      .catch(error => {
            console.error('Error:', error);
        });
}

function displayStudents(container, students) {
    container.innerHTML = '';

    students.forEach(student => {
        const studentElement = document.createElement('div');
        studentElement.className = 'card_student';

        const h3Element = document.createElement('h3');
        h3Element.textContent = student.name;

        const reviewButton = document.createElement('button');
        reviewButton.className = 'review-button';

        // Set the button text based on the 'attempted' flag
        reviewButton.textContent = student.attempted ? 'Review Attempt' : 'Not Attempted';

        // Add click event listener to the review button
        reviewButton.addEventListener('click', () => {
            const assessmentID = container.getAttribute('assessmentID');


            if (student.attempted && assessmentID === student.assessmentID) {
                console.log(`Reviewing attempted assessment for student: ${student.name}, assessment ID: ${assessmentID}`);
            } 
            else {
                console.log(`Student: ${student.name} has not attempted assessment ID: ${assessmentID}`);
            }
        });

        studentElement.appendChild(h3Element);
        studentElement.appendChild(reviewButton);

        // Append the studentElement to the container
        container.appendChild(studentElement);
    });
}
