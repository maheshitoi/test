<?php
function changeDateFormat($dateCol, $data, $format = 'd-m-Y')
{
    foreach ($dateCol as $k => $v) {
        if (checkValue($data, $v)) {
            $data[$v] = date($format, strtotime($data[$v]));
        }
    }
    return $data;
}

function isPastDate($date)
{
    return time() > strtotime($date);

}
function isValidDate($dates)
{
    return date('Y-m-d', strtotime($dates)) === $dates;
}
function strToDate($date,$format='Y-m-d'){
    return date($format,strtotime($date));
}

function checkInBeweenDate($fromD, $todate, $checkDate = null)
{
    $currentDate = date('Y-m-d', strtotime($checkDate ? $checkDate : date('Y-m-d')));
    $startDate   = date('Y-m-d', strtotime($fromD));
    $endDate     = date('Y-m-d', strtotime($todate));
    return ($currentDate >= $startDate) && ($currentDate <= $endDate);
}
function checkDateInFinYear($from,$to){
    $finYear = findFinYear();
    $startDate   = date('Y-m-d', strtotime($from));
    $endDate     = date('Y-m-d', strtotime($to));
    return ($startDate >= $finYear[0]) && ($endDate <= $finYear[1]); 

}

function startAndEndMonthByDate($date=null,$format='Y-m-d')
{
    if(empty($date)){
        $date =date('Y-m-d');
    }
    $std       = strtotime($date);
    $month     = date('m', $std);
    $year      = date('Y', $std);
    $dateText  = strtotime('01-' . $month . '-' . $year);
    $startDate = date($format, $dateText);
    $endDateStr   = date('t-m-Y', $dateText);
    $endDate = date($format,strtotime($endDateStr));
    return [$month,$year,$startDate,$endDate,$dateText];
}
    function findFinYear($date=null,$format="Y-m-d")
{
    if(empty($date)){
        $date = date('Y-m-d');
    }
    $month      = strToDate($date,'m');
    $year       = strToDate($date,'Y');
    $finYear        = (int) $month > 4 ? $year : $year-1;
    $startYear =$finYear.'-04-01';
    $endYear = ($finYear+1).'-03-31';
    return [date($format, strtotime($startYear)),date($format, strtotime($endYear))];
}

// function getMonthRange($date=null,$format = 'Y-m-d'){
//     if(empty($date)){
//         $date =date('Y-m-d');
//     }
//     $dateText        = strtotime('01-' . date('m', strtotime($date)) . '-' . date('Y', strtotime($date)));
//         $endDate         = date('t-m-Y', $dateText);
//         return [date($format, $dateText),date($format, $endDate)];
// }
function lastDayOfPreviousMonth($format = 'Y-m-d'){
    return date($format, strtotime('last day of previous month'));
}
function countDaysBetweenDate($from_date ,$to_date=null){
    if(empty($to_date)){
        $to_date = date('Y-m-d');
    }
    $diff = abs(strtotime($from_date)-strtotime($to_date));
    return $diff/(60*60*24);
}

function subtractDays($f_d,$days){
    return date('Y-m-d', strtotime('-'.$days.' day', strtotime($f_d)));
}

function countMonthBetweenDate($from_date ,$to_date=null){
    if(empty($to_date)){
        $to_date = strtotime(date('Y-m-d'));
    }
    $from_date = strtotime($from_date);
    $months = 0;
while (strtotime('+1 MONTH', $from_date) < $to_date) {
    $months++;
    $from_date = strtotime('+1 MONTH', $from_date);
}
return $months;
}