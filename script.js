
document.addEventListener("DOMContentLoaded", () => {
    for (let i = 1; i <= 5; i++) {
        const subjectSelect = document.getElementById(`subject${i}`);
        if (subjectSelect) {
            subjectSelect.addEventListener("change", function () {
                if (this.value === "other") {
                    convertToTextInput(this);
                }
            });
        }
    }
});

function convertToTextInput(selectElement) {
    const textInput = document.createElement("input");
    textInput.type = "text";
    textInput.id = selectElement.id;    
    textInput.name = selectElement.name; 
    textInput.placeholder = "Enter subject Name";
    textInput.required = true;

    selectElement.parentNode.replaceChild(textInput, selectElement);
    
    textInput.focus();
}

function getGradeAndStatus(marks) {
    if (marks >= 90) return { grade: "A+", status: "Pass" };
    if (marks >= 80) return { grade: "A", status: "Pass" };
    if (marks >= 70) return { grade: "B", status: "Pass" };
    if (marks >= 60) return { grade: "C", status: "Pass" };
    if (marks >= 50) return { grade: "D", status: "Pass" };
    if (marks >= 40) return { grade: "E", status: "Pass" };
    return { grade: "F", status: "Fail" };
}

function calculateReport() {
    let totalMarks = 0;
    let enteredSubjects = 0;
    let hasFailedAny = false;

    for (let i = 1; i <= 5; i++) {
        const marksInput = document.getElementById(`marks${i}`);
        const gradeInput = document.getElementById(`grade${i}`);
        const pfInput = document.getElementById(`passfail${i}`);

        const rawValue = marksInput.value.trim();

        if (rawValue === "") {
            gradeInput.value = "";
            pfInput.value = "";
            continue;
        }

        let marks = parseFloat(rawValue);

        if (marks < 0) {
            marks = 0;
            marksInput.value = 0;
        } else if (marks > 100) {
            marks = 100;
            marksInput.value = 100;
        }

        const result = getGradeAndStatus(marks);
        gradeInput.value = result.grade;
        pfInput.value = result.status;

        totalMarks += marks;
        enteredSubjects++;

        if (result.status === "Fail") {
            hasFailedAny = true;
        }
    }

    const totalMarksEl = document.getElementById("totalMarks");
    const overallGradeEl = document.getElementById("overallGrade");
    const overallResultEl = document.getElementById("overallResult");

    if (enteredSubjects === 0) {
        totalMarksEl.value = "";
        overallGradeEl.value = "";
        overallResultEl.value = "";
        return;
    }

    totalMarksEl.value = `${totalMarks} / 500`;

    const overallPercentage = (totalMarks / 500) * 100;
    const overallSummary = getGradeAndStatus(overallPercentage);

    if (hasFailedAny) {
        overallGradeEl.value = "F";
        overallResultEl.value = "Fail";
    } else {
        overallGradeEl.value = overallSummary.grade;
        overallResultEl.value = "Pass";
    }
}

function saveAndNext() {
    const form = document.getElementById("reportForm");
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    form.submit();
}
