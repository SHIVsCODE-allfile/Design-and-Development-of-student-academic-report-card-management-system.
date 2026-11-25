<?php

session_start();

$conn = new PDO("mysql:host=localhost;db_name = users", "root", "");

if (isset($_POST['user'])) {
    $username = $_POST['user'];
    $email = $_POST['email'];
    $password = $_POST['pass'];
    $type = $_POST['user_type'];

    $query = $conn->prepare("SELECT * FROM user WHERE username = ? AND email = ? AND password = ? AND user_type = ?");
    $query->execute([$username, $email, $password,$type]);
    $row = $query->fetch();

    if ($row) {
        if ($password == $row['password']) {
            $_SESSION['username'] = $row['username'];

            if ($row['user_type'] == "teacher") {
                header("Location: teacherlogin.html");
            } else {
                header("Location: student.html");
            }
        } else {
            echo "Wrong password!";
        }
    } else {
        echo "User not found!  please login first then we can fetch your details and we careful we are case sensitive. ";
    }
}
?>