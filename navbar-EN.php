<?php
//FIXME: On timeout actually logout
if (isset($_POST['logout']) || isset($_GET['logout'])) {
    session_start();
    session_destroy();
    header('Location: index-EN.php');
}
?>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <h2> <?php echo $navTitle;?></h2>
            <ul class="navbar-nav ms-auto d-flex justify-content-between align-items-baseline gap-3">
                <li class="nav-item">
                    <button class="btn-slide timerBtn" onclick="location.reload()"></button>
                </li>
                <li class="nav-item">
                    <div class="nav-item dropdown"> <!-- language dropdown menu -->
                        <button class="btn btn-secondary dropdown-toggle rounded-pill" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">Language
                            <img src="img/united-states_32px.png" class="img-responsive inline-block" alt="flag"></img>
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
                        <a href="authenticate-EN.php" class="btn-slide"><span>Login / Register</span></a>
                    <?php } else { ?>
                        <form action="navbar-EN.php" method="POST">
                            <button name="logout" value="logout" class="btn-slide"><span><i class="fas fa-sign-out-alt"></i> Logout: <?php echo $_SESSION['name']; ?></span></button>
                        </form>
                    <?php } ?>
                </li>
            </ul>
        </div>
    </div>
</nav>