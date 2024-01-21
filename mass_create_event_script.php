<?php
require_once "db_connector.php";

// Check if a file was uploaded
if (isset($_FILES['file'])) {
    $uploadDir = 'uploads/'; // Adjust the path where you want to store the uploaded file

    // Get original file name
    $originalFileName = $_FILES['file']['name'];

    // Extract the filename without the extension
    $filenameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);

    // Get the current user's username from the session (replace 'your_username_key' with your actual session key)
    session_start();
    $username = isset($_SESSION['name']) ? $_SESSION['name'] : 'unknown_user';

    // Generate a new filename
    $timestamp = time();
    $newFileName = "{$filenameWithoutExtension}-{$username}-{$timestamp}.csv";
    $uploadFile = $uploadDir . $newFileName;

    // Move the uploaded file to the specified directory with the new filename
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
//        echo "File is valid, and was successfully uploaded with the new filename: $newFileName.\n";

        // Read the CSV file contents, ignoring the first row
        $data = array();
        $linesAdded=0;

        if (($handle = fopen($uploadFile, 'r')) !== false) {

            $labels = fgetcsv($handle, 1000, ',');
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $data[] = $row;
            }

            fclose($handle);
        }

        // Display the data
        print_r($labels);


        foreach ($data as $datum){
            $datum=explode(";", $datum[0]);
            printArray($datum);

            $conn=connect2db();
            $stmt = $conn->prepare("select id from classroom where building=? and name=?");
            $stmt->bind_param("ss", $datum[1], $datum[2]);
            $stmt->execute();
            $classID=$stmt->get_result()->fetch_assoc();
            printArray($classID);
            $conn->close();

            $conn=connect2db();
            $stmt = $conn->prepare("select id from lecture where name=?");
            $stmt->bind_param("s", $datum[3]);
            $stmt->execute();
            $lectureID=$stmt->get_result()->fetch_assoc();
            $conn->close();

            $startDate=date_format(date_create($datum[4]),"Y-m-d");
            $endDate=date_format(date_create($datum[5]),"Y-m-d");

            $conn=connect2db();
            $stmt = $conn->prepare("insert into reservation(userID, repeatable, classroomID, lectureID, 
                        start_date, end_date, start_time, duration, day_of_week) 
                        values(?, ?, ?, ?, str_to_date(?,'%Y-%m-%d'), str_to_date(?,'%Y-%m-%d'), ?, ?, ?)");
            $dayOfWeek=null;
            if($datum[0]){
                $unixTimestamp = strtotime($datum[4]);
                $dayOfWeek = date("w", $unixTimestamp);
            }

            $stmt->bind_param("iisiisssi", $_SESSION['userID'], $datum[0], $classID['id'], $lectureID['id'], $startDate, $endDate, $datum[6], $datum[7], $dayOfWeek);
            $stmt->execute();
            $conn->close();

        }

    } else {
        $_SESSION['notification']['title'] = "Oops...";
        $_SESSION['notification']['message'] = "Bookings from file were successfully added!";
    }
}

header("Location: index-EN.php");