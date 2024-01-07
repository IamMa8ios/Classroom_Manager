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

            <form class="row g-3 needs-validation" novalidate>
                <!-- classID(classname), dropdown for Lectures, start date, end date, recurring event(checkbox), daysOfWeek(5x checkbox)  -->
                <!-- start time, duration -->

                <!--  classID, classname -->
                <!-- dropdown for Lectures, recurring event(checkbox) -->
                <!-- start date, end date -->
                <!-- daysOfWeek(5x checkbox), start time, duration  -->

                <div class="col-md-6">
                    <input style="display: none" name="classID" value="<?php echo $_SESSION['classID'] ?>">
                    <div class="input-group has-validation">
                        <span class="input-group-text" id="validationTooltipUsernamePrepend">Classroom</span>
                        <input type="text" class="form-control" id="validationTooltipUsername"
                            value="<?php echo $_SESSION['classname'] ?>"
                            aria-describedby="validationTooltipUsernamePrepend" required disabled>
                        <div class="invalid-tooltip">
                            Please choose a unique and valid username.
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group has-validation">
                        <span class="input-group-text" id="validationTooltipUsernamePrepend">Lectures</span>
                        <select class="form-select" id="validationCustom04" required>
                            <option selected disabled value="">Choose...</option>
                            <option>...</option>
                        </select>
                    </div>

                </div>

                <div class="col-md-6">
                    <div class="input-group has-validation">
                        <span class="input-group-text" id="validationTooltipUsernamePrepend">Start Date</span>
                        <input type="text" class="form-control" id="validationTooltipUsername"
                            value="<?php echo $_GET['date'] ?>" aria-describedby="validationTooltipUsernamePrepend"
                            required disabled>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group has-validation">
                        <span class="input-group-text" id="validationTooltipUsernamePrepend">End Date</span>
                        <input type="date" class="form-control" id="validationTooltipUsername" value=""
                            aria-describedby="validationTooltipUsernamePrepend" required>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group has-validation">
                        <span class="input-group-text" id="validationTooltipUsernamePrepend">Start Time</span>
                        <input type="time" class="form-control" name="" value="10:05 AM" />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="input-group has-validation">
                        <span class="input-group-text" id="validationTooltipUsernamePrepend">Duration</span>
                        <input step=".5" value="0" type="number" min="1" max="3" class="form-control" />

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                        <label class="form-check-label" for="defaultCheck1">
                            Monday
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="defaultCheck2">
                        <label class="form-check-label" for="defaultCheck2">
                            Tuesday
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="defaultCheck3">
                        <label class="form-check-label" for="defaultCheck3">
                            Wednesday
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="defaultCheck4">
                        <label class="form-check-label" for="defaultCheck4">
                            Thursday
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="" id="defaultCheck5">
                        <label class="form-check-label" for="defaultCheck5">
                            Friday
                        </label>
                    </div>
                </div>



                <div class="col-md-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" role="switch" id="flexSwitchCheckDefault">
                        <label class="form-check-label" for="flexSwitchCheckDefault">Recurring Event
                            input</label>
                    </div>
                </div>
                <div class="col-12">
                    <button class="btn btn-primary" type="submit">Submit form</button>
                </div>
            </form>

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