<?php
function connect2db(){
    $servername = "localhost";

    $username = null;

    if(isset($_SESSION) && isset($_SESSION['role'])){
        if($_SESSION['role']==1){
            $username="guest_user";
        }else if($_SESSION['role']==2){
            $username="teacher_user";
        }else if($_SESSION['role']==3){
            $username="res_admin";
        }else if($_SESSION['role']==4){
            $username="user_admin";
        }
    }

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
