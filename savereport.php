<?php
$conn = new mysqli("localhost", "root", "", "studentdetails");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Collect Student Personal Details (adjust field names if your HTML uses different name="" attributes)
    $studentName = $_POST['studentName'] ?? '';
    $studentId   = $_POST['studentId'] ?? '';
    $semester    = $_POST['semester'] ?? '';
    $year        = $_POST['year'] ?? '';

    // Insert into strudent_personal (matching the exact table name from your summary script)
    $stmt1 = $conn->prepare("INSERT INTO strudent_personal (Name, StudentID, year, semester) VALUES (?, ?, ?, ?)");
    $stmt1->bind_param("ssss", $studentName, $studentId, $year, $semester);

    if (!$stmt1->execute()) {
        die("Error saving student details: " . $stmt1->error);
    }
    $stmt1->close();

    // 2. Collect and Insert the 5 Subjects & Marks into student_marks
    $stmt2 = $conn->prepare("INSERT INTO student_marks (Enrollment, subjectname, marks) VALUES (?, ?, ?)");

    for ($i = 1; $i <= 5; $i++) {
        $subject = $_POST["subject$i"] ?? '';
        $marks   = $_POST["marks$i"] ?? '';

        if (!empty(trim($subject)) && $marks !== '') {
            $marksInt = (int)$marks;
            $stmt2->bind_param("ssi", $studentId, $subject, $marksInt);
            $stmt2->execute();
        }
    }

    $stmt2->close();
    $conn->close();

    // 3. Redirect to your Confirmation / Thank You page
    header("Location: thankyou.html"); // Change filename if your Thank You page has a different name
    exit();
}
?>
