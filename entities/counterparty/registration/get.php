<?php

namespace dreamwhiteAPIv1;
header("Access-Control-Allow-Origin: *");
require_once "../../../includes.php";

/*ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);*/

$input = json_decode(file_get_contents('php://input'));
$phone = $input->phone;

$manager = new CounterpartyManager();
$counterparty = $manager->getByPhone($phone);

header('Content-type: application/json');

//$data = [];
$data['id'] = $counterparty->id();
$data['firstName'] =  $counterparty->name();
$data['lastName'] = $counterparty->lastName();
$data['birthday'] = formatBirthday($counterparty->birthday());

$data['country'] = $counterparty->country();
$data['city'] = $counterparty->city();
$data['address'] = $counterparty->address();
$data['postcode'] = $counterparty->postcode();
$data['phone'] = formatPhone($counterparty->phone());
$data['email'] = $counterparty->email();

echo json_encode($data, JSON_UNESCAPED_UNICODE);

function formatPhone(string $phone) : array {
    $prefix = '+7';
    $codes = [
        '+7', // Россия, Казахстан
        '+375' // Беларусь
    ];

    foreach ($codes as $code) {
        if (strpos($phone, $code) !== false) {
            $prefix = $code;
            $phone = str_replace($code, "", $phone);
            break;
        }
    }

    $phone = str_replace("-", "", $phone);
    $phone = str_replace(" ", "", $phone);

    $data = [];
    $data['prefix'] = $prefix;
    $data['number'] = $phone;

    return $data;
}

function formatBirthday(string $date) : string {
    $parsed = date_parse($date);
    return $parsed['day']  . '.' . $parsed['month'] . '.' .$parsed['year'] ;
}