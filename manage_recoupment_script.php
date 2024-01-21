<?php
require_once "session_manager.php";
require_once "db_connector.php";

printArray($_POST);

if ($_SESSION['role'] == 3) {

    if(isset($_POST['deny'])){

        $requestID=$status=$notes="";
        $requestID=$_POST['deny'];
        $status=2;
        $notes=$_POST['notes'];

        if(empty($notes)){
            $_SESSION['notification']['title'] = "Oops...";
            $_SESSION['notification']['message'] = "Please provide some noted before denying the request!";
            header("Location: user-dashboard-EN.php");
        }

    }elseif (isset($_POST['approve'])){
        $requestID=$_POST['approve'];
        $status=1;
    }

    $conn = connect2db();
    $stmt = $conn->prepare("UPDATE recoupment_requests SET status=?, notes=? WHERE id=?");
    $stmt->bind_param("isi", $status, $notes, $requestID);

    if($stmt->execute()){
        $_SESSION['notification']['title'] = "Success!";
        $msg="Recoupment was ";
    }else{
        $_SESSION['notification']['title'] = "Oops...";
        $msg="Recoupment could not be ";
    }

    $status==1?$msg=$msg."approved!":$msg=$msg."denied!";
    $_SESSION['notification']['message'] = $msg;
    $conn->close();
}
header("Location: user-dashboard-EN.php");




