<?php
require_once "session_manager.php";
require_once "db_connector.php";

if(isset($_GET['userID'])){
    if(isset($_GET['action'])){

        //FIXME: complete DB actions
        if($_GET['action'] == 1){ // Approve Recoupment
            echo 'Approve Recoupment';

        }else if($_GET['action'] == 2){ // Deny Recoupment
            echo 'Deny Recoupment';

        }
    }
}

exit();
