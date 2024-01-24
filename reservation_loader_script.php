<?php
require_once "db_connector.php";

$conn=connect2db();

$classroomID = $_GET['classroom'];
//    $sql = "select * from reservation where classroomID = ?";
//    $res = $conn->query($sql);

//φέρνουμε τις κρατήσεις που αφορούν την αίθουσα που φορτώνεται κάθε φορά
$stmt = $conn->prepare("select * from reservation where classroomID = ?");
$stmt->bind_param("i", $classroomID);
$stmt->execute();
$result = $stmt->get_result(); // get the mysqli result

//δημιουργούμε πίνακα για να αποθηκεύσουμε τις κρατήσεις με την κατάλληλη μορφοποίηση
$reservations = array();
while ($row = $result->fetch_assoc()) {

    //φέρνουμε το όνομα του μαθήματος
    $stmt = $conn->prepare("select name from lecture where id = ?");
    $stmt->bind_param("i", $row['lectureID']);
    $stmt->execute();
    $data = $stmt->get_result();
    $lecture = $data->fetch_assoc();

    //φέρνουμε το όνομα του καθηγητή που παραδίδει το μάθημα
    $stmt = $conn->prepare("select name from user where id = ?");
    $stmt->bind_param("i", $row['userID']);
    $stmt->execute();
    $data = $stmt->get_result();
    $user_name = $data->fetch_assoc();

    //μορφοποιούμε την ώρα έναρξης
    $dateTime = DateTime::createFromFormat('H:i:s', $row['start_time']);

    // Προσθέτουμε το χρόνο που θα διαρκέσει η διάλεξη
    $dateTime->modify('+' . $row['duration'] . ' hours');

    //Μορφοποιούμε το νέο χρόνο κατάλληλα ώστε να λάβουμε την ώρα λήξης
    $end_time = $dateTime->format('H:i:s');

    //ορίζουμε τα στοιχεία του πίνακα με τον τρόπο με τον οποίο θα εμφανίζονται στο ημερολόγιο
    $reservation['title'] = $lecture['name'] . " by " . $user_name['name'];
    $reservation['start'] = $row['start_date']."T".$row['start_time'];
    $reservation['allDay'] = false;

    //εάν κάποια κράτηση είναι επαναλαμβανόμενη ορίζουμε τα κατάλληλα πεδία
    if($row['repeatable']==true){
        $reservation['end'] = $row['end_date']."T".$end_time;
        $reservation['startRecur'] = $reservation['start'];
        $reservation['endRecur'] = $reservation['end'];
        $reservation['daysOfWeek']=$row['day_of_week'];

    }else{
        $reservation['end'] = $row['start_date']."T".$end_time; //αλλιώς μόνο την ώρα λήξης
    }

    //ορίζουμε ένα url, έτσι ώστε όταν ο χρήστης κλικάρει στο ημερολόγιο να τον αποστέλλει στη σελίδα δημιουργίας
    //κράτησης ή αναπλήρωσης για αυτήν την αίθουσα
    $reservation['url']="create_event-EN.php?eventID=".$row['id'];
    $reservations[] = $reservation; //προσθέτουμε τη συγκεκριμένη κράτηση στο σύνολο
    $reservation=array(); //αδειάζουμε για την επόμενη κράτηση

}

$conn->close();

//μετατρέπουμε τα δεδομένα που φέραμε με κατάλληλο τρόπο ώστε να τα λάβει το ημερολόγιο της κάθε αίθουσας
//επειδή η fullCalendar είναι βιβλιοθήκη Javascript, πρέπει να κωδικοποιήσουμε σε JSON
$jsonData=json_encode($reservations);
header('Content-Type: application/json');
echo $jsonData;

?>