<?php
require_once "session_manager.php";
require_once "db_connector.php";

if ($_SESSION['role'] == 3) { // role = res_admin

    if(isset($_POST['deny'])){ // deny recoupment request

        $requestID=$status=$notes="";
        $requestID=$_POST['deny'];
        $status=2;
        $notes=$_POST['notes'];

        if(empty($notes)){
            $_SESSION['notification']['title'] = "Oops...";
            $_SESSION['notification']['message'] = "Please provide some noted before denying the request!";
            header("Location: user-dashboard-EN.php");
        }

    }elseif (isset($_POST['approve'])){ // // approve recoupment request
        $requestID=$_POST['approve'];
        $status=1;
    }

    $conn = connect2db();
    $stmt = $conn->prepare("UPDATE recoupment_requests SET status=?, notes=? WHERE id=?");
    $stmt->bind_param("isi", $status, $notes, $requestID);

    if($stmt->execute()){ // sql query ok
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




