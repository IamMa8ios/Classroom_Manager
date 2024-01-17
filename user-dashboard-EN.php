<?php
require_once "session_manager.php";
require_once "db_connector.php";

if ($_SESSION['role'] == 1) {
    header("Location: index-EN.php");
}

$title = "My Dashboard";
$navTitle = $title;

if ($_SESSION['role'] != 2) {

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

//FIXME: Change status color accordingly
$recoupmentStatus[] = "Pending";
$recoupmentStatus[] = "Approved";
$recoupmentStatus[] = "Denied";

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

        <?php if ($_SESSION['role'] == 4) { ?>
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
                            <td><?php foreach ($roles as $role) {
                                    if ($role['id'] == $user['roleID']) {
                                        echo $role['name'];
                                        break;
                                    }
                                } ?></td>
                            <td>
                                <a class="btn rounded-4" id="edit-a"
                                   href="user-profile-EN.php?userID=<?php echo $user['id']; ?>&action=1"><i
                                            class="fas fa-edit me-2"></i>Edit</a>
                                <a class="btn rounded-4" id="delete-a"
                                   href="user-profile-EN.php?userID=<?php echo $user['id']; ?>&action=2"><i
                                            class="fas fa-trash-alt me-2"></i>Delete</a>

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
                    <?php if($_SESSION['role']==3){ ?>
                    <th>Actions</th>
                    <?php } ?>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($recoupments as $recoupment) { ?>
                    <tr>
                        <td><?php echo $recoupment['id']; ?></td>
                        <td class="<?php echo $recoupmentStatus[$recoupment['status']]; ?>"><?php echo $recoupmentStatus[$recoupment['status']]; ?></td>
                        <td><?php echo $recoupment['request_start']; ?></td>
                        <td><?php echo $recoupment['date_lost']; ?></td>
                        <td><?php echo $recoupment['date_recouped']; ?></td>
                        <td><?php foreach ($allClasses as $class) {
                                if ($class['id'] == $recoupment['classroomID']) {
                                    echo $class['name'];
                                    break;
                                }
                            } ?></td>
                        <td><?php echo $recoupment['start_time']; ?></td>
                        <td><?php echo $recoupment['duration']; ?> hour(s)</td>
                        <td>
                            <button id="edit-a" class="btn rounded-4" data-bs-toggle="modal"
                                    data-bs-target=".view-notes-modal"><i class="far fa-eye me-2"></i>View
                            </button>
                        </td>

                        <?php if($_SESSION['role']==3){ ?>
                        <td>
                            <a class="btn rounded-4" id="edit-a"
                               href="manage-recoupment-EN.php?userID=<?php echo $recoupment['id']; ?>&action=1"><i
                                        class="fas fa-check-circle me-2"></i>Approve</a>
                            <a class="btn rounded-4" id="delete-a" data-bs-toggle="modal"
                               data-bs-target=".denyBtn-modal"><i
                                        class="fas fa-trash-alt me-2"></i>Deny</a>
                        </td>

                        <?php } ?>
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
                    <?php if($_SESSION['role']==3){ ?>
                        <th>Actions</th>
                    <?php } ?>
                </tr>
                </tfoot>
            </table>

        </div>

    </div>

</div>

<!-- denyBtn Modal -->
<div class="modal fade denyBtn-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <form action="manage-recoupment-EN.php?userID=<?php echo $recoupment['id']; ?>&action=2" method="get">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Reason for Denying the Request</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Textarea 8 rows height -->
                    <div class="form-outline">
                        <textarea class="form-control" id="textAreaExample2" rows="8"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal"><i class="far fa-times-circle"></i>Close
                    </button>
                    <button type="submit" class="btn"><i class="far fa-check-circle"></i>Submit</button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- View Notes Modal -->
<div class="modal fade view-notes-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <form action="manage-recoupment-EN.php?userID=<?php echo $recoupment['id']; ?>&action=2" method="get">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Notes</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Textarea 8 rows height -->
                    <div class="form-outline">
                        <!-- FIXME: perfect $recoupment['notes'] -->
                        <textarea class="form-control" id="textAreaExample2" readonly
                                  rows="8"><?php echo $recoupment['notes'] ?></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal"><i class="far fa-times-circle me-2"></i>Close
                    </button>
                </div>
            </div>
        </div>
    </form>
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