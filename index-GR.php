<?php
require_once "session_manager.php";
require_once "db_connector.php";

$navTitle = "";

if (isset($_POST['classroom']) || isset($_SESSION['classroom'])) {
    $_SESSION['classID'] = $_POST['classroom'];
    $conn = connect2db();

    $stmt = $conn->prepare("select name from classroom where id=?");
    $stmt->bind_param("i", $_POST['classroom']);
    $stmt->execute();
    $data = $stmt->get_result(); // get the mysqli result
    $className = $data->fetch_assoc();

    $_SESSION['className'] = $className['name'];
    $conn->close();

    $navTitle = 'Προεπισκόπηση Αίθουσας: ' . $_SESSION['className'];
} else {
    $navTitle = "Επιλέξτε μια Αίθουσα";
}

//$_SESSION['notification'] = 'createSuccessAlert()';
?>

<!DOCTYPE html>
<html lang="gr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- FullCalendar CSS -->
    <link href='fullcalendar/packages/core/main.css' rel='stylesheet'/>
    <link href='fullcalendar/packages/daygrid/main.css' rel='stylesheet'/>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>

    <!-- Include SweetAlert 2 from CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <link rel="stylesheet" href="css/style.css">
    <style>
        /*.fc-prev-button,*/
        /*.fc-next-button,*/
        /*.fc-today-button,*/
        /*.fc-button-group .fc-button,*/
        /*.fc-today-button,*/
        /*.fc-button-group .fc-button-active{*/
        /*    background: var(--dark-purple); !* Change this to the desired color *!*/
        /*    color: var(--white-ish); !* Change this to the desired text color *!*/
        /*    box-shadow: none;*/
        /*    border: none;*/
        /*}*/
        /*.fc-button-group .fc-button:hover,*/
        /*.fc-button-group .fc-button-active:hover,*/
        /*.fc-today-button:hover{*/
        /*    background: var(--dark-purple);*/
        /*    box-shadow: inset 0 0 .5em var(--white-ish);*/
        /*}*/
        .fc-prev-button,
        .fc-next-button,
        .fc-today-button,
        .fc-button-group .fc-button {
            background-color: var(--dark-purple); /* Change this to the desired color */
            color: var(--white-ish); /* Change this to the desired text color */
        }

        .fc-button.fc-button-active,
        .fc-button.fc-button-active:active,
        .fc-button.fc-button-active:focus {
            background-color: var(--dark-purple); /* Change this to the desired active color */
            color: var(--white-ish); /* Change this to the desired active text color */
        }


    </style>
</head>

<body>

<div id="wrapper">

    <!-- Sidebar -->
    <?php require_once "sidebar-gr.php"; ?>

    <!-- Page Content -->
    <div id="content">
        <?php require_once "navbar-gr.php"; ?>
        <?php require_once "modal.php"; ?>

        <?php if (isset($_POST['classroom'])) { ?>
            <!-- FullCalendar -->
            <div id="calendar" class="bg-light"></div>
        <?php } else {
            echo "Παρακαλώ Επιλέξτε μια Αίθουσα από το μενού αριστερά";
        } ?>
    </div>
    <input class="visually-hidden" id="hidden-userID" value="*test id*">
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

<script>
    document.addEventListener('DOMContentLoaded', function () {

        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            plugins: ['interaction', 'dayGrid', 'timeGrid', 'list'],
            height: "parent",
            disableDragging: true,
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            },
            defaultView: 'dayGridMonth',
            defaultDate: '2023-12-12',
            navLinks: true, // can click day/week names to navigate views
            editable: false,
            eventLimit: true, // allow "more" link when too many events
            events: {
                url: 'reservation_loader_script.php?classroom=<?php echo $_POST["classroom"]; ?>',
                dataType: 'json',
                type: "POST",
                color: "#1f2029"
            },
            <?php if($_SESSION['role'] != 1){ ?>
            dateClick: function (info) {
                location.href = `create_event-gr.php?date=${info.dateStr}`;
            },
            <?php } ?>
            <?php if($_SESSION['role'] > 2){ ?>
            eventRender: function (info) {
                // Customize the event rendering here
                var $eventElement = info.el;

                // Add a custom button with an icon
                var $editEventBtn = document.createElement('a');
                // $editEventBtn.classList.add('btn');
                $editEventBtn.style.color = 'var(--white-ish)';
                $editEventBtn.innerHTML = '<i class="fas fa-edit"></i>'; // Example icon using Font Awesome

                // Add an event listener to the custom button
                $editEventBtn.addEventListener('click', function () {
                    // Create a form element
                    var form = document.createElement('form');
                    form.method = 'post';
                    form.action = 'edit-event-gr.php';

                    // Create input fields and add them to the form
                    var userID = document.querySelector('#hidden-userID');
                    var eventID = document.createElement('input');
                    eventID.type = 'hidden';
                    eventID.name = 'eventID';
                    eventID.value = userID.value;
                    form.appendChild(eventID);
                    document.body.appendChild(form);

                    form.submit();
                });

                // Append the custom button to the event element
                $eventElement.appendChild($editEventBtn);
            }
            <?php } ?>
        });

        calendar.render();
    });
</script>

<script src='js/main.js'></script>

</body>

</html>