<?php

namespace dreamwhiteAPIv1;
require "../../../includes.php";

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

$input = file_get_contents('php://input');

$data = json_decode($input, true);

$url = $data['events'][0]['meta']['href'];

$manager = new CounterpartyManager();
$counterparty = $manager->getByUrl($url);


$allowedOwners = [
    'https://online.moysklad.ru/api/remap/1.1/entity/employee/57e00517-e00e-11e6-7a69-9711001f6490'// Анна - Флигель
];

//$anna = 'https://online.moysklad.ru/api/remap/1.1/entity/employee/57e00517-e00e-11e6-7a69-9711001f6490'; // anna

$name = $counterparty->props['name'];
$phone = $counterparty->props['phone'];

$owner = $counterparty->raw['owner']['meta']['href'];

echo $owner . PHP_EOL;

$tags = $counterparty->props['tags'];

if (empty($tags) && in_array($owner, $allowedOwners)) {
    $sms = new SendSMS();
    $sms->send($phone, $name);
}

echo $counterparty->props['name'];
echo $counterparty->props['phone'];


//file_put_contents('log.json', $counterparty);

exit;

