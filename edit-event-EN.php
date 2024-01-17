<?php
require_once "session_manager.php";
require_once "db_connector.php";


//$conn = connect2db();
//$stmt = $conn->prepare("select * from lecture where userID = ?");
//$stmt->bind_param("i", $_SESSION['userID']);
//$stmt->execute();
//$result = $stmt->get_result(); // get the mysqli result
//$lectures = array();
//while ($lecture = $result->fetch_assoc()) {
//    array_push($lectures, $lecture);
//}
//$conn->close();
//
//$startDate = "";
//if (isset($_GET['date'])) {
//    $startDate = $_GET['date'];
//}
//
//$conn = connect2db();
//$stmt = $conn->prepare("select time_available_start as `start`, time_available_end as `end` from classroom where id = ?");
//$stmt->bind_param("i", $_SESSION['classID']);
//$stmt->execute();
//$time = $stmt->get_result()->fetch_assoc(); // get the mysqli result
//$conn->close();
//
//$minStart = $time['start'];
//$maxStart = DateTime::createFromFormat('H:i:s', $time['end']);
//$maxStart->modify('-1 hours');
//$maxStart = $maxStart->format('H:i:s');

$navTitle = "Edit Event";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create a reservation</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- FullCalendar CSS -->
    <link href='fullcalendar/packages/core/main.css' rel='stylesheet'/>
    <link href='fullcalendar/packages/daygrid/main.css' rel='stylesheet'/>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div id="wrapper">

    <!-- Sidebar -->
    <?php require_once "sidebar-EN.php" ?>

    <!-- Page Content -->
    <div id="content">
        <?php require_once "navbar-EN.php" ?>

        <!--        FIXME: get event date for edit form -->


    </div>
</div>


<!-- Bootstrap JS, Popper.js, and jQuery -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<!-- FullCalendar JS -->
<script src='fullcalendar/packages/core/main.js'></script>
<script src='fullcalendar/packages/interaction/main.js'></script>
<script src='fullcalendar/packages/daygrid/main.js'></script>
<script src='fullcalendar/packages/timegrid/main.js'></script>
<script src='fullcalendar/packages/list/main.js'></script>

<script src='js/main.js'></script>
</body>

</html>