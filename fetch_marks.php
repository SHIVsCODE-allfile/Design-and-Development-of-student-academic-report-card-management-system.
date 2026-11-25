<?php
header('Content-Type: application/json');

// 1. Database Connection
$host = "localhost";
$username = "root";
$password = "";
$dbname = "studentdetails";

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]);
    exit;
}

// 2. Fetch Marks Logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['roll_no'])) {
    $roll_no = trim($_POST['roll_no']);

    $stmt = $conn->prepare("SELECT studentname, Enrollment, subjectname, marks, creates_at FROM student_marks WHERE Enrollment = ?");
    $stmt->bind_param("s", $roll_no);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student_name = "";
        $enrollment_no = "";
        $last_updated = "";
        $subjects = [];
        $total_marks = 0;
        $max_total = 0;

        while ($row = $result->fetch_assoc()) {
            $student_name = ucwords($row['studentname']);
            $enrollment_no = $row['Enrollment'];
            $last_updated = $row['creates_at'];
            $mark = (int)$row['marks'];

            // Calculate Grade
            if ($mark >= 90) $grade = 'A+';
            elseif ($mark >= 80) $grade = 'A';
            elseif ($mark >= 70) $grade = 'B';
            elseif ($mark >= 60) $grade = 'C';
            elseif ($mark >= 33) $grade = 'D';
            else $grade = 'F';

            $subjects[] = [
                'subject' => $row['subjectname'],
                'marks' => $mark,
                'grade' => $grade
            ];

            $total_marks += $mark;
            $max_total += 100;
        }

        $percentage = ($max_total > 0) ? ($total_marks / $max_total) * 100 : 0;
        $result_status = ($percentage >= 33) ? 'PASSED' : 'FAILED';

        echo json_encode([
            'status' => 'success',
            'student_name' => $student_name,
            'enrollment_no' => $enrollment_no,
            'last_updated' => $last_updated,
            'subjects' => $subjects,
            'total_marks' => $total_marks,
            'max_total' => $max_total,
            'percentage' => number_format($percentage, 2),
            'result_status' => $result_status
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'No marksheet found for Enrollment Number: ' . htmlspecialchars($roll_no)
        ]);
    }
    $stmt->close();
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Please enter a valid Enrollment Number.'
    ]);
}

$conn->close();
?>
