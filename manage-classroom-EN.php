<?php
require_once "session_manager.php";
require_once "db_connector.php";
print_r($_POST);

$title = "Creating new Class";
$name = $building = $capacity = $time_available_start = $time_available_end = $days_available = $type = $computers = $projector = $locked = "";

if (isset($_POST['delete'])) {
    if ($_SESSION['role'] > 2) {
        $conn = connect2db();
        $stmt = $conn->prepare("delete from classroom where id = ?");
        $stmt->bind_param("i", $_POST['delete']);
        if ($stmt->execute()) {
            echo "class " . $_POST['delete'] . " deleted";
        } else {
            echo "could not delete";
        }
        $conn->close();
    }
} else if ($_POST['edit']) {
    $conn = connect2db();
    $stmt = $conn->prepare("select * from classroom where id = ?");
    $stmt->bind_param("i", $_POST['edit']);
    $stmt->execute();
    $classData = $stmt->get_result()->fetch_assoc(); // get the mysqli result
    $conn->close();

    $name = $classData['name'];
    $title = "Editing " . $name;
    $building = $classData['building'];
    $capacity = $classData['capacity'];
    $time_available_start = $classData['time_available_start'];
    $time_available_end = $classData['time_available_end'];
    $days_available = $classData['days_available'];
    $type = $classData['type'];
    $computers = $classData['computers'];
    $projector = $classData['projector'];
    $locked = $classData['locked'];
}
//else{
//    header("Location: index-EN.php");
//}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
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

        <div class="container mt-5 p-3" id="create-event-container">
            <form id="create_event_form" action="create_event_script.php" method="post">

                <div class="row g-3 my-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="className">Class Name</label>
                            <input type="text" class="form-control" value="<?php echo $name; ?>"
                                   id="className" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="building">Building</label>
                            <input type="text" class="form-control" value="<?php echo $building; ?>" id="building"
                                   required>
                        </div>
                    </div>
                </div>

                <div class="row g-3 my-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="startTime">Available From</label>
                            <input type="time" class="form-control" name="startTime" id="startTime" min="09:00:00"
                                   max="20:00:00" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="endTime">Available Until</label>
                            <input type="time" class="form-control" name="endTime" id="endTime" min="10:00:00"
                                   max="21:00:00" required>
                        </div>
                    </div>
                </div>

                <div class="row g-3 my-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <label class="input-group-text" for="capacity">Capacity</label>
                            <input type="number" class="form-control" name="capacity" id="capacity" min="5" max="150"
                                   step="1" required>
                        </div>
                    </div>
                    <div class="col-md-3 daysOfWeek">
                        <div class="form-check my-3">
                            <input class="form-check-input" type="checkbox" name="projector" value="" id="projector">
                            <label class="form-check-label" for="projector">Projector</label>
                        </div>
                    </div>
                    <div class="col-md-3 daysOfWeek">
                        <div class="form-check my-3">
                            <input class="form-check-input" type="checkbox" name="locked" value="" id="locked">
                            <label class="form-check-label" for="locked">Locked</label>
                        </div>
                    </div>
                </div>

                <div class="row g-3 daysOfWeek">
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="monday" value="" id="monday">
                            <label class="form-check-label" for="monday">Monday</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="tuesday" value="" id="tuesday">
                            <label class="form-check-label" for="tuesday">Tuesday</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="wednesday" value="" id="wednesday">
                            <label class="form-check-label" for="wednesday">Wednesday</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="thursday" value="" id="thursday">
                            <label class="form-check-label" for="thursday">Thursday</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="friday" value="" id="friday">
                            <label class="form-check-label" for="friday">Friday</label>
                        </div>
                    </div>
                </div>


                <div class="row g-3 my-3">
                    <div class="col-md-6">
                        <input class="lab-switch" type="checkbox" id="lab-switch-toggle" data-theory="Theory"
                               data-lab="Lab" onclick="handleLabTheorySwitch('lab-switch-toggle','lab-capacity')">
                    </div>

                    <div class="col-md-6">
                        <div class="input-group visually-hidden" id="lab-capacity">
                            <label class="input-group-text" for="capacity">Capacity</label>
                            <input type="number" class="form-control" name="capacity" id="capacity" min="5" max="150"
                                   step="1" required>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col">
                        <button type="button" class="btn btn-secondary btn-not"><i
                                    class="fas fa-long-arrow-alt-left mx-2"></i> Back
                        </button>
                        <button type="button" class="btn btn-secondary btn-not" onclick="clearForm()">Clear <i
                                    class="fas fa-eraser"></i></button>
                        <button type="submit" class="btn btn-primary">Submit <i class="far fa-check-circle"></i>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>


</div>


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