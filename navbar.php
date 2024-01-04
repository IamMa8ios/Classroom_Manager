<?php
if (isset($_POST['logout'])) {
    session_start();
    session_destroy();
    header('Location: index.php');
}

require_once "db_connector.php";

if(isset($_POST['classroom'])){
    $conn=connect2db();

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
            <?php if(isset($_POST['classroom'])){ ?>
                <h2>Viewing <?php echo $className['name'];?></h2>
            <?php }else { echo "<h2> Please Select a Classroom </h2>"; } ?>
            <ul class="navbar-nav ms-auto ">
                <li class="nav-item me-2">
                    <button class="btn btn-primary timerBtn" onclick="location.reload()"></button>
                </li>
                <li class="nav-item me-2">
                    <button class="btn btn-primary">Change Language</button>
                </li>
                <li class="nav-item me-2">
                    <?php if ($_SESSION['role'] == 1) { ?>
                        <a href="authenticate.php" class="btn btn-secondary">Login/Register</a>
                    <?php } else { ?>
                        <form action="navbar.php" method="POST">
                        <button name="logout" value="logout" class="btn btn-secondary">Logout</button>
                        </form>
                    <?php } ?>
                </li>
            </ul>
        </div>
    </div>
</nav>