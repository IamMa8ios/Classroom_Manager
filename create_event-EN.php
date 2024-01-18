<?php
require_once "session_manager.php";
require_once "db_connector.php";

if ($_SESSION['role'] > 1) {

    $conn = connect2db();
    $sql = "";
    if ($_SESSION['role'] == 2) {
        $sql = "select * from lecture where userID = ?";
    } elseif ($_SESSION['role'] == 3) {
        $sql = "select * from lecture";
    } else {
        header("Location: index-EN.php");
    }
    $stmt = $conn->prepare($sql);
    if ($_SESSION['role'] == 2) {
        $stmt->bind_param("i", $_SESSION['userID']);
    }
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result
    $lectures = array();
    while ($lecture = $result->fetch_assoc()) {
        array_push($lectures, $lecture);
    }
    $conn->close();

    $action = $startDate = $endDate = $startTime = "";
    $duration = 0;
    $defaultLecture = $lectures[0];

    if (isset($_GET['date'])) {
        $action = "create";
        $startDate = $_GET['date'];

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
    } elseif (isset($_GET['eventID']) && $_SESSION['role'] == 3) {
        $action = "edit";
        $navTitle = "Editing Event";
        $formTitle = "Edit Event";

        $conn = connect2db();
        $stmt = $conn->prepare("select * from reservation where id=?");
        $stmt->bind_param("i", $_GET['eventID']);
        $stmt->execute();
        $result = $stmt->get_result(); // get the mysqli result
        $event = $result->fetch_assoc();
        $conn->close();

        $conn = connect2db();
        $stmt = $conn->prepare("select * from user where roleID=2");
        $stmt->execute();
        $result = $stmt->get_result(); // get the mysqli result
        $teachers = array();
        while ($teacher = $result->fetch_assoc()) {
            array_push($teachers, $teacher);
        }
        $conn->close();

        $startDate = $event['start_date'];
        $endDate = $event['end_date'];
        $startTime = $event['start_time'];
        $duration = $event['duration'];
    } else {
        header("Location: index-EN.php");
    }
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

                            <div class="container mt-5 bg-purple-svg" id="create-event-container">
<!--                                <div class="row g-3 my-3">-->
<!--                                                                    <div class="col-md-6">-->
<!--                                                                        <div class="form-check my-3">-->
<!--                                                                            <input class="form-check-input" type="checkbox" id="create-event"-->
<!--                                                                                   onclick="handleLabTheorySwitch('create-event','create-event-fields')">-->
<!--                                                                            <label class="form-check-label" for="create-event">--><?php //echo $formTitle; ?><!--</label>-->
<!--                                                                        </div>-->
<!--                                                                    </div>-->
<!--                                </div>-->
                                <div class="row container" id="create-event-fields">
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
                                <div class="mt-5 bg-purple-svg" id="create-recoupment-container">
                                    <form class="container p-2" id="recoupment_form" action="create_recoupment_script.php" method="post">
<!--                                        <div class="row g-3 my-3">-->
<!--                                            <div class="col-md-6">-->
<!--                                                <div class="form-check my-3">-->
<!--                                                    <input class="form-check-input" type="checkbox" name="recoupment" value=""-->
<!--                                                           id="recoupment"-->
<!--                                                           onclick="handleLabTheorySwitch('recoupment','recoupment-fields')">-->
<!--                                                    <label class="form-check-label" for="recoupment">Recoupment</label>-->
<!--                                                </div>-->
<!--                                            </div>-->
<!--                                        </div>-->

                                        <div class="row g-3 my-3 bg-purple-svg" id="recoupment-fields">
                                            <div class="col-md-6">
                                                <div class="input-group">
                                                    <label class="input-group-text" for="initial-reservation">Initial
                                                        Reservation</label>
                                                    <select class="form-select" name="initial-reservation" id="initial-reservation"
                                                            required>
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
                                                    <input type="date" class="form-control" name="recoupment-date" value=""
                                                           id="recoupment-date"
                                                           required readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6 my-3">
                                                <div class="input-group">
                                                    <label class="input-group-text" for="recoupment-date">Actual Recoupment Date</label>
                                                    <input type="date" class="form-control" name="recoupment-date" value=""
                                                           id="recoupment-date"
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
                                                    <label class="input-group-text" for="recoupment-duration">Recoupment
                                                        Duration</label>
                                                    <input type="number" class="form-control" name="recoupment-duration"
                                                           id="recoupment-duration" min="1" max="3" step="0.5" required>
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

    <script src='js/main.js'></script>
</body>

</html>