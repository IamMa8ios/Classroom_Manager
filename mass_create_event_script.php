<?php
require_once "db_connector.php";

// Ελέγχουμε εάν δόθηκε αρχείο
if (isset($_FILES['file'])) {
    $uploadDir = 'uploads/'; //Ορίζουμε το φάκελο που θα αποθηκευτεί το αρχείο

    // Κρατάμε το αρχικό όνομα του αρχείου
    $originalFileName = $_FILES['file']['name'];

    // Αφαιρούμε την κατάληξη του αρχείου
    $filenameWithoutExtension = pathinfo($originalFileName, PATHINFO_FILENAME);

    // Φέρνουμε το όνομα του χρήστη που ανέβασε το αρχείο
    session_start();
    $username = isset($_SESSION['name']) ? $_SESSION['name'] : 'unknown_user';

    // Παράγουμε το νέο όνομα αρχείου με τη μορφή "Όνομα αρχείου-Όνομα χρήστη-timestamp.csv"
    $timestamp = time();
    $newFileName = "{$filenameWithoutExtension}-{$username}-{$timestamp}.csv";
    $uploadFile = $uploadDir . $newFileName;

    // Μεταφέρουμε το αρχείο στον κατάλληλο φάκελο με το κατάλληλο όνομα
    if (move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {

        // Διαβάζουμε το αρχείο και κρατάμε τα δεδομένα σε πίνακα
        $data = array();
        if (($handle = fopen($uploadFile, 'r')) !== false) {

            //η 1η γραμμή περιέχει τα ονόματα κάθε στήλης
            $labels = fgetcsv($handle, 1000, ',');
            //στη συνέχεια είναι τα δεδομένα της κάθε κράτησης
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                $data[] = $row;
            }

            fclose($handle);
        }

        //Στη συνέχεια μορφοποιούμε κάθε στοιχείο του πίνακα ώστε να είναι κατάλληλο για εισαγωγή στη βάση δεδομένων
        foreach ($data as $datum){
            //διαχωρίζουμε τα πεδία με βάση το ;
            $datum=explode(";", $datum[0]);

            //επειδή οι καθηγητές δεν μπορούν να γνωρίζουν το id κάθε αίθουσας, καταχωρούν το όνομά της και το
            //κτήριό της, όπου ο συνδυασμός τους είναι μοναδικός, άρα μπορεί να δώσει το id της αίθουσας
            $conn=connect2db();
            $stmt = $conn->prepare("select id from classroom where building=? and name=?");
            $stmt->bind_param("ss", $datum[1], $datum[2]);
            $stmt->execute();
            $classID=$stmt->get_result()->fetch_assoc();
            $conn->close();

            //βρίσκουμε το id του μαθήματος
            $conn=connect2db();
            $stmt = $conn->prepare("select id from lecture where name=?");
            $stmt->bind_param("s", $datum[3]);
            $stmt->execute();
            $lectureID=$stmt->get_result()->fetch_assoc();
            $conn->close();

            //μετατρέπουμε τις ημερομηνίες στο κατάλληλο format, το οποίο το δίνουμε και στο query στη συνέχεια
            $startDate=date_format(date_create($datum[4]),"Y-m-d");
            $endDate=date_format(date_create($datum[5]),"Y-m-d");

            $conn=connect2db();
            $stmt = $conn->prepare("insert into reservation(userID, repeatable, classroomID, lectureID, 
                        start_date, end_date, start_time, duration, day_of_week) 
                        values(?, ?, ?, ?, str_to_date(?,'%Y-%m-%d'), str_to_date(?,'%Y-%m-%d'), ?, ?, ?)");

            //βρίσκουμε την ημέρα της εβδομάδας που αντιστοιχεί η αίθουσα σε περίπτωση που είναι επαναλαμβανόμενη η κράτηση
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
        $_SESSION['notification']['message'] = "Something went wrong while processing the file!";
    }
}

header("Location: index-EN.php");