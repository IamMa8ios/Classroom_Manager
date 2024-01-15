<?php
require_once "session_manager.php";
require_once "db_connector.php";

if ($_SESSION['role'] > 2) {

    if (isset($_POST['delete'])) {
        $conn = connect2db();
        $stmt = $conn->prepare("delete from classroom where id = ?");
        $stmt->bind_param("i", $_POST['delete']);
        if ($stmt->execute()) {
            echo "class " . $_POST['delete'] . " deleted";
        } else {
            echo "could not delete";
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

        if (isset($_POST['action']) && strcmp($_POST['action'], "create") == 0) {
            $sql = "insert into classroom (name, building, capacity, time_available_start, time_available_end, " .
                "days_available, type, computers, projector, locked) values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $conn = connect2db();
            $stmt = $conn->prepare($sql);
        } elseif (isset($_POST['action']) && strcmp($_POST['action'], "edit") == 0) {

            $sql = "update classroom set name=?, building=?, capacity=?, time_available_start=?, time_available_end=?, " .
                "days_available=?, type=?, computers=?, projector=?, locked=? where id=?";
            $conn = connect2db();
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssisssiiiii", $_POST['name'], $_POST['building'], $_POST['capacity'], $_POST['startTime'],
                $_POST['endTime'], $daysOfWeek, $type, $_POST['computers'], $projector, $locked, $_POST['classID']);

        }

        if ($stmt->execute()) {
            if (strcmp($_POST['action'], "create") == 0) {
                echo "class created";
            } else {
                echo "class " . $_POST['name'] . " edited";
            }
        } else {
            echo $stmt->error;
        }

        $conn->close();

    }

}

header("Location: index-en.php");