<?php
$str = "Покажи расписание на сегодня и завтра";

$reg = array(
    'gEsTod' => "/(пока(жи|зать) расписание на сегодня)|(ч[оёе] там сегодня)|(на сегодня)/iu",
    'gEsTom' => "/(пока(жи|зать) расписание на завтра)|(ч[оёе] там завтра)|(на завтра)/iu",
    'gEsTW' => "/(пока(жи|зать) расписание на( эт(у|ой) | )недел[юеи])|(ч[оёе] там на( этой | )недел[еи])|(на( эт(у|ой) | )недел[еию])/iu",
    'gEsNW' => "/пока(жи|зать) расписание на следующ(ую|ей) недел[юеи])|(ч[оёе] там на следующей недел[еи])|(на следующую неделю)/iu",
    'gNTW' => "/(пока(жи|зать) номер( этой | )недели)|(какая( эта | )неделя)/iu",
);

$parse_date = preg_split("/ /iu",trim("на 24 сентября"));
if(preg_match("/\\d{4}/iu", $parse_date[count($parse_date)-1])) {
    $pos = count($parse_date)-2;
    $date[0] = $parse_date[count($parse_date)-3];
    $date[2] = $parse_date[count($parse_date)-1];
} else {
    $pos = count($parse_date)-1;
    $date[0] = $parse_date[count($parse_date)-2];
    $date[2] = date("Y");
}
switch (true){
    case (preg_match("/^сентябр[яь]$/iu", $parse_date[$pos])):
        $date[1] = "sep";
        break;
    case (preg_match("/^октябр[яь]$/iu", $parse_date[$pos])):
        $date[1] = "oct";
        break;
    case (preg_match("/^ноябр[яь]$/iu", $parse_date[$pos])):
        $date[1] = "nov";
        break;
    case (preg_match("/^декабр[яь]$/iu", $parse_date[$pos])):
        $date[1] = "dec";
        break;
    case (preg_match("/^январ[яь]$/iu", $parse_date[$pos])):
        $date[1] = "jan";
        break;
    case (preg_match("/^феврал[яь]$/iu", $parse_date[$pos])):
        $date[1] = "feb";
        break;
    case (preg_match("/^март(а|)$/iu", $parse_date[$pos])):
        $date[1] = "mar";
        break;
    case (preg_match("/^апрел[яь]$/iu", $parse_date[$pos])):
        $date[1] = "apr";
        break;
    case (preg_match("/^ма[яй]$/iu", $parse_date[$pos])):
        $date[1] = "may";
        break;
    case (preg_match("/^июн[яь]$/iu", $parse_date[$pos])):
        $date[1] = "jun";
        break;
    case (preg_match("/^июл[яь]$/iu", $parse_date[$pos])):
        $date[1] = "jul";
        break;
    case (preg_match("/^август(а|)$/iu", $parse_date[$pos])):
        $date[1] = "aug";
        break;
    default:
        return;
}
$testjson = "[123,124,125,126]";
print_r(json_decode($testjson));

