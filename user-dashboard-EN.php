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
                    <?php if ($_SESSION['role'] == 3) { ?>
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

                        <?php if ($_SESSION['role'] == 3) { ?>
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
                    <?php if ($_SESSION['role'] == 3) { ?>
                        <th>Actions</th>
                    <?php } ?>
                </tr>
                </tfoot>
            </table>

        </div>

        <div class="container-fluid mt-5 p-3 bg-purple-svg rounded-4">
            <div class="container-xl px-4 mt-4">
                <!-- Account page navigation-->
                <nav class="nav nav-borders">
                    <a class="nav-link active ms-0"
                       href="https://www.bootdey.com/snippets/view/bs5-edit-profile-account-details" target="__blank">Profile</a>
                    <a class="nav-link" href="https://www.bootdey.com/snippets/view/bs5-profile-billing-page"
                       target="__blank">Billing</a>
                    <a class="nav-link" href="https://www.bootdey.com/snippets/view/bs5-profile-security-page"
                       target="__blank">Security</a>
                    <a class="nav-link" href="https://www.bootdey.com/snippets/view/bs5-edit-notifications-page"
                       target="__blank">Notifications</a>
                </nav>
                <hr class="mt-0 mb-4">
                <div class="row">
                    <div class="col-xl-4">
                        <!-- Profile picture card-->
                        <div class="card mb-4 mb-xl-0">
                            <div class="card-header">Profile Picture</div>
                            <div class="card-body text-center">
                                <!-- Profile picture image-->
                                <img class="img-account-profile rounded-circle mb-2"
                                     src="http://bootdey.com/img/Content/avatar/avatar1.png" alt="">
                                <!-- Profile picture help block-->
                                <div class="small font-italic text-muted mb-4">JPG or PNG no larger than 5 MB</div>
                                <!-- Profile picture upload button-->
                                <button class="btn btn-primary" type="button">Upload new image</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-8">
                        <!-- Account details card-->
                        <div class="card mb-4" id="profile">
                            <div class="card-header"><strong>Profile Details</strong></div>
                            <div class="card-body">
                                <form>
                                    <!-- Form Group (username)-->
                                    <div class="mb-3">
                                        <label class="small mb-1" for="inputUsername">Username (how your name will
                                            appear to other users on the site)</label>
                                        <input class="form-control" id="inputUsername" type="text"
                                               placeholder="Enter your username" value="username">
                                    </div>
                                    <!-- Form Row-->
                                    <div class="row gx-3 mb-3">
                                        <!-- Form Group (first name)-->
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="inputFirstName">First name</label>
                                            <input class="form-control" id="inputFirstName" type="text"
                                                   placeholder="Enter your first name" value="Valerie">
                                        </div>
                                        <!-- Form Group (last name)-->
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="inputLastName">Last name</label>
                                            <input class="form-control" id="inputLastName" type="text"
                                                   placeholder="Enter your last name" value="Luna">
                                        </div>
                                    </div>
                                    <!-- Form Row        -->
                                    <div class="row gx-3 mb-3">
                                        <!-- Form Group (organization name)-->
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="inputOrgName">Organization name</label>
                                            <input class="form-control" id="inputOrgName" type="text"
                                                   placeholder="Enter your organization name" value="Start Bootstrap">
                                        </div>
                                        <!-- Form Group (location)-->
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="inputLocation">Location</label>
                                            <input class="form-control" id="inputLocation" type="text"
                                                   placeholder="Enter your location" value="San Francisco, CA">
                                        </div>
                                    </div>
                                    <!-- Form Group (email address)-->
                                    <div class="mb-3">
                                        <label class="small mb-1" for="inputEmailAddress">Email address</label>
                                        <input class="form-control" id="inputEmailAddress" type="email"
                                               placeholder="Enter your email address" value="name@example.com">
                                    </div>
                                    <!-- Form Row-->
                                    <div class="row gx-3 mb-3">
                                        <!-- Form Group (phone number)-->
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="inputPhone">Phone number</label>
                                            <input class="form-control" id="inputPhone" type="tel"
                                                   placeholder="Enter your phone number" value="555-123-4567">
                                        </div>
                                        <!-- Form Group (birthday)-->
                                        <div class="col-md-6">
                                            <label class="small mb-1" for="inputBirthday">Birthday</label>
                                            <input class="form-control" id="inputBirthday" type="text" name="birthday"
                                                   placeholder="Enter your birthday" value="06/10/1988">
                                        </div>
                                    </div>
                                    <!-- Save changes button-->
                                    <button class="btn btn-primary" type="button">Save Changes</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
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
                    <a type="submit" class="btn"
                       href="manage-recoupment-EN.php?userID=<?php echo $recoupment['id']; ?>&action=1"><i
                                class="far fa-check-circle"></i>Submit</a>
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