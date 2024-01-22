<?php

if(!isset($_SESSION)){
    session_start();
}

//Ελέγχουμε εάν ο κατάλληλος χρήστης στέλνει το αίτημα post, αλλιώς δε θα πρέπει να εξυπηρετηθεί
if (!isset($_POST) && ($_SESSION['role']==3 || $_SESSION['role']==2)) {
    header("Location: index-EN.php");
}

require_once "db_connector.php";

//Αρχικοποιούμε όλα τα στοιχεία που θα αποθηκεύσουμε στη βάση δεδομένων
$classID = $userID = $lectureID = $dayOfWeek = $startTime = $duration = $startDate = $endDate = $errors = "";
$params = array();

//Ξεκινάμε για κάθε στοιχείο που δίνεται, να αφαιρούμε κακόβουλους χαρακτήρες και ελέγχουμε ότι έχουν συμπληρωθεί
//όλα τα πεδία, ώστε να εμφανίσουμε τα κατάλληλα μηνύματα. Σε κάθε έλεγχο, προστίθεται κάθε σφάλμα στο μήνυμα που
//θα εμφανιστεί στο τέλος
if(isset($_POST['teacher'])){
    $userID = sanitize($_POST['teacher']);
}elseif (isset($_SESSION['userID'])) {
    $userID = sanitize($_SESSION['userID']);
} else {
    $errors="You forgot to set a user!\n";
}

if (isset($_POST['classID'])) {
    $classID = sanitize($_POST['classID']);
} else {
    $errors=$errors."You forgot to set a classroom!\n";
}

if (isset($_POST['lectureID'])) {
    $lectureID = sanitize($_POST['lectureID']);
} else {
    $errors=$errors."You forgot to set a lecture!\n";
}

if (isset($_POST['startDate'])) {
    $startDate = sanitize($_POST['startDate']);
} else {
    $errors=$errors."You forgot to set a start date!\n";
}

if (isset($_POST['startTime'])) {
    $startTime = sanitize($_POST['startTime']);
} else {
    $errors=$errors."You forgot to set a start time!\n";
}

if (isset($_POST['duration'])) {
    $duration = sanitize($_POST['duration']);
} else {
    $errors=$errors."You forgot to set duration!\n";
}

//εάν υπήρξε κάποια παράλειψη έως τώρα, καταχωρούμε το κατάλληλο μήνυμα ώστε να το εμφανίσει στη σελίδα της κράτησης
if(!empty($errors)){
    $_SESSION['notification']['title'] = "Oops...";
    $_SESSION['notification']['message'] = $errors;
    header("Location: create_event-EN.php?date=".$startDate);
}

$conn = connect2db();

//εάν η κράτηση είναι επαναλαμβανόμενη, θα πρέπει να γίνει διαφοροποίηση λόγω του πλήθους των παραμέτρων που θα δοθούν
if (isset($_POST['recurring'])) {
    $repeatable = true;

    //Εφόσον πρόκειται για επαναλαμβανόμενη κράτηση, πρέπει να υπάρχει και ημερομηνία λήξης
    if (isset($_POST['endDate'])) {

        //αντιστοιχούμε την ημέρα της κράτησης με μια αριθμητική τιμή (όπου Κυριακή=0, Δευτέρα=1 κ.ο.κ.)
        //την αποθηκεύουμε στη μορφή [0-9] ώστε να την αναγνωρίσει η βιβλιοθήκη fullCalendar
        $dayOfWeek = "[" . date('w', strtotime(sanitize($_POST['startDate']))) . "]";
        $endDate=sanitize($_POST['endDate']);

        //καταχωρούμε το κατάλληλο query ανάλογα με το αν πρόκειται για νέα κράτηση ή ενημέρωση
        $sql="";
        if(isset($_POST['edit'])){
            $sql="update reservation set userID=?, classroomID=?, lectureID=?, repeatable=?, start_date=?, end_date=?, day_of_week=?, start_time=?, duration=? where id=".$_POST['edit'];
        }else{
            $sql="insert into reservation(userID, classroomID, lectureID, repeatable, start_date, end_date, day_of_week, start_time, duration) values(?, ?, ?, ?, ?, ?, ?, ?, ?)";
        }
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiiissssi", $userID, $classID, $lectureID, $repeatable, $startDate, $endDate, $dayOfWeek, $startTime, $duration);

    } else {
        $_SESSION['notification']['title'] = "Oops...";
        $_SESSION['notification']['message'] = $errors."You forgot to add an end date!\n";
        header("Location: create_event-EN.php?date=".$startDate);
    }

} else {
    $repeatable = false;
    $sql="";
    if(isset($_POST['edit'])){
        $sql="update reservation set userID=?, classroomID=?, lectureID=?, start_date=?, start_time=?, duration=? where id=".$_POST['edit'];
    }else{
        $sql="insert into reservation(userID, classroomID, lectureID, start_date, start_time, duration) values(?, ?, ?, ?, ?, ?)";
    }
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiissi", $userID, $classID, $lectureID, $startDate, $startTime, $duration);
}

//Καταχωρούμε τα κατάλληλα μηνύματα για επιτυχία/αποτυχία και στη συνέχεια μεταφέρουμε το χρήστη στην κατάλληλη σελίδα
if ($stmt->execute()) {
    $_SESSION['notification']['title'] = "Success!";
    $_SESSION['notification']['message'] = "Class ".$_SESSION['className']." has been booked!";
    $conn->close();
    header("Location: index-EN.php");
} else {
    $_SESSION['notification']['title'] = "Success!";
    $_SESSION['notification']['message'] = "Class ".$_SESSION['className']." could not be booked!";
    $conn->close();
    header("Location: create_event-EN.php?date=".$startDate);
}
