async function fetchAssessmentDetails() {
    const urlParams = new URLSearchParams(window.location.search);
    const assessmentID = urlParams.get('assessmentID');

    const response = await fetch(`fetch_assessment_details.php?assessmentID=${assessmentID}`);
    const data = await response.json();

    document.getElementById('assessment-name').textContent = data.assessment_Name;

    const questionsContainer = document.getElementById('questions-container');
    questionsContainer.innerHTML = '';

    data.questions.forEach(question => {
        const questionDiv = document.createElement('div');
        questionDiv.classList.add('question');

        let questionHTML = `<p>${question.question_No}. ${question.question}</p>`;

        if (question.question_Type === 'M') {
            questionHTML += `
                <label><input type="radio" name="question-${question.question_ID}" value="${question.choice1}" required> ${question.choice1}</label><br>
                <label><input type="radio" name="question-${question.question_ID}" value="${question.choice2}" required> ${question.choice2}</label><br>
                <label><input type="radio" name="question-${question.question_ID}" value="${question.choice3}" required> ${question.choice3}</label><br>
                <label><input type="radio" name="question-${question.question_ID}" value="${question.choice4}" required> ${question.choice4}</label>
            `;
        } else if (question.question_Type === 'T') {
            questionHTML += `
                <label><input type="radio" name="question-${question.question_ID}" value="T" required> True</label><br>
                <label><input type="radio" name="question-${question.question_ID}" value="F" required> False</label>
            `;
        } else if (question.question_Type === 'S') {
            questionHTML += `<input type="text" name="question-${question.question_ID}" required>`;
        } else if (question.question_Type === 'F') {
            for (let i = 1; i <= 10; i++) {
                if (question[`match${i}`]) {
                    questionHTML += `
                        <label>Match ${i}: ${question[`match${i}`]}</label>
                        <input type="text" name="question-${question.question_ID}-match${i}" required><br>
                    `;
                }
            }
        }

        questionDiv.innerHTML = questionHTML;
        questionsContainer.appendChild(questionDiv);
    });

    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'assessmentID';
    hiddenInput.value = assessmentID;
    document.getElementById('assessment-form').appendChild(hiddenInput);

    const userIDInput = document.createElement('input');
    userIDInput.type = 'hidden';
    userIDInput.name = 'userID';
    userIDInput.value = 'USER123'; // Replace with actual userID
    document.getElementById('assessment-form').appendChild(userIDInput);
}

async function submitAssessment() {
    const formData = new FormData(document.getElementById('assessment-form'));

    const response = await fetch('submit_assessment.php', {
        method: 'POST',
        body: formData
    });

    const result = await response.text();
    alert(result);
}

document.addEventListener('DOMContentLoaded', () => {
    fetchAssessmentDetails();

    document.getElementById('assessment-form').addEventListener('submit', event => {
        event.preventDefault();
        submitAssessment();
    });
});
