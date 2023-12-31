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
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3>Sidebar</h3>
        </div>

        <ul class="list-unstyled components">
            <li class="active">
                <a href="#homeSubmenu" data-bs-toggle="collapse" aria-expanded="false" class="dropdown-toggle">Home</a>
                <ul class="collapse list-unstyled" id="homeSubmenu">
                    <li><a href="#">Submenu 1</a></li>
                    <li><a href="#">Submenu 2</a></li>
                    <li><a href="#">Submenu 3</a></li>
                </ul>
            </li>
            <li>
                <a href="#">Page 1</a>
            </li>
            <li>
                <a href="#">Page 2</a>
            </li>
        </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container-fluid">
                <button type="button" id="sidebarCollapse" class="btn btn-dark">
                    <i class="fas fa-align-left"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <span id="timer">Timer: 10:00</span>
                        </li>
                        <li class="nav-item">
                            <button class="btn btn-primary">Change Language</button>
                        </li>
                        <li class="nav-item">
                            <button class="btn btn-secondary">Login/Register</button>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- FullCalendar -->
        <div id="calendar"></div>
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
    // Toggle sidebar
    $("#sidebarCollapse").on("click", function () {
        $("#sidebar").toggleClass("active");
    });

    // Timer
    function startTimer() {
        var duration = 10 * 60; // 10 minutes
        var timer = duration, minutes, seconds;
        setInterval(function () {
            minutes = parseInt(timer / 60, 10);
            seconds = parseInt(timer % 60, 10);

            minutes = minutes < 10 ? "0" + minutes : minutes;
            seconds = seconds < 10 ? "0" + seconds : seconds;

            document.getElementById("timer").textContent = "Timer: " + minutes + ":" + seconds;

            if (--timer < 0) {
                // Logout or perform necessary action when timer ends
                alert("Session timed out. Logging out...");
                // Add logout logic here
            }
        }, 1000);
    }

</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            plugins: [ 'interaction', 'dayGrid', 'timeGrid', 'list' ],
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
                url: 'reservation_loader_script.php',
                dataType: 'json',
                type: "POST"
            }
        });

        calendar.render();
    });

</script>
</body>
</html>
