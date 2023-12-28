<?php
function DatePeriod_start_end($begin,$end){

    $begin = new DateTime($begin);

    $end = new DateTime($end.' +7 day');

    $daterange = new DatePeriod($begin, new DateInterval('P7D'), $end);

    foreach($daterange as $date){
        $dates[] = $date->format("Y-m-d");
    }
    return $dates;

}

print_r(DatePeriod_start_end("2023-10-01", "2024-02-29"));

?>