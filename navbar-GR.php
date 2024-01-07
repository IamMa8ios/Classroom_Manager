<?php
if (isset($_POST['logout'])) {
    session_start();
    session_destroy();
    header('Location: index.php');
}

require_once "db_connector.php";

if (isset($_POST['classroom'])) {
    $conn = connect2db();

    $stmt = $conn->prepare("select name from classroom where id=?");
    $stmt->bind_param("i", $_POST['classroom']);
    $stmt->execute();
    $data = $stmt->get_result(); // get the mysqli result
    $className = $data->fetch_assoc();
}


?>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <?php if (isset($_POST['classroom'])) { ?>
                <h2>Βλέπετε την αίθουσα: 
                    <?php echo $className['name']; ?>
                </h2>
            <?php } else {
                echo "<h2> Παρακαλώ επιλέξτε μια αίθουσα </h2>";
            } ?>
            <ul class="navbar-nav ms-auto d-flex justify-content-between align-items-baseline gap-3">
                <li class="nav-item">
                    <button class="btn btn-primary timerBtn" onclick="location.reload()"></button>
                </li>
                <li class="nav-item">
                    <div class="nav-item dropdown"> <!-- language dropdown menu -->
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">Γλώσσα
                            <img src="img/greece_32px.png" class="img-responsive inline-block"></img>
                        </button>

                        <!-- // FIXME: Logout btn show in Login/Register btn's place after login... -->
                        <ul class="dropdown-menu">
                            <a class="dropdown-item" href="index-EN.php">
                                <img src="img/united-states_64px.png" class="img-responsive inline-block"></img>
                            </a>
                            <a class="dropdown-item" href="index-GR.php">
                                <img src="img/greece_64px.png" class="img-responsive inline-block"></img>
                            </a>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <?php if ($_SESSION['role'] == 1) { ?>
                        <a href="authenticate.php" class="btn btn-secondary">Σύνδεση/Εγγραφή</a>
                    <?php } else { ?>
                        <form action="navbar-GR.php" method="POST">
                            <button name="logout" value="logout" class="btn btn-secondary">Αποσύνδεση</button>
                        </form>
                    <?php } ?>
                </li>
            </ul>
        </div>
    </div>
</nav>
