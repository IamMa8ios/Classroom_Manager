<?php
if (isset($_POST['logout']) || isset($_GET['logout'])) {
    session_start();
    session_destroy();
    header('Location: index-gr.php');
}
?>

<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <h2> <?php echo $navTitle; ?></h2>
            <ul class="navbar-nav ms-auto d-flex justify-content-between align-items-baseline gap-3">
                <li class="nav-item">
                    <button class="btn-slide timerBtn rounded-pill" onclick="location.reload()"></button>
                </li>

                <li class="nav-item">
                    <?php if ($_SESSION['role'] == 1) { ?>
                        <a href="authenticate-gr.php"
                           class="dropdown-item btn-slide"><span>Είσοδος / Εγγραφή</span></a>
                    <?php } else { ?>
                    <div class="btn-group">
                        <button type="button"
                                class="rounded-pill btn btn-secondary dropdown-toggle dropdown-toggle-split"
                                id="settings"
                                data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user"></i>
                            <?php echo $_SESSION['name']; ?>
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu pe-4 setting-dropdown-menu">
                            <li><a class="dropdown-item" href="user-profile-gr.php">Προφίλ</a></li>
                            <li><a class="dropdown-item" >Γλώσσα</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li class="nav-item">
                                <a class="dropdown-item" href="index-gr.php">
                                    <img src="img/united-states_64px.png" class="img-responsive inline-block" alt="english flag"></img>
                                    EN-US
                                </a>
                                <a class="dropdown-item" href="index-GR.php">
                                    <img src="img/greece_64px.png" class="img-responsive inline-block" alt="greek flag"></img>
                                    GR
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li class="mt-4">
                                    <form action="navbar-gr.php" method="POST">
                                        <button name="logout" value="logout" class="dropdown-item btn-slide"><span><i
                                                        class="fas fa-sign-out-alt"></i> Αποσύνδεση</span>
                                        </button>
                                    </form>
                                <?php } ?>
                            </li>
                </li>
            </ul>
        </div>

    </div>
</nav>