<?php
if (isset($_POST['logout'])) {
    session_start();
    session_destroy();
    header('Location: index.php');
}

require_once "db_connector.php";

if (isset($_POST['classroom'])) {
    $_SESSION['classID'] = $_POST['classroom'];
    $conn = connect2db();

    $stmt = $conn->prepare("select name from classroom where id=?");
    $stmt->bind_param("i", $_POST['classroom']);
    $stmt->execute();
    $data = $stmt->get_result(); // get the mysqli result
    $className = $data->fetch_assoc();
    
    $_SESSION['className'] = $className['name'];
    $conn->close();
}

?>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <?php if (isset($_POST['classroom'])) { ?>
                <h2>Viewing
                    <?php echo $className['name']; ?>
                </h2>
            <?php } else {
                echo "<h2> Please Select a Classroom </h2>";
            } ?>
            <ul class="navbar-nav ms-auto d-flex justify-content-between align-items-baseline gap-3">
                <li class="nav-item">
                    <button class="btn-slide timerBtn" onclick="location.reload()"></button>
                </li>
                <li class="nav-item">
                    <div class="nav-item dropdown"> <!-- language dropdown menu -->
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">Language
                            <img src="img/united-states_32px.png" class="img-responsive inline-block"></img>
                        </button>

                        <ul class="dropdown-menu">
                            <a class="dropdown-item" href="index-EN.php">
                                <img src="img/united-states_64px.png" class="img-responsive inline-block"></img>
                                EN-US
                            </a>
                            <a class="dropdown-item" href="index-GR.php">
                                <img src="img/greece_64px.png" class="img-responsive inline-block"></img>
                                GR
                            </a>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <?php if ($_SESSION['role'] == 1) { ?>
                        <a href="authenticate.php" class="btn-slide"><span>Login / Register</span></a> <!-- FIXME: GR  page too (class="btn-slide")-->
                    <?php } else { ?>
                        <form action="navbar-EN.php" method="POST">
                            <button name="logout" value="logout" class="btn btn-secondary text-center">Logout</button>
                        </form>
                    <?php } ?>
                </li>
            </ul>
        </div>
    </div>
</nav>