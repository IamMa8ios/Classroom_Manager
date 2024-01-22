<?php
require_once "session_manager.php";
require_once "db_connector.php";

if ($_SESSION['role'] > 2) { //εφόσον πρόκειται για διαχειριστές

    //διαγράφουμε την αίθουσα, σε περίπτωση αίτησης διαγραφής
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
        //Αλλιώς ορίζουμε τα δεδομένα που θα εισαχθούν σε περίπτωση δημιουργίας/επεξεργασίας
        $sql = "";

        //Οι μέρες που είναι διαθέσιμες οι αίθουσες αναπαριστώνται από 5 χαρακτήρες, όπου ο 1ος αφορά τη Δευτέρα,
        // ο 2ος την Τρίτη κ.ο.κ.
        // 0 = Μη διαθέσιμη εκείνη την ημέρα
        // 1 = Διαθέσιμη εκείνη την ημέρα
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

        //Εάν έχει δοθεί πλήθος υπολογιστών, πρόκειται για εργαστήριο
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

        //ορίζουμε το κατάλληλο query ανάλογα με το αν πρόκειται να κάνουμε δημιουργία ή ενημέρωση
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

        if ($stmt->execute()) {//καταχωρούμε τα κατάλληλα μηνύματα
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