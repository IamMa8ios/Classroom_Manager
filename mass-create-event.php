<?php

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
        $firstRowSkipped = false;

        if (($handle = fopen($uploadFile, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                // Skip the first row
                if (!$firstRowSkipped) {
                    $firstRowSkipped = true;
                    continue;
                }

                $data[] = $row;
            }
            fclose($handle);
        }

        // Display the data
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    } else {
        echo "Upload failed.\n";
    }
}