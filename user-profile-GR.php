<?php
require_once "session_manager.php";
require_once "db_connector.php";

$title = "Το Προφίλ Μου";
$email = $name = $department = "";
$userID = $_SESSION['userID'];

if ($_SESSION['role'] > 1) { //if logged in

    if ($_SESSION['role'] == 4 && isset($_GET['userID'])) { //if admin is managing a user's profile
        $userID = $_GET['userID'];

        $conn = connect2db();
        $stmt = $conn->prepare("SELECT * FROM teacher WHERE userID = ?");
        $stmt->bind_param("i", $userID);
        $stmt->execute();
        $teacherType = $stmt->get_result()->fetch_assoc();
        $conn->close();
    }

} else {
    $_SESSION['notification']['title'] = "Οουπς..";
    $_SESSION['notification']['message'] = "Πρέπει να είστε συνδεδεμένος για να δείτε αυτή τη σελίδα";
    header("Location: index-gr.php");
}

$userProfile = loadUserProfile($userID);
$name = $userProfile['name'];
$email = $userProfile['email'];
$department = $userProfile['department'];

function loadUserProfile($profileID)
{
    //any user viewing their profile
    $conn = connect2db();
    $stmt = $conn->prepare("SELECT name, email, department FROM user WHERE id = ?");
    $stmt->bind_param("i", $profileID);
    $stmt->execute();
    $userData = $stmt->get_result()->fetch_assoc();
    $conn->close();
    return $userData;
}

$navTitle = $title;
?>

<!DOCTYPE html>
<html lang="gr">

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
    <?php require_once "sidebar-gr.php"; ?>

    <!-- Page Content -->
    <div id="content">
        <?php require_once "navbar-gr.php"; ?>
        <?php require_once "modal.php"; ?>

        <div class="container px-4 mt-4">

            <div class="col-xl-8">
                <!-- Account details card-->
                <div class="card mb-4 " id="profile">
                    <div class="card-header"><strong>Λεπτομέρειες Προφίλ</strong></div>
                    <div class="card-body">
                        <form id="profileForm" action="manage_user_script.php" method="post">

                            <input class="form-control" id="userID" type="text" style="display: none"
                                   name="userID" value="<?php echo $userID; ?>" required>

                            <!-- Form Row-->
                            <div class="row gx-3 mb-3">
                                <!-- Form Group (first name)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="name">Ονοματεπώνυμο</label>
                                    <input class="form-control" id="name" type="text"
                                           placeholder="Enter your full name" name="name" value="<?php echo $name; ?>"
                                           required>
                                </div>
                                <!-- Form Group (last name)-->
                                <div class="col-md-6">
                                    <label class="small mb-1" for="email">Email</label>
                                    <input class="form-control" id="email" type="email"
                                           placeholder="Enter your email address" name="email"
                                           value="<?php echo $email; ?>" required>
                                </div>
                            </div>

                            <!-- Form Group (username)-->
                            <div class="mb-3">
                                <label class="small mb-1" for="dept">Όνομα Τμήματος</label>
                                <input class="form-control" id="dept" type="text"
                                       placeholder="Enter your department name" name="dept"
                                       value="<?php echo $department; ?>" required>
                            </div>

                            <?php if (isset($teacherType)) { ?>
                                <div class="mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="teacherType" value="dep"
                                               id="dep" <?php if ($teacherType['dep']) echo "checked "; ?>>
                                        <label class="form-check-label" for="dep">
                                            DEP
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="teacherType" value="edip"
                                               id="edip" <?php if ($teacherType['edip']) echo "checked "; ?>>
                                        <label class="form-check-label" for="edip">
                                            EDIP
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="teacherType" value="etep"
                                               id="etep" <?php if ($teacherType['etep']) echo "checked "; ?>>
                                        <label class="form-check-label" for="etep">
                                            ETEP
                                        </label>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="row gx-3 mb-3">
                                <div class="col-md-9">
                                    <button id="saveBtn" class="btn btn-primary" name="save" type="submit">
                                        <i class="fas fa-save me-2"></i> Αποθήκευση Αλλαγών
                                    </button>
                                </div>
                                <div class="col-md-3">
                                    <button id="deleteBtn" class="btn btn-danger"
                                            name="delete" type="submit">
                                        <i class="fas fa-user-times"></i> Διαγραφή Λογαριασμού
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>


<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

<script src='js/main.js'></script>
</body>

</html>