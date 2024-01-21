<?php
require_once "session_manager.php";
require_once "db_connector.php";
require_once "date_repeater.php";

if ($_SESSION['role'] > 1) {

    $conn = connect2db();
    $sql="";
    if($_SESSION['role']==2){
        $sql="select * from lecture where userID = ?";
    }elseif ($_SESSION['role']==3){
        $sql="select * from lecture";
    }else{
        header("Location: index-EN.php");
    }
    $stmt = $conn->prepare($sql);
    if ($_SESSION['role']==2){
        $stmt->bind_param("i", $_SESSION['userID']);
    }
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result
    $lectures = array();
    while ($lecture = $result->fetch_assoc()) {
        $lectures[] = $lecture;
    }
    $conn->close();

    $action=$startDate=$endDate=$startTime="";
    $duration=0;
    $defaultLecture=$lectures[0];

    if (isset($_GET['date'])) {
        $action="create";
        $startDate = $_GET['date'];

        // For new event
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
        $formTitle = "New Event";

        // For recoupments

        // get all starting and ending dates for each event
        $conn = connect2db();
        $sql="select id, start_date as `start`, end_date as `end`, lectureID, repeatable from reservation";
        if($_SESSION['role']==2) $sql=$sql." where userID=?";
        $stmt = $conn->prepare($sql);
        if($_SESSION['role']==2) $stmt->bind_param("i", $_SESSION['userID']);
        $stmt->execute();
        $result=$stmt->get_result();
        $originalEvents=array();
        while ($reservation = $result->fetch_assoc()) {
            $originalEvents[] = $reservation;
        }
        $conn->close();

        //get lecture name for event
        foreach ($originalEvents as $key=>$event){
            foreach ($lectures as $lecture){
//                echo $lecture['id']==$event['lectureID']?"matched":"not matched";
                if($lecture['id']==$event['lectureID']){
                    $originalEvents[$key]['lecture']=$lecture['name'];
                    break;
                }
            }

            if($event['repeatable']){
                $originalEvents[$key]['possible_dates']=getDatesBetween($event['start'], $event['end']);
            }else{
                $originalEvents[$key]['possible_dates']=[$event['start']];
            }
        }

    }elseif (isset($_GET['eventID']) && $_SESSION['role']==3){
        $action="edit";
        $navTitle = "Editing Event";
        $formTitle = "Edit Event";

        $conn = connect2db();
        $stmt = $conn->prepare("select * from reservation where id=?");
        $stmt->bind_param("i", $_GET['eventID']);
        $stmt->execute();
        $result = $stmt->get_result(); // get the mysqli result
        $event = $result->fetch_assoc();
        $conn->close();



        $startDate=$event['start_date'];
        $endDate=$event['end_date'];
        $startTime=$event['start_time'];
        $duration=$event['duration'];
    }else{
        header("Location: index-EN.php");
    }

    $conn = connect2db();
    $stmt = $conn->prepare("select * from user where roleID=2");
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result
    $teachers=array();
    while ($teacher = $result->fetch_assoc()) {
        $teachers[] = $teacher;
    }
    $conn->close();
} else {
    header("Location: index-EN.php");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $navTitle; ?></title>
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
    <?php require_once "sidebar-EN.php"; ?>

    <!-- Page Content -->
    <div id="content">
        <?php require_once "navbar-EN.php"; ?>
        <?php require_once "modal.php"; ?>

        <?php if (!isset($_GET['eventID'])) { ?>

            <div class="accordion accordion-flush mt-4 container p-4" id="accordionFlushExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" style="border-top-left-radius: 999em; border-top-right-radius: 999em;">
<!--                        style="background: var(--dark-purple);color: var(--white-ish); outline: 2px solid var(--white-ish)"-->
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                            Create Event
                        </button>
                    </h2>
                    <div id="flush-collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
                        <div class="accordion-body">

                            <div class="container mt-2 bg-purple-svg" id="create-event-container">

                                <div class="row " id="create-event-fields">
                                    <form id="create_event_form" action="create_event_script.php" method="post">
                                        <input type="text" class="form-control" name="classID" id="classID"
                                               value="<?php echo $_SESSION['classID']; ?>" style="display: none" required readonly>
                                        <div class="row g-3 my-3">
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <label class="input-group-text" for="className">Class Name</label>
                                                    <input type="text" class="form-control"
                                                           value="<?php echo $_SESSION['className']; ?>"
                                                           id="className" required readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <label class="input-group-text" for="lectures">Lectures</label>
                                                    <select class="form-select" name="lectureID" id="lectures" required>
                                                        <?php foreach ($lectures as $lecture) { ?>
                                                            <option value="<?php echo $lecture['id']; ?>"
                                                                <?php if ($_SESSION['role'] == 3 && $lecture['id'] == $event['lectureID']) echo " selected "; ?>>
                                                                <?php echo $lecture['code'] . " - " . $lecture['name']; ?>
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
                                                           value="<?php echo $startDate; ?>" id="startDate" required readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <label class="input-group-text" for="endDate">End Date</label>
                                                    <input type="date" class="form-control" name="endDate"
                                                           value="<?php echo $endDate; ?>" id="endDate">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-3 my-3">
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <label class="input-group-text" for="startTime">Start Time</label>
                                                    <input type="time" class="form-control" name="startTime"
                                                           value="<?php echo $startTime; ?>" id="startTime"
                                                           min="<?php echo $minStart; ?>" max="<?php echo $maxStart; ?>" required>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <label class="input-group-text" for="duration">Duration</label>
                                                    <input type="number" class="form-control" name="duration"
                                                           value="<?php echo $duration; ?>" id="duration" min="1" max="3"
                                                           step="0.5" required>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row g-3 my-3 daysOfWeek">
                                            <?php if (isset($_GET['eventID']) && $_SESSION['role'] == 3) { ?>
                                                <div class="col-md-6">
                                                    <div class="input-group">
                                                        <label class="input-group-text" for="userID">Teacher</label>
                                                        <select class="form-select" name="teacher" id="teacher" required>
                                                            <?php foreach ($teachers as $teacher) { ?>
                                                                <option value="<?php echo $teacher['id']; ?>" <?php if ($teacher['id'] == $event['userID']) echo " selected "; ?>>
                                                                    <?php echo $teacher['name']; ?>
                                                                </option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <div class="col-md-6">
                                                <div class="form-check my-3">
                                                    <input class="form-check-input" type="checkbox" name="recurring" value=""
                                                           id="recurring">
                                                    <label class="form-check-label"
                                                           for="recurring" <?php if (isset($event['repeatable']) && $event['repeatable']) echo "checked"; ?> >Recurring</label>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="row my-3">
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-secondary btn-not" onclick="clearEventForm()">Clear
                                                    <i
                                                            class="fas fa-eraser"></i></button>
                                                <button type="submit" class="btn btn-primary"
                                                    <?php if (isset($_GET['eventID'])) {
                                                        echo 'name="edit" value="' . $_GET['eventID'] . '"';
                                                    } ?>
                                                >Submit <i class="far fa-check-circle"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    <?php if (!isset($_GET['eventID'])) { ?>
                                        <h2 class="my-3 position-relative d-flex justify-content-center align-items-center">OR</h2>

                                        <form enctype="multipart/form-data" id="create_event_from_file_form"
                                              action="mass_create_event_script.php"
                                              method="post">
                                            <div class="row g-3 my-3">
                                                <div class="col-md-6">
                                                    <input type="file" name="file" class="form-control" id="fileUpload" accept=".csv">
                                                </div>
                                            </div>
                                            <div class="row my-3">
                                                <div class="col">
                                                    <button type="button" class="btn btn-secondary btn-not"
                                                            onclick="clearEventFileForm()">Clear
                                                        <i
                                                                class="fas fa-eraser"></i></button>
                                                    <button type="submit" class="btn btn-primary">Submit
                                                        <i class="far fa-check-circle"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>

                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#flush-collapseTwo" aria-expanded="false"
                                    aria-controls="flush-collapseTwo">
                                Create Recoupment
                            </button>
                        </h2>
                        <div id="flush-collapseTwo" class="accordion-collapse collapse"
                             data-bs-parent="#accordionFlushExample">
                            <div class="accordion-body">
                                <div class="mt-2 bg-purple-svg" id="create-recoupment-container">
                                    <form class="container p-2" id="recoupment_form" action="create_recoupment_script.php" method="post">
                                        <div class="row g-3 my-3 bg-purple-svg" id="recoupment-fields">

                                            <input type="text" class="form-control" name="classID" id="classID"
                                                   value="<?php echo $_SESSION['classID']; ?>" style="display: none" required readonly>
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <label class="input-group-text" for="className">Class Name</label>
                                                    <input type="text" class="form-control"
                                                           value="<?php echo $_SESSION['className']; ?>"
                                                           id="className" required readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-6 my-3">
                                                <div class="input-group">
                                                    <label class="input-group-text" for="recoupment-date">Recoupment Date</label>
                                                    <input type="date" class="form-control" name="recoupment-date" value="<?php echo $_GET['date']; ?>"
                                                           id="recoupment-date"
                                                           required readonly>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <label class="input-group-text" for="initial-reservation">Initial Reservation</label>
                                                    <select class="form-select" name="initial-reservation" id="initial-reservation"
                                                            onchange="updateOptions()" required>
                                                        <?php foreach ($originalEvents as $event) { ?>
                                                            <option value="<?php echo $event['id']; ?>">
                                                                <?php echo $event['lecture']." - ".$event['start']; ?>
                                                            </option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6 my-3">
                                                <div class="input-group">
                                                    <label class="input-group-text" for="date_lost">Date Lost</label>
                                                    <select class="form-select" name="date_lost" id="date_lost"
                                                            required>
                                                        <?php foreach ($originalEvents as $event) {  ?>
                                                        <?php foreach ($event['possible_dates'] as $possible_date) {  ?>
                                                            <option class="<?php echo $event['id']; ?>" id="<?php echo $event['id']; ?>" value="<?php echo $possible_date; ?>">
                                                                <?php echo $event['lecture']." - ".$possible_date; ?>
                                                            </option>
                                                        <?php } }?>
                                                    </select>
                                                </div>
                                            </div>

                                            <?php if($_SESSION['role']==3){ ?>
                                            <div class="col-md-6 my-3">
                                                <div class="input-group">
                                                    <label class="input-group-text" for="teacher">Teacher</label>
                                                    <select class="form-select" name="teacher" id="teacher"
                                                            required>
                                                        <?php foreach ($teachers as $teacher) {  ?>
                                                                <option id="teacher-<?php echo $teacher['id']; ?>" value="<?php echo $teacher['id']; ?>">
                                                                    <?php echo $teacher['name']; ?>
                                                                </option>
                                                            <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <?php } ?>

                                            <div class="col-md-6 my-3">
                                                <div class="input-group">
                                                    <label class="input-group-text" for="start-time">Start Time</label>
                                                    <input type="time" class="form-control" name="start_time" id="start-time"
                                                           min="<?php echo $minStart; ?>" max="<?php echo $maxStart; ?>" required>
                                                </div>
                                            </div>

                                            <div class="col-md-6 my-3">
                                                <div class="input-group">
                                                    <label class="input-group-text" for="duration">Duration</label>
                                                    <input type="number" class="form-control" name="duration"
                                                           id="duration" min="1" max="3" step="0.5" required>
                                                </div>
                                            </div>

                                            <div class="row my-3">
                                                <div class="col-md-6">
                                                    <button type="button" class="btn btn-secondary btn-not"
                                                            onclick="clearRecoupmentForm()">
                                                        Clear <i
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
                    </div>
                    <!--            <div class="accordion-item">-->
                    <!--                <h2 class="accordion-header">-->
                    <!--                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">-->
                    <!--                        Accordion Item #3-->
                    <!--                    </button>-->
                    <!--                </h2>-->
                    <!--                <div id="flush-collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">-->
                    <!--                    <div class="accordion-body">Placeholder content for this accordion, which is intended to demonstrate the <code>.accordion-flush</code> class. This is the third item's accordion body. Nothing more exciting happening here in terms of content, but just filling up the space to make it look, at least at first glance, a bit more representative of how this would look in a real-world application.</div>-->
                    <!--                </div>-->
                    <!--            </div>-->
                </div>




            </div>

        <?php } ?>


    </div>


    <!-- Bootstrap JS, Popper.js, and jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

    <script>
        // Function to update options in mySelect based on the selected option in groups
        function updateOptions() {
            var original_events = document.getElementById('initial-reservation');
            var eventID = original_events.value;

            var possible_dates = document.getElementById('date_lost');
            var options = possible_dates.getElementsByTagName('option');


            for (var i = 0; i < options.length; i++) {
                var option = options[i];
                if (option.id===eventID) {
                    option.style.display="block";
                }else{
                    option.style.display="none";
                }
            }
        }

        updateOptions();
    </script>

    <script src='js/main.js'></script>
</body>

</html>