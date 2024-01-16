<?php
require_once "session_manager.php";
require_once "db_connector.php";

$conn = connect2db();
$stmt = $conn->prepare("select * from lecture where userID = ?");
$stmt->bind_param("i", $_SESSION['userID']);
$stmt->execute();
$result = $stmt->get_result(); // get the mysqli result
$lectures = array();
while ($lecture = $result->fetch_assoc()) {
    array_push($lectures, $lecture);
}
$conn->close();

$startDate = "";
if (isset($_GET['date'])) {
    $startDate = $_GET['date'];
}

$conn = connect2db();
$stmt = $conn->prepare("select time_available_start as `start`, time_available_end as `end` from classroom where id = ?");
$stmt->bind_param("i", $_SESSION['classID']);
$stmt->execute();
$time = $stmt->get_result()->fetch_assoc(); // get the mysqli result
$conn->close();

$minStart = $time['start'];
$maxStart = DateTime::createFromFormat('H:i:s', $time['end']);
$maxStart->modify('-1 hours');
$maxStart = $maxStart->format('H:i:s');

$navTitle = "Creating New Event";
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

        <div class="container mt-5 bg-purple-svg" id="create-event-container">
            <div class="row g-3 my-3">
                <div class="col-md-6">
                    <div class="form-check my-3">
                        <input class="form-check-input" type="checkbox" id="create-event"
                               onclick="handleLabTheorySwitch('create-event','create-event-fields')">
                        <label class="form-check-label" for="create-event">Create Event</label>
                    </div>
                </div>
            </div>
            <div class="row visually-hidden" id="create-event-fields">
                <form id="create_event_form" action="create_event_script.php" method="post">
                    <input type="text" class="form-control" name="classID" id="classID"
                           value="<?php echo $_SESSION['classID']; ?>" style="display: none" required readonly>
                    <div class="row g-3 my-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-group-text" for="className">Class Name</label>
                                <input type="text" class="form-control" value="<?php echo $_SESSION['className']; ?>"
                                       id="className" required readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-group-text" for="lectures">Lectures</label>
                                <select class="form-select" name="lectureID" id="lectures" required>
                                    <?php foreach ($lectures as $lecture) { ?>
                                        <option value="<?php echo $lecture['id']; ?>">
                                            <?php echo $lecture['code'] . " - " . $lecture['name'] ?>
                                        </option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 my-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-group-text" for="startDate">Start Date</label>
                                <input type="date" class="form-control" name="startDate"
                                       value="<?php echo $startDate ?>" id="startDate" required readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-group-text" for="endDate">End Date</label>
                                <input type="date" class="form-control" name="endDate" id="endDate">
                            </div>
                        </div>
                        <div class="col-md-6" style="display: none">
                            <div class="input-group">
                                <label class="input-group-text" for="lostDate">Date Lost</label>
                                <input type="date" class="form-control" name="lostDate" id="lostDate">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 my-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-group-text" for="startTime">Start Time</label>
                                <input type="time" class="form-control" name="startTime" id="startTime"
                                       min="<?php echo $minStart; ?>" max="<?php echo $maxStart; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-group-text" for="duration">Duration</label>
                                <input type="number" class="form-control" name="duration" id="duration" min="1" max="3"
                                       step="0.5" required>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 my-3 daysOfWeek">
                        <div class="col-md-6">
                            <div class="form-check my-3">
                                <input class="form-check-input" type="checkbox" name="recurring" value=""
                                       id="recurring">
                                <label class="form-check-label" for="recurring">Recurring</label>
                            </div>
                        </div>
                    </div>


                    <div class="row my-3">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-secondary btn-not" onclick="clearEventForm()">Clear <i
                                        class="fas fa-eraser"></i></button>
                            <button type="submit" class="btn btn-primary">Submit <i
                                        class="far fa-check-circle"></i></button>
                        </div>
                    </div>
                </form>

                <h2 class="my-3 position-relative d-flex justify-content-center align-items-center">OR</h2>

                <form enctype="multipart/form-data" id="create_event_from_file_form" action="mass-create-event.php"
                      method="post">
                    <div class="row g-3 my-3">
                        <div class="col-md-6">
                            <input type="file" name="file" class="form-control" id="fileUpload" accept=".csv">
                        </div>
                    </div>
                    <div class="row my-3">
                        <div class="col">
                            <button type="button" class="btn btn-secondary btn-not" onclick="clearEventFileForm()">Clear
                                <i
                                        class="fas fa-eraser"></i></button>
                            <button type="submit" class="btn btn-primary">Submit <i
                                        class="far fa-check-circle"></i></button>
                        </div>
                    </div>
                </form>

            </div>
        </div>

        <div class="container mt-5 bg-purple-svg" id="create-recoupment-container">
            <form id="recoupment_form" action="create_recoupment_script.php" method="post">
                <div class="row g-3 my-3">
                    <div class="col-md-6">
                        <div class="form-check my-3">
                            <input class="form-check-input" type="checkbox" name="recoupment" value="" id="recoupment"
                                   onclick="handleLabTheorySwitch('recoupment','recoupment-fields')">
                            <label class="form-check-label" for="recoupment">Recoupment</label>
                        </div>
                    </div>
                </div>
                <!-- hidden until Recoupment is checked-->
                <!--                    αναγνωριστικό αίτησης, ημερομηνία αίτησης, κατάσταση αίτησης (System auto)-->
                <!--                    αρχική κράτηση (lecture name, classname, user name) , ημερομηνία προς αναπλήρωση (προσδιορίζουν επακριβώς πότε δεν μπορεί να γίνει η διδασκαλία του μαθήματος)-->
                <!--                    ημερομηνία αναπλήρωσης, αίθουσα αναπλήρωσης,-->
                <!--                    ώρα αναπλήρωσης, και διάρκεια αναπλήρωσης FIXME: do recoupment -->
                <div class="row g-3 my-3 bg-purple-svg visually-hidden" id="recoupment-fields">
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="initial-reservation">Initial Reservation</label>
                            <select class="form-select" name="initial-reservation" id="initial-reservation" required>
                                <?php foreach ($lectures as $lecture) { // change ?>
                                    <option value="<?php echo $lecture['id']; ?>">
                                        <?php echo $lecture['code'] . " - " . $lecture['name'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 my-3">
                        <div class="input-group">
                            <label class="input-group-text" for="recoupment-date">Recoupment Date</label>
                            <input type="date" class="form-control" name="recoupment-date" value="" id="recoupment-date"
                                   required readonly>
                        </div>
                    </div>
                    <div class="col-md-6 my-3">
                        <div class="input-group">
                            <label class="input-group-text" for="recoupment-date">Actual Recoupment Date</label>
                            <input type="date" class="form-control" name="recoupment-date" value="" id="recoupment-date"
                                   required readonly>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="recoupment-hall">Recoupment Hall</label>
                            <select class="form-select" name="recoupment-hall" id="recoupment-hall" required>
                                <?php foreach ($lectures as $lecture) { // change ?>
                                    <option value="<?php echo $lecture['id']; ?>">
                                        <?php echo $lecture['code'] . " - " . $lecture['name'] ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 my-3">
                        <div class="input-group">
                            <label class="input-group-text" for="recoupment-time">Recoupment Time</label>
                            <input type="time" class="form-control" name="recoupment-time" id="recoupment-time"
                                   min="<?php echo $minStart; ?>" max="<?php echo $maxStart; ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6 my-3">
                        <div class="input-group">
                            <label class="input-group-text" for="recoupment-duration">Recoupment Duration</label>
                            <input type="number" class="form-control" name="recoupment-duration"
                                   id="recoupment-duration" min="1" max="3" step="0.5" required>
                        </div>
                    </div>

                    <div class="row my-3">
                        <div class="col-md-6">
                            <button type="button" class="btn btn-secondary btn-not" onclick="clearRecoupmentForm()">Clear <i
                                        class="fas fa-eraser"></i></button>
                            <button type="submit" class="btn btn-primary">Submit <i
                                        class="far fa-check-circle"></i></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>


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