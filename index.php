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
        <?php require_once "sidebar.php" ?>

        <!-- Page Content -->
        <div id="content">
            <?php require_once "navbar.php" ?>

            <?php if (isset($_POST['classroom'])) { ?>
                <!-- FullCalendar -->
                <div id="calendar" class="bg-light"></div>
            <?php } else {
                echo "Please select a classroom from the menu on the left";
            } ?>

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


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                plugins: ['interaction', 'dayGrid', 'timeGrid', 'list'],
                height: "parent",
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                defaultView: 'dayGridMonth',
                defaultDate: '2023-12-12',
                navLinks: true, // can click day/week names to navigate views
                editable: true,
                eventLimit: true, // allow "more" link when too many events
                events: {
                    url: 'reservation_loader_script.php?classroom=<?php echo $_POST["classroom"]; ?>',
                    dataType: 'json',
                    type: "POST"
                },
                dateClick: function(info) {
                    location.href = `create_event.php?date=${info.dateStr}`;
                }
            });

            calendar.render();
        });
    </script>

    <script src='js/main.js'></script>
</body>

</html>