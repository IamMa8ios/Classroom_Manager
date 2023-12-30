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


function getBookings($classroomID, $roleID)
{
//echo "Connected successfully";

    $conn = connect2db($roleID);
//    $sql = "select * from reservation where classroomID = ?";
//    $res = $conn->query($sql);

    $stmt = $conn->prepare("select * from reservation where classroomID = ?");
    $stmt->bind_param("i", $classroomID);
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result

    $reservations = array();

    while ($row = $result->fetch_assoc()) {

//    if ($row['repeatable'] == 1) {
//        $begin = new DateTime($row["start_date"]);
//
//        $end = new DateTime($row["end_date"] . ' +7 day');
//
//        $daterange = new DatePeriod($begin, new DateInterval('P7D'), $end);
//
//        foreach ($daterange as $date) {
//            $dates[] = $date->format("Y-m-d");
//            array_push($reservations, $row['userID'], $row['classroomID'], $date->format("Y-m-d"), $row['start_time']);
//        }
//    }else{
//        array_push($reservations, $row['userID'], $row['classroomID'], $row['start_date'], $row['start_time']);
//    }

        $stmt = $conn->prepare("select name from lecture where id = ?");
        $stmt->bind_param("i", $row['lectureID']);
        $stmt->execute();
        $data = $stmt->get_result(); // get the mysqli result
        $lecture = $data->fetch_assoc();
//        print_r($lecture);

        $stmt = $conn->prepare("select name from user where id = ?");
        $stmt->bind_param("i", $row['userID']);
        $stmt->execute();
        $data = $stmt->get_result(); // get the mysqli result
        $user_name = $data->fetch_assoc();
//        print_r($user_name);

        $dateTime = DateTime::createFromFormat('H:i:s', $row['start_time']);

        // Add 3 hours
        $dateTime->modify('+' . $row['duration'] . ' hours');

        // Get the new time as a string
        $end_time = $dateTime->format('H:i:s');

        array_push($reservations, $row['repeatable'], $row['day_of_week'], $user_name['name'], $lecture['name'], $row['start_date'], $row['end_date'], $row['start_time'], $end_time);

//        print_r($reservations);

    }

    $conn->close();

//    print_r($reservations);


    // Encode the data as JSON for JavaScript
    $jsonData = json_encode([
        'title' => $reservations[3] . " by " . $reservations[2],
        'start' => $reservations[4],
        'end' => $reservations[5],
        'startTime' => $reservations[6],
        'endTime' => $reservations[7]
    ]);


    return $jsonData;
}

print_r(getBookings(1, 1))

?>