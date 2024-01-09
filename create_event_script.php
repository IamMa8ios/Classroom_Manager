<?php
require_once "db_connector.php";

print_r($_POST);

$classID = $userID = $lectureID = $daysOfWeek = $startTime = $duration = $startDate = $endDate = "";

if(isset($_POST['classID'])){
    $classID = $_POST['classID'];
}else{
    echo "class id not set";
}

if(isset($_POST['startDate'])){
    $startDate = $_POST['startDate'];
}else{
    echo "start date not set";
}

if(isset($_POST['recurring'])){
    $repeatable = true;
}else{
    $repeatable = false;
}

if($repeatable && !isset($_POST['endDate'])){
    echo "end date not set";
}else{
    $endDate = $_POST['endDate'];
}

if(isset($_POST['lectureID'])){
    $lectureID = $_POST['lectureID'];
}else{
    echo "lecture id not set";
}

if(isset($_POST['startTime'])){
    $lectureID = $_POST['startTime'];
}else{
    echo "start time not set";
}

if(isset($_POST['startTime'])){
    $lectureID = $_POST['startTime'];
}else{
    echo "start time not set";
}

$conn = connect2db();
$stmt = $conn->prepare("insert into reservation() values(?, )");
$stmt->bind_param("i", $_SESSION['userID']);

if($stmt->execute()){

}else{

}
