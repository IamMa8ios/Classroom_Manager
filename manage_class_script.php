<?php
require_once "session_manager.php";
require_once "db_connector.php";

if($_SESSION['role']>2){

    if(isset($_POST['delete'])){
        $conn = connect2db();
        $stmt = $conn->prepare("delete from classroom where id = ?");
        $stmt->bind_param("i", $_POST['delete']);
        if($stmt->execute()){
            echo "class ".$_POST['delete']." deleted";
        }else{
            echo "could not delete";
        }
        $conn->close();
    }else{
        print_r($_POST);
//        [name] => class 1 [building] => building 1 [startTime] => 09:00:00 [endTime] => 21:00:00 [capacity]
//        [projector] => [monday] => [tuesday] => [wednesday] => [thursday] => [friday] => [computers] => [action] => edit
        $sql="";
        $daysOfWeek="00000";
        if(isset($_POST['monday'])){
            $daysOfWeek[0]="1";
        }
        if(isset($_POST['tuesday'])){
            $daysOfWeek[1]="1";
        }
        if(isset($_POST['wednesday'])){
            $daysOfWeek[2]="1";
        }
        if(isset($_POST['thursday'])){
            $daysOfWeek[3]="1";
        }
        if(isset($_POST['friday'])){
            $daysOfWeek[4]="1";
        }
        if(isset($_POST['action']) && strcmp($_POST['action'], "create")==0){
            $sql="insert into class() values(?, ?, ?, ?)";
        }elseif(isset($_POST['action']) && strcmp($_POST['action'], "edit")==0){
            echo "edit";
        }

    }

}
