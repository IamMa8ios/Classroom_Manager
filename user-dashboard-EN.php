<?php
require_once "session_manager.php";
require_once "db_connector.php";

if ($_SESSION['role'] == 1) { //αρχικά πρέπει να έχει συνδεθεί χρήστης για να δει το προφίλ του
    header("Location: index-EN.php");
}

$title = "My Dashboard";
$navTitle = $title;

if ($_SESSION['role'] == 4) { //εάν πρόκειται για διαχειριστή χρηστών

    //φέρνουμε τα στοιχεία όλων των χρηστών
    $conn = connect2db();
    $stmt = $conn->prepare("select id, name, email, department, roleID from user");
    $stmt->execute();
    $result = $stmt->get_result();

    //και τα κρατάμε σε έναν πίνακα
    $users = array();
    while ($user = $result->fetch_assoc()) {
        $users[] = $user;
    }
    $conn->close();

    //στη συνέχεια φέρνουμε τους ρόλους που μπορεί να έχουν οι χρήστες
    $conn = connect2db();
    $stmt = $conn->prepare("select id, name from role");
    $stmt->execute();
    $result = $stmt->get_result(); // get the mysqli result

    //και τους κρατάμε σε νέο πίνακα
    $roles = array();
    while ($role = $result->fetch_assoc()) {
        $roles[] = $role;
    }
    $conn->close();

}else{ //εάν πρόκειται για διαχειριστή κρατήσεων ή καθηγητή

    //φέρνουμε όλα τα αιτήματα αναπλήρωσης
    $conn = connect2db();
    $sql = "select id, status, request_start, date_lost, date_recouped, classroomID, start_time, duration, notes from recoupment_requests";

    //και στην περίπτωση του καθηγητή περιοριζόμαστε μόνο στα δικά του αιτήματα
    if ($_SESSION['role'] == 2) {
        $sql = $sql . " where userID=?";
    }

    $stmt = $conn->prepare($sql);

    if ($_SESSION['role'] == 2) {
        $stmt->bind_param("i", $_SESSION['userID']);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    //μετά κρατάμε και τα αιτήματα σε νέο πίνακα
    $recoupments = array();
    while ($recoupment = $result->fetch_assoc()) {
        $recoupments[] = $recoupment;
    }
    $conn->close();

    //Η σημασία που έχει η τιμή της μεταβλητής status στη βάση δεδομένων
    $recoupmentStatus[] = "Pending";
    $recoupmentStatus[] = "Approved";
    $recoupmentStatus[] = "Denied";

}


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
    <?php require_once "sidebar-EN.php"; ?>

    <!-- Page Content -->
    <div id="content">
        <?php require_once "navbar-EN.php"; ?>
        <?php require_once "modal.php"; ?>

        <?php
        //για το διαχειριστή χρηστών εμφανίζουμε έναν πίνακα με όλους τους χρήστες, απ' όπου θα
        //μπορεί να επιλέξει ποιον θέλει να επεξεργαστεί
        if ($_SESSION['role'] == 4) { ?>
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
                                   href="user-profile-EN.php?userID=<?php echo $user['id']; ?>"><i
                                            class="fas fa-edit me-2"></i>Edit</a>
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
        <?php }
        //αλλιώς εμφανίζουμε τα αιτήματα των αναπληρώσεων και στο διαχειριστή δίνουμε επιπλέον
        // τη δυνατότητα να τα δεχτεί ή να τα απορρίψει
        else { ?>
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
                                        onclick="transferViewData(this)"
                                        data-viewNotes="<?php echo $recoupment['notes']; ?>"
                                        data-bs-target=".view-notes-modal"><i class="far fa-eye me-2"></i>View
                                </button>
                            </td>

                            <?php if ($_SESSION['role'] == 3) { ?>
                                <td>
                                    <form action="manage_recoupment_script.php" method="post">
                                        <button class="btn rounded-4" id="edit-a" name="approve"
                                                value="<?php echo $recoupment['id']; ?>"><i
                                                    class="fas fa-check-circle me-2"></i>Approve
                                        </button>
                                    </form>
                                    <a class="btn rounded-4" id="delete-a" data-bs-toggle="modal"
                                       onclick="transferRecoupmentID(this)"
                                       data-bs-target=".denyBtn-modal"
                                       data-recoupmentID="<?php echo $recoupment['id']; ?>">
                                        <i class="fas fa-trash-alt me-2"></i>Deny</a>
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
        <?php } ?>
    </div>

</div>

<!-- denyBtn Modal -->
<div class="modal fade denyBtn-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <form action="manage_recoupment_script.php" method="post">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="staticBackdropLabel">Reason for Denying the Request</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-outline">
                        <textarea name="notes" class="form-control" id="textAreaExample2" rows="8"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal"><i class="far fa-times-circle"></i>Close
                    </button>
                    <button type="submit" name="deny" id="deny" class="btn"><i class="far fa-check-circle"></i>Submit
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- View Notes Modal -->
<div class="modal fade view-notes-modal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
     aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Notes</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-outline">
                    <input type="text" class="form-control" id="notesArea" readonly>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn" data-bs-dismiss="modal"><i class="far fa-times-circle me-2"></i>Close
                </button>
            </div>
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
<script>
    function transferRecoupmentID(btnClicked) { // θέτει το value του #deny να είναι το attribute 'data-recoupmentID' για να
        const button = document.querySelector('#deny') // γίνει άρνηση της σωστής αναπλήρωσης
        const getAttrData = btnClicked.getAttribute('data-recoupmentID')

        button.setAttribute('value', getAttrData)
    }
</script>
<script>
    function transferViewData(btnClicked) { // γεμίζει το #notesArea με το notes της DB
        const notesArea = document.querySelector('#notesArea')
        notesArea.value=btnClicked.getAttribute('data-viewNotes')
    }
</script>
<script src='js/main.js'></script>

</body>

</html>