<?php

$servername = "localhost";
$username = "guest_user";
$password = "123456";

// Create connection
$conn = new mysqli($servername, $username, $password);
$conn->select_db("lecture_hall_db");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully";

$sql = "select * from reservation";
$res = $conn->query($sql);

$reservations = array();

while ($row = $res->fetch_assoc()) {

    if ($row['repeatable'] == 1) {
        $begin = new DateTime($row["start_date"]);

        $end = new DateTime($row["end_date"] . ' +7 day');

        $daterange = new DatePeriod($begin, new DateInterval('P7D'), $end);

        foreach ($daterange as $date) {
            $dates[] = $date->format("Y-m-d");
            array_push($reservations, $row['userID'], $row['classroomID'], $date->format("Y-m-d"), $row['start_time']);
        }
    }else{
        array_push($reservations, $row['userID'], $row['classroomID'], $row['start_date'], $row['start_time']);
    }

    print_r($reservations);

}

$conn->close();

?>