<?php
require_once "session_manager.php";
require_once "db_connector.php";

$title = "";

if ($_SESSION['role'] == 2) {
    $title="My profile";
}elseif($_SESSION['role'] == 3) {
    if(isset($_GET['userID']) && isset($_GET['action'])){
        $title="Managing user profile";

        if($_GET['action']==1){

        }elseif ($_GET['action']==2){

        }else{
            header("Location: index-EN.php");
        }
    }else{
        $title="My profile";
    }
}else{
    header("Location: index-EN.php");
}
$navTitle = $title;
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

        <div class="container mt-5 p-3 bg-purple-svg" id="create-event-container">

        </div>
    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<script src='js/main.js'></script>
</body>

</html>