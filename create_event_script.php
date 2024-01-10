<?php

if (!isset($_POST)) {
    header("Location: index-EN.php");
}

if(!isset($_SESSION)){
    session_start();
}

require_once "db_connector.php";

function sanitize($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

$classID = $userID = $lectureID = $dayOfWeek = $startTime = $duration = $startDate = $endDate = "";
$params = array();

if (isset($_SESSION['userID'])) {
    $userID = sanitize($_SESSION['userID']);
} else {
    echo "user id not set";
}

if (isset($_POST['classID'])) {
    $classID = sanitize($_POST['classID']);
} else {
    echo "class id not set";
}

if (isset($_POST['lectureID'])) {
    $lectureID = sanitize($_POST['lectureID']);
} else {
    echo "lecture id not set";
}

if (isset($_POST['startDate'])) {
    $startDate = sanitize($_POST['startDate']);
} else {
    echo "start date not set";
}

if (isset($_POST['startTime'])) {
    $startTime = sanitize($_POST['startTime']);
} else {
    echo "start time not set";
}

if (isset($_POST['duration'])) {
    $duration = sanitize($_POST['duration']);
} else {
    echo "start time not set";
}

$conn = connect2db();

if (isset($_POST['recurring'])) {
    $repeatable = true;

    if (isset($_POST['endDate'])) {
        $dayOfWeek = "[" . date('w', strtotime(sanitize($_POST['startDate']))) . "]";
        $endDate=sanitize($_POST['endDate']);

        $stmt = $conn->prepare("insert into reservation(userID, classroomID, lectureID, repeatable, start_date, end_date, day_of_week, start_time, duration) values(?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iiiissssi", $userID, $classID, $lectureID, $repeatable, $startDate, $endDate, $dayOfWeek, $startTime, $duration);

    } else {
        echo "end date not set";
    }

} else {
    $repeatable = false;
    $stmt = $conn->prepare("insert into reservation(userID, classroomID, lectureID, start_date, start_time, duration) values(?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiissi", $userID, $classID, $lectureID, $startDate, $startTime, $duration);
}

if ($stmt->execute()) {
    echo "booking successful";
    header("Location: index-EN.php");
} else {
    echo "Error: " . $stmt->error;
}
