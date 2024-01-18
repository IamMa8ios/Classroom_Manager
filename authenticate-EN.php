<?php
require_once 'session_manager.php';
require_once 'db_connector.php';

if ($_SESSION['role'] != 1) {
    header("Location: index-EN.php");
}

$email = $password = $name = $dept = $roleID = "";

// Handle registration
if (isset($_POST['register'])) {

    $_SESSION['role'] = 2;

    // Check if the email is already registered
    $conn = connect2db();
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Email is already registered";
        $_SESSION['role'] = 1;
    } else {

        $email = sanitize($_POST['email']);
        $password = password_hash(sanitize($_POST['password']), PASSWORD_DEFAULT);
        $name = sanitize($_POST['name']);
        $dept = sanitize($_POST['dept']);
        $roleID = 2;

        // Insert new user into the database
        $stmt = $conn->prepare("INSERT INTO user (email, password, name, department, roleID) VALUES (?, ?, ?, ?, 2)");
        $stmt->bind_param("ssss", $email, $password, $name, $dept);

        if ($stmt->execute()) {
            echo "Registration successful";
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['dept'] = $dept;
            header("Location: index-EN.php");
        } else {
            $_SESSION['role'] = 1;
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
}

// Handle login
if (isset($_POST['login'])) {

    $_SESSION['role'] = 2;

    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    $conn = connect2db();

    // Retrieve user from the database based on the email
    $stmt = $conn->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            echo "Login successful";
            $_SESSION['name'] = $row['name'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['dept'] = $row['department'];
            $_SESSION['userID'] = $row['id'];
            $_SESSION['role'] = $row['roleID'];
            $_SESSION['notification'] = 'createSuccessAlert("center","Login successful")';
            header("Location: index-EN.php");
        } else {
            echo "Invalid password";
            $_SESSION['role'] = 1;
        }
    } else {
        echo "Email not found";
        $_SESSION['role'] = 1;
    }

    $stmt->close();
}

?>

<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://fonts.googleapis.com/css?family=Roboto:300,400&display=swap" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css?family=Poppins:300,400,500&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="fonts/icomoon/style.css">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">

    <!-- Style -->
    <link rel="stylesheet" href="css/login-register.css">

    <!-- Font Awesome JS -->
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/solid.js" integrity="sha384-tzzSw1/Vo+0N5UhStP3bvwWPq+uvzCMfrN1fEFe+xBmv1C/AtVX5K0uZtmcHitFZ" crossorigin="anonymous"></script>
    <script defer src="https://use.fontawesome.com/releases/v5.0.13/js/fontawesome.js" integrity="sha384-6OIrr52G08NpOFSZdxxz1xdNSndlD4vdcf/q2myIUVO0VsqaGHJsB0RaBE01VTOY" crossorigin="anonymous"></script>

    <title>Calendar #10</title>
</head>

<body>
    <div class="section">
        <div class="container">
            <div class="row full-height justify-content-center">
                <div class="col-12 text-center align-self-center py-5">
                    <div class="section pb-5 pt-5 pt-sm-2 text-center">
                        <h6 class="mb-0 pb-3"><span>Log In </span><span>Register</span></h6>
                        <input class="checkbox" type="checkbox" id="reg-log" name="reg-log" />
                        <label for="reg-log"></label>
                        <div class="card-3d-wrap mx-auto">
                            <div class="card-3d-wrapper">

                                <div class="card-front">
                                    <div class="center-wrap">
                                        <div class="section text-center">
                                            <h4 class="mb-4 pb-3">Log In</h4>
                                            <form action="authenticate-EN.php" method="post">
                                                <div class="form-group">
                                                    <input type="email" name="email" class="form-style" placeholder="Your Email" id="email" autocomplete="off">
                                                    <i class="input-icon fas fa-at"></i>
                                                </div>
                                                <div class="form-group mt-2">
                                                    <input type="password" name="password" class="form-style" placeholder="Your Password" id="pass" autocomplete="off">
                                                    <i class="input-icon fas fa-lock"></i>
                                                </div>
                                                <button name="login" value="login" class="btn mt-4">submit</button>
                                                <a href="index-EN.php" class="btn mt-4"><i class="fas fa-long-arrow-alt-left mx-2"></i>back</a>
                                                <p class="mb-0 mt-4 text-center"><a href="#0" class="link">Forgot your
                                                        password?</a></p>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-back">
                                    <div class="center-wrap">
                                        <div class="section text-center">
                                            <h4 class="mb-4 pb-3">Register</h4>
                                            <form action="authenticate-EN.php" method="post">
                                                <div class="form-group">
                                                    <input type="text" name="name" class="form-style" value="<?php echo htmlspecialchars($name); ?>" placeholder="Your Full Name" id="name" autocomplete="off">
                                                    <i class="input-icon fas fa-id-card"></i>
                                                </div>
                                                <div class="form-group mt-2">
                                                    <input type="email" name="email" class="form-style" value="<?php echo htmlspecialchars($email); ?>" placeholder="Your Email" id="email" autocomplete="off">
                                                    <i class="input-icon fas fa-at"></i>
                                                </div>
                                                <div class="form-group mt-2">
                                                    <input type="password" name="password" class="form-style" placeholder="Your Password" id="password" autocomplete="off" value="<?php echo htmlspecialchars($password); ?>">
                                                    <i class="input-icon fas fa-lock"></i>
                                                </div>
                                                <div class="form-group mt-2">
                                                    <input type="text" name="dept" class="form-style" placeholder="Your Department" id="dept" autocomplete="off" value="<?php echo htmlspecialchars($dept); ?>">
                                                    <i class="input-icon fas fa-building"></i>
                                                </div>
                                                <button name="register" value="register" class="btn mt-4" type="submit">submit</button>
                                                <a href="index-EN.php" class="btn mt-4"><i class="fas fa-long-arrow-alt-left mx-2"></i>back</a>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>

</body>

</html>