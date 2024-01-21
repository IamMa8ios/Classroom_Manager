<?php
require_once "session_manager.php";
require_once "db_connector.php";

if ($_SESSION['role'] > 2) {

    if (isset($_POST['delete'])) {
        $conn = connect2db();
        $stmt = $conn->prepare("delete from classroom where id = ?");
        $stmt->bind_param("i", $_POST['delete']);
        if ($stmt->execute()) {
            $_SESSION['notification']['title'] = "Success!";
            $_SESSION['notification']['message'] = "Class was successfully deleted!";
        } else {
            $_SESSION['notification']['title'] = "Oops";
            $_SESSION['notification']['message'] = "Class could not be deleted!";
        }
        $conn->close();
    } else {

        $sql = "";
        $daysOfWeek = "00000";
        if (isset($_POST['monday'])) {
            $daysOfWeek[0] = "1";
        }
        if (isset($_POST['tuesday'])) {
            $daysOfWeek[1] = "1";
        }
        if (isset($_POST['wednesday'])) {
            $daysOfWeek[2] = "1";
        }
        if (isset($_POST['thursday'])) {
            $daysOfWeek[3] = "1";
        }
        if (isset($_POST['friday'])) {
            $daysOfWeek[4] = "1";
        }

        if (isset($_POST['computers']) && $_POST['computers'] != null && $_POST['computers'] > 0) {
            $type = 0;
        } else {
            $type = 1;
        }

        if (isset($_POST['projector'])) {
            $projector = 1;
        } else {
            $projector = 0;
        }

        if (isset($_POST['locked'])) {
            $locked = 1;
        } else {
            $locked = 0;
        }

        $conn = connect2db();
        $sql="";

        if(isset($_POST['edit'])){
            $sql = "update classroom set name=?, building=?, capacity=?, time_available_start=?, time_available_end=?, " .
                "days_available=?, type=?, computers=?, projector=?, locked=? where id=".$_POST['edit'];
        }elseif(isset($_POST['create'])){
            $sql = "insert into classroom (name, building, capacity, time_available_start, time_available_end, " .
                "days_available, type, computers, projector, locked) values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        }
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisssiiii", $_POST['name'], $_POST['building'], $_POST['capacity'], $_POST['startTime'],
            $_POST['endTime'], $daysOfWeek, $type, $_POST['computers'], $projector, $locked);

        if ($stmt->execute()) {
            if (strcmp($_POST['action'], "create") == 0) {
                $_SESSION['notification']['title'] = "Success!";
                $_SESSION['notification']['message'] = "Class ".$_POST['name']." in ".$_POST['building']." was created!";
            } else {
                $_SESSION['notification']['title'] = "Success!";
                $_SESSION['notification']['message'] = "Changes in class ".$_POST['name']." have been saved!";
            }
        } else {
            $_SESSION['notification']['title'] = "Oops...";
            $_SESSION['notification']['message'] = "Class could not be created!";
        }

        $conn->close();

    }

}

header("Location: index-EN.php");