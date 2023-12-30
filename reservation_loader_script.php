<?php
function connect2db($role)
{
    $servername = "localhost";
    $username = "guest_user";
    $password = "123456";

// Create connection
    $conn = new mysqli($servername, $username, $password);
    $conn->select_db("classroom_booking_db");

// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

//echo "Connected successfully";

$conn = connect2db(1);
$classroomID = 1;
//    $sql = "select * from reservation where classroomID = ?";
//    $res = $conn->query($sql);

$stmt = $conn->prepare("select * from reservation where classroomID = ?");
$stmt->bind_param("i", $classroomID);
$stmt->execute();
$result = $stmt->get_result(); // get the mysqli result

$reservations = array();

while ($row = $result->fetch_assoc()) {

    $stmt = $conn->prepare("select name from lecture where id = ?");
    $stmt->bind_param("i", $row['lectureID']);
    $stmt->execute();
    $data = $stmt->get_result(); // get the mysqli result
    $lecture = $data->fetch_assoc();

    $stmt = $conn->prepare("select name from user where id = ?");
    $stmt->bind_param("i", $row['userID']);
    $stmt->execute();
    $data = $stmt->get_result(); // get the mysqli result
    $user_name = $data->fetch_assoc();

    $dateTime = DateTime::createFromFormat('H:i:s', $row['start_time']);

    // Add 3 hours
    $dateTime->modify('+' . $row['duration'] . ' hours');

    // Get the new time as a string
    $end_time = $dateTime->format('H:i:s');

    $reservation['title'] = $lecture['name'] . " by " . $user_name['name'];
    $reservation['start'] = $row['start_date'];
    $reservation['end'] = $row['end_date'];
    $reservation['startTime'] = $row['start_time'];
    $reservation['endTime'] = $end_time;
    if($row['day_of_week']!=null){
        $reservation['daysOfWeek']=$row['day_of_week'];
    }
    array_push($reservations, $reservation);


}
//print_r($reservations);

$conn->close();
$jsonData=json_encode($reservations);
header('Content-Type: application/json');
echo $jsonData;

?>