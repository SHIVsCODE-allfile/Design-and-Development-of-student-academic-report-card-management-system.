<?php
header('Content-Type: application/json');
mysqli_report(MYSQLI_REPORT_OFF);

$conn = @new mysqli("localhost", "root", "", "studentdetails");
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed"]);
    exit();
}

function gradeFor($p) {
    if ($p >= 90) return "A+";
    if ($p >= 80) return "A";
    if ($p >= 70) return "B";
    if ($p >= 60) return "C";
    if ($p >= 50) return "D";
    if ($p >= 40) return "E";
    return "F";
}

// 1. Students (same table name as used in savereport.php)
$students = [];
$res = $conn->query("SELECT Name, StudentID, year, semester FROM strudent_personal");
if (!$res) {
    echo json_encode(["error" => "Could not read student table"]);
    exit();
}
while ($row = $res->fetch_assoc()) {
    $students[$row['StudentID']] = [
        "name"     => $row['Name'],
        "id"       => $row['StudentID'],
        "year"     => $row['year'],
        "semester" => $row['semester'],
        "subjects" => []
    ];
}

// 2. Marks (skip the "Total" row, we recompute it)
$res2 = $conn->query("SELECT Enrollment, subjectname, marks FROM student_marks");
if ($res2) {
    while ($row = $res2->fetch_assoc()) {
        if ($row['subjectname'] === "Total") continue;
        if (isset($students[$row['Enrollment']])) {
            $students[$row['Enrollment']]['subjects'][$row['subjectname']] = (int)$row['marks'];
        }
    }
}

// 3. Build summary
$list = [];
$passed = 0;
$failed = 0;
$percentSum = 0;

foreach ($students as $s) {
    $count = count($s['subjects']);
    if ($count === 0) continue;

    $total   = array_sum($s['subjects']);
    $percent = $total / ($count * 100) * 100;
    $hasFail = min($s['subjects']) < 40;
    $result  = $hasFail ? "Fail" : "Pass";

    if ($hasFail) $failed++; else $passed++;
    $percentSum += $percent;

    $list[] = [
        "name"     => $s['name'],
        "id"       => $s['id'],
        "year"     => $s['year'],
        "semester" => $s['semester'],
        "total"    => $total . " / " . ($count * 100),
        "percent"  => round($percent, 1),
        "grade"    => $hasFail ? "F" : gradeFor($percent),
        "result"   => $result
    ];
}

$n = count($list);

echo json_encode([
    "summary" => [
        "students" => $n,
        "passed"   => $passed,
        "failed"   => $failed,
        "average"  => $n ? round($percentSum / $n, 1) : 0
    ],
    // newest first, last 10
    "recent" => array_slice(array_reverse($list), 0, 10)
]);

$conn->close();
