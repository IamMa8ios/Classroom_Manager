<?php
require_once "session_manager.php";

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- FullCalendar CSS -->
    <link href='fullcalendar/packages/core/main.css' rel='stylesheet' />
    <link href='fullcalendar/packages/daygrid/main.css' rel='stylesheet' />

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

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
                <form id="create_event_form">
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
                                <select class="form-select" name="lecture" id="lectures" required>
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
                                    value="<?php echo $_GET['date']; ?>" id="startDate" required readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-group-text" for="endDate">End Date</label>
                                <input type="date" class="form-control" id="endDate">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 my-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-group-text" for="startTime">Start Time</label>
                                <input type="time" class="form-control" id="startTime">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="input-group">
                                <label class="input-group-text" for="duration">Duration</label>
                                <input type="number" class="form-control" id="duration" min="1" max="3" step="0.5">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3 my-3 daysOfWeek">
                        <div class="col-md-6">
                            <label class="form-label">Choose which days you would like to book</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="monday">
                                <label class="form-check-label" for="monday">Monday</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="tuesday">
                                <label class="form-check-label" for="tuesday">Tuesday</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="wednesday">
                                <label class="form-check-label" for="wednesday">Wednesday</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="thursday">
                                <label class="form-check-label" for="thursday">Thursday</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="" id="friday">
                                <label class="form-check-label" for="friday">Friday</label>
                            </div>
                            <div class="form-check my-3">
                                <input class="form-check-input" type="checkbox" value="" id="recurring">
                                <label class="form-check-label" for="recurring">Recurring</label>
                            </div>
                        </div>
                    </div>

                    <h2 class="my-3 position-relative d-flex justify-content-center align-items-center">OR</h2>

                    <div class="row g-3 my-3">
                        <div class="col-md-6">
                            <input type="file" class="form-control" id="fileUpload">
                        </div>
                    </div>

                    <div class="row my-3">
                        <div class="col">
                            <button type="button" class="btn btn-secondary btn-not" ><i
                                    class="fas fa-long-arrow-alt-left mx-2"></i> Back</button>
                            <button type="button" class="btn btn-secondary btn-not" onclick="clearForm()">Clear <i
                                    class="fas fa-eraser"></i></button>
                            <button type="submit" class="btn btn-primary">Submit <i
                                    class="far fa-check-circle"></i></button>
                        </div>
                    </div>
                </form>
            </div>


        </div>
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