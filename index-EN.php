<?php
require_once "session_manager.php";
require_once "db_connector.php";

$navTitle = "";

//Επειδή κάθε αίθουσα έχει το δικό της πρόγραμμα, θα πρέπει να δοθεί μια αίθουσα ώστε να εμφανιστεί το πρόγραμμά της
//Οι διαθέσιμες αίθουσες εμφανίζονται στο sidebar
if (isset($_POST['classroom']) || isset($_SESSION['classroom'])) {
    $_SESSION['classID'] = $_POST['classroom'];

    //φέρνουμε τα δεδομένα για τη συγκεκριμένη αίθουσα
    $conn = connect2db();
    $stmt = $conn->prepare("select name from classroom where id=?");
    $stmt->bind_param("i", $_POST['classroom']);
    $stmt->execute();
    $data = $stmt->get_result(); // get the mysqli result
    $className = $data->fetch_assoc();

    $_SESSION['className'] = $className['name'];
    $conn->close();

    $navTitle = 'Viewing ' . $_SESSION['className'];
} else {
    $navTitle = "Please Select a Classroom";
}

//Το ημερολόγιο στο οποίο εμφανίζεται το πρόγραμμα της αίθουσας δημιουργείται με τη βοήθεια της βιβλιοθήκης fullCalendar
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
    <?php require_once "sidebar-EN.php" ?>

    <!-- Page Content -->
    <div id="content">
        <?php require_once "navbar-EN.php" ?>
        <?php require_once "modal.php" ?>

        <?php if (isset($_POST['classroom'])) { ?>
            <!-- FullCalendar -->
            <div id="calendar" class="bg-light"></div>
        <?php } else {
            echo "Please select a classroom from the menu on the left";
        } ?>
    </div>
    <input class="visually-hidden" id="hidden-userID" value="">
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
    document.addEventListener('DOMContentLoaded', function () { // init js FullCalendar

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
            navLinks: true,
            editable: false,
            eventLimit: true, //Σε περίπτωση που υπάρχουν πολλές κρατήσεις σε μια ημέρα, να εμφανίζονται σε ξεχωριστά
            //ορίζουμε το αρχείο από το οποίο θα αντλεί τα δεδομένα για εμφάνιση
            events: {
                url: 'reservation_loader_script.php?classroom=<?php echo $_POST["classroom"]; // φέρνει τα events από DB?>',
                dataType: 'json',
                type: "POST",
                color: "#1f2029"
            },
            <?php
            //εάν πρόκειται για εγγεγραμένο χρήστη, με την επιλογή μιας ημέρας του ημερολογίου δύναται η
            // δημιουργία μιας κράτησης ή αναπλήρωσης
            if($_SESSION['role'] != 1){ ?>
            dateClick: function (info) { // κάθε κελί όταν κλικαριστεί --> create event
                location.href = `create_event-EN.php?date=${info.dateStr}`;
            },
            <?php } ?>
            <?php if($_SESSION['role'] > 2){ ?>
            eventRender: function (info) {
                var $eventElement = info.el;

                var $editEventBtn = document.createElement('a');
                $editEventBtn.style.color = 'var(--white-ish)';
                $editEventBtn.innerHTML = '<i class="fas fa-edit"></i>';

                // σε κάθε event δημιουργείτε ενα κουμπί που όταν γίνει κλικ
                // γίνεται post στο edit-event-EN.php για Edit
                $editEventBtn.addEventListener('click', function () {
                    // Δημιουργείται μια φόρμα στην οποία εισάγουμε ένα κρυφό input με value το userID να γα γίνει post
                    var form = document.createElement('form');
                    form.method = 'post';
                    form.action = 'edit-event-EN.php';

                    var userID = document.querySelector('#hidden-userID');
                    var eventID = document.createElement('input');
                    eventID.type = 'hidden';
                    eventID.name = 'eventID';
                    eventID.value = userID.value;
                    form.appendChild(eventID);
                    document.body.appendChild(form);

                    form.submit();
                });

                // βάζουμε το κουμπί στο event
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