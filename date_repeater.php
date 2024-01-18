<?php
function getDatesBetween($begin,$end){

    $begin = new DateTime($begin);

    $end = new DateTime($end.' +7 day');

    $daterange = new DatePeriod($begin, new DateInterval('P7D'), $end);

    $dates=[];
    foreach($daterange as $date){
        $dates[] = $date->format("Y-m-d");
    }
    return $dates;

}

?>