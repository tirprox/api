<?php
namespace dreamwhiteAPI;
//require "CounterpartyGetter.php";
$input = json_decode(file_get_contents('php://input'));
$phone = $input->phone;

//$phone = "9110239927";
$counterpartyGetter = new CounterpartyGetter();

$data = $counterpartyGetter->getByPhone($phone);

//$data = $counterpartyGetter->getByPhone();

header('Content-type: application/json');

//var_dump($data);
echo json_encode($data, JSON_UNESCAPED_UNICODE);

//echo json_encode($exporter->getCounterpartyByPhone($phone));
//echo $exporter->getCounterpartyByPhone($phone);
