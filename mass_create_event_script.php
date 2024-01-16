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
        echo "File is valid, and was successfully uploaded with the new filename: $newFileName.\n";

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
        echo '<pre>';
        print_r($data);
        echo '</pre>';


//        foreach ($data as $datum){
//            $conn=connect2db();
//            $stmt = $conn->prepare("insert into reservation(userID, repeatable, classroomID, lectureID,
//                        start_date, end_date, start_time, duration, day_of_week) values(?, ?, ?, ?, ?, ?, ?, ?, ?)");
//            $dayOfWeek=null;
//            if($datum[0]){
//                $unixTimestamp = strtotime($datum[4]);
//                $dayOfWeek = date("w", $unixTimestamp);
//            }
//            $stmt->bind_param("iisiisssi", $_SESSION['userID'], $datum[0], $datum[1], $datum[2], $datum[3], $datum[4], $datum[5], $datum[6], $datum[7], dayOfWeek);
//            $stmt->execute();
//            $conn->close();
//        }
//
//        exit();

    } else {
        echo "Upload failed.\n";
    }
}