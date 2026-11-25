<?php
$conn = new mysqli("localhost","root","","users");

if ($conn->connect_error)
{
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") 
{

    $user = $_POST['user'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $user_type = $_POST['user_type'];


    $stmt =  $conn->prepare( "INSERT INTO user ( username, email, password,user_type) VALUES (?, ?, ?,?)" );
    $stmt->bind_param("ssss", $user, $email, $pass, $user_type);

    if ($stmt->execute())
         {
        header("Location: teacherlogin.html");
        exit();
    } else 
    {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
