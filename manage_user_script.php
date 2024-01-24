<?php
require_once "db_connector.php";
require_once "session_manager.php";

if($_SESSION['role']==4 || $_SESSION['role']==2) { //Για τους χρήστες υπάρχει ο κατάλληλος διαχειριστής

    if (isset($_POST['save'])) { //Αν πρόκειται για ενημέρωση των στοιχείων ενός χρήστη

        //ελέγχουμε εάν πρόκειται για καθηγητή ώστε να ενημερώσουμε και τον αποκλειστικό τους πίνακα
        if (isset($_POST['teacherType'])) {
            $conn = connect2db();
            $stmt = $conn->prepare("SELECT COUNT(*) as total FROM teacher WHERE userID = ?");
            $stmt->bind_param("i", $_POST['userID']);
            $stmt->execute();
            $total = $stmt->get_result()->fetch_assoc()['total'];
            $conn->close();

            $sql = "";
            if ($total == 1) {
                $sql = "update teacher set ";
                if ($_POST['teacherType'] == 'dep') {
                    $sql = $sql . "dep=1, edip=0, etep=0";
                } elseif ($_POST['teacherType'] == 'edip') {
                    $sql = $sql . "dep=0, edip=1, etep=0";
                } else {
                    $sql = $sql . "dep=0, edip=0, etep=1";
                }

                $sql = $sql . " where userID=?";

            } else {
                //εάν δεν υπήρχαν στοιχεία καταχωρούμε αυτά που δόθηκαν
                $sql = "insert into teacher(" . sanitize($_POST['teacherType']) . ", userID) values(1, ?)";
            }

            $conn = connect2db();
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_POST['userID']);
            if ($stmt->execute()) {
                $_SESSION['notification']['title'] = "Success!";
                $_SESSION['notification']['message'] = "Teacher was updated!";
            } else {
                $_SESSION['notification']['title'] = "Oops..";
                $_SESSION['notification']['message'] = "Teacher could not be updated!";
            }
            $conn->close();
        }

        //ενημέρωση των γενικών στοιχείων του χρήστη
        $conn = connect2db();
        $stmt = $conn->prepare("update user set name=?, email=?, department=? where id=?");
        $stmt->bind_param("sssi", $_POST['name'], $_POST['email'], $_POST['dept'], $_POST['userID']);
        if ($stmt->execute()) {
            $_SESSION['notification']['title'] = "Success!";
            $_SESSION['notification']['message'] = "Profile was updated!";
        } else {
            $_SESSION['notification']['title'] = "Oops..";
            $_SESSION['notification']['message'] = "Profile could not be updated!";
        }
        $conn->close();

    }elseif (isset($_POST['delete'])){ //Διαγραφή χρήστη. Εάν πρόκειται για καθηγητή, υπάρχει cascade
        $conn = connect2db();
        $stmt = $conn->prepare("DELETE FROM user WHERE id = ?");
        $stmt->bind_param("i", $_POST['userID']);
        if ($stmt->execute()) {
            $_SESSION['notification']['title'] = "Success!";
            $_SESSION['notification']['message'] = "User was deleted!";
        } else {
            $_SESSION['notification']['title'] = "Oops..";
            $_SESSION['notification']['message'] = "User could not be deleted!";
        }
        $conn->close();
    } else {
        $_SESSION['notification']['title'] = "Oops..";
        $_SESSION['notification']['message'] = "The request could not be completed!";
        header("Location: user-profile-EN.php");
    }
}
header("Location: user-profile-EN.php");