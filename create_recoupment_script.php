<?php
require_once "session_manager.php";
require_once "db_connector.php";

$conn = connect2db();
$stmt = $conn->prepare("select start_date from reservation where id=?");
$initial_date=sanitize($_POST['initial-reservation']);
$stmt->bind_param("i", $initial_date);
$stmt->execute();
$result = $stmt->get_result(); // φέρνουμε την αρχική ημερομηνία του event
$start_date = $result->fetch_assoc();
$conn->close();

$conn = connect2db();
$sql="insert into recoupment_requests(userID, request_start, date_lost, date_recouped, classroomID, start_time, duration)
 values(?, str_to_date(?,'%Y-%m-%d'), str_to_date(?,'%Y-%m-%d'), ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

$userID=isset($_POST['teacher'])?sanitize($_POST['teacher']):$_SESSION['userID'];
$date_lost=date_create(sanitize($_POST['date_lost']));
$date_lost=date_format($date_lost,"Y-m-d");
$recoupment_date=date_create(sanitize($_POST['recoupment-date']));
$recoupment_date=date_format($recoupment_date,"Y-m-d");
$classID=sanitize($_POST['classID']);
$start_time=sanitize($_POST['start_time']);
$duration=sanitize($_POST['duration']);

// αφού τα δεδομένα από post είναι διαμορφωμένα σωστά, εισάγουμε το recoupment request στη DB
$stmt->bind_param("isssisi", $userID, $start_date['start_date'], $date_lost, $recoupment_date, $classID, $start_time, $duration);

if($stmt->execute()){ // μήνυμα για το χρήστη
    $_SESSION['notification']['title'] = 'Success!';
    $_SESSION['notification']['message'] = "The recoupment you requested has been submitted.";
}else{
    $_SESSION['notification']['title'] = 'Oops...';
    $_SESSION['notification']['message'] = "The recoupment you requested could not be completed.";
}

$conn->close();

header("Location: index-EN.php");
?>
