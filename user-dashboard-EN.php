<?php
require_once "session_manager.php";
require_once "db_connector.php";

if($_SESSION['role']==1){
    header("Location: index-EN.php");
}

$title = "My Dashboard";
$navTitle = $title;

if($_SESSION['role']!=2) {

    $conn = connect2db();
    $stmt = $conn->prepare("select id, name, email, department, roleID from user");
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result

    $users = array();
    while ($user = $result->fetch_assoc()) {
        $users[] = $user;
    }
    $conn->close();

    $conn = connect2db();
    $stmt = $conn->prepare("select id, name from role");
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result

    $roles = array();
    while ($role = $result->fetch_assoc()) {
        $roles[] = $role;
    }
    $conn->close();
}

$conn = connect2db();
$sql = "select id, status, request_start, date_lost, date_recouped, classroomID, start_time, duration, notes from recoupment_requests";
if ($_SESSION['role'] == 2) {
    $sql = $sql . " where userID=?";
}
$stmt = $conn->prepare($sql);
if ($_SESSION['role'] == 2) {
    $stmt->bind_param("i", $_SESSION['userID']);
}
$stmt->execute();
$result = $stmt->get_result(); // get the mysqli result

$recoupments = array();
while ($recoupment = $result->fetch_assoc()) {
    $recoupments[] = $recoupment;
}
$conn->close();

$recoupmentStatus[] = "Submitted";
$recoupmentStatus[] = "Approved";
$recoupmentStatus[] = "Denied";

//FIXME: Customize table action buttons
//FIXME: Dynamic modal for deny button + notes
//FIXME: Recoupments for each teacher
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0-alpha3/css/bootstrap.min.css">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"/>

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/dataTables.bootstrap5.min.css">

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div id="wrapper">

    <!-- Sidebar -->
    <?php require_once "sidebar-EN.php" ?>

    <!-- Page Content -->
    <div id="content">
        <?php require_once "navbar-EN.php" ?>

        <?php if($_SESSION['role']==4){ ?>
        <div class="container-fluid mt-5 p-3 bg-purple-svg rounded-4">

            <h2 class="my-3">Users</h2>

            <table id="dataTable" class="table table-striped" style="width:100%">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($users as $user) { ?>
                    <tr>
                        <td><?php echo $user['name']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['department']; ?></td>
                        <td><?php foreach ($roles as $role){
                            if($role['id']==$user['roleID']){
                                echo $role['name'];
                                break;
                            }
                            } ?></td>
                        <td>
                            <a href="manage-user-EN.php?userID=<?php echo $user['id']; ?>&action=1">Edit</a>
                            <a href="manage-user-EN.php?userID=<?php echo $user['id']; ?>&action=2">Delete</a>
                        </td>
                    </tr>
                <?php } ?>

                </tbody>
                <tfoot>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
                </tfoot>
            </table>

        </div>
        <?php } ?>
        <div class="container-fluid mt-5 p-3 bg-purple-svg rounded-4">

            <h2 class="my-3">Recoupment Requests</h2>

            <table id="recoupmentDataTable" class="table table-striped" style="width:100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Status</th>
                    <th>Starting Lecture Date</th>
                    <th>Date Lost</th>
                    <th>Recoupment Date</th>
                    <th>Classroom</th>
                    <th>Start Time</th>
                    <th>Duration</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($recoupments as $recoupment) { ?>
                    <tr>
                        <td><?php echo $recoupment['id']; ?></td>
                        <td><?php echo $recoupmentStatus[$recoupment['status']]; ?></td>
                        <td><?php echo $recoupment['request_start']; ?></td>
                        <td><?php echo $recoupment['date_lost']; ?></td>
                        <td><?php echo $recoupment['date_recouped']; ?></td>
                        <td><?php foreach ($allClasses as $class){
                            if($class['id']==$recoupment['classroomID']){
                                echo $class['name'];
                                break;
                            }
                        } ?></td>
                        <td><?php echo $recoupment['start_time']; ?></td>
                        <td><?php echo $recoupment['duration']; ?> hour(s)</td>
                        <td><?php echo $recoupment['notes']; ?></td>
                        <td>
                            <a href="manage-recoupment-EN.php?userID=<?php echo $recoupment['id']; ?>&action=1">Approve</a>
                            <a href="manage-recoupment-EN.php?userID=<?php echo $recoupment['id']; ?>&action=2">Deny</a>
                        </td>
                    </tr>
                <?php } ?>

                </tbody>
                <tfoot>
                <tr>
                    <th>ID</th>
                    <th>Status</th>
                    <th>Starting Lecture Date</th>
                    <th>Date Lost</th>
                    <th>Recoupment Date</th>
                    <th>Classroom</th>
                    <th>Start Time</th>
                    <th>Duration</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
                </tfoot>
            </table>

        </div>

    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
<script>new DataTable('#dataTable');</script>
<script>new DataTable('#recoupmentDataTable');</script>
<script src='js/main.js'></script>
</body>

</html>