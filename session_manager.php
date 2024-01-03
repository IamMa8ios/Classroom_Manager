<?php
if(!isset($_SESSION)){
    session_start();
}
if(!isset($_SESSION['role'])){
    $_SESSION['role']=1;
}