<?php
function connect2db(){
    $servername = "localhost";

    $username = null;

    if(!isset($_SESSION)){
        session_start();
    }

    //ορίζουμε το όνομα του χρήστη που θα συνδεθεί στη βάση δεδομένων με βάση το ρόλο του χρήστη
    if(isset($_SESSION['role'])){
        if($_SESSION['role']==1){
            $username="guest_user";
        }elseif($_SESSION['role']==2){
            $username="teacher_user";
        }elseif($_SESSION['role']==3){
            $username="booking_admin_user";
        }elseif($_SESSION['role']==4){
            $username="teacher_admin_user";
        }
    }else{//εάν δεν έχει ξεκινήσει κάποιο session, θεωρούμε ότι πρόκειται για επισκέπτη
        $_SESSION['role']=1;
        $username="guest_user";
    }
    //Για απλότητα, ο κωδικός είναι κοινός για όλους
    $password = "123456";

// Create connection
    $conn = new mysqli($servername, $username, $password);
    $conn->select_db("classroom_booking_db");

// Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

//Βοηθητικές μέθοδοι
function sanitize($data){
    return htmlspecialchars(stripslashes(trim($data)));
}

function printArray($array){
    echo "<pre>";
    print_r($array);
    echo "</pre>";
}
