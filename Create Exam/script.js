const days = Array.from({ length: 31 }, (_, i) => i + 1);
const months = [
  "January",
  "February",
  "March",
  "April",
  "May",
  "June",
  "July",
  "August",
  "September",
  "October",
  "November",
  "December",
];
const years = Array.from(
  { length: 101 },
  (_, i) => new Date().getFullYear() - 50 + i
);
const hours = Array.from({ length: 24 }, (_, i) =>
  i.toString().padStart(2, "0")
);
const minutes = Array.from({ length: 60 }, (_, i) =>
  i.toString().padStart(2, "0")
);
const time_limit_type = ["weeks", "days", "hours", "minutes", "seconds"];
const time_limit_expire = [
  "Open attempts are submitted automatically",
  "Attempts must be submitted before time expires, or they are not counted",
];
//   Might not be constant but updated if a teacher adds new grading category
const grade_category = ["Uncategorized"];
const grade_attempts = Array.from({ length: 10 }, (_, i) => i + 1);
const grading_method = ["Highest grade", "Average grade", "First attempt", "Last attempt"]

function populateSelect(selectId, values, defaultValue) {
  const select = document.getElementById(selectId);
  values.forEach((value) => {
    const option = document.createElement("option");
    option.value = value;
    option.textContent = value;
    if (value === defaultValue) {
      option.selected = true;
    }
    select.appendChild(option);
  });
}

document.addEventListener("DOMContentLoaded", () => {
  const currentDate = new Date();

  populateSelect("open_quiz_day", days, currentDate.getDate());
  populateSelect(
    "open_quiz_month",
    months,
    months[currentDate.getMonth()]
  );
  populateSelect("open_quiz_year", years, currentDate.getFullYear());
  populateSelect(
    "open_quiz_hour",
    hours,
    currentDate.getHours().toString().padStart(2, "0")
  );
  populateSelect(
    "open_quiz_minute",
    minutes,
    currentDate.getMinutes().toString().padStart(2, "0")
  );

  populateSelect("close_quiz_day", days, currentDate.getDate());
  populateSelect(
    "close_quiz_month",
    months,
    months[currentDate.getMonth()]
  );
  populateSelect("close_quiz_year", years, currentDate.getFullYear());
  populateSelect(
    "close_quiz_hour",
    hours,
    currentDate.getHours().toString().padStart(2, "0")
  );
  populateSelect(
    "close_quiz_minute",
    minutes,
    currentDate.getMinutes().toString().padStart(2, "0")
  );
  populateSelect("time_limit_type", time_limit_type);
  populateSelect("time_limit_expire", time_limit_expire);
  populateSelect("grade_category", grade_category);
  populateSelect("grade_attempts", grade_attempts);
  populateSelect("grading_method", grading_method);
});