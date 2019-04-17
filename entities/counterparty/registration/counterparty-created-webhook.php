<?php

namespace dreamwhiteAPIv1;
require "../../../includes.php";

/*
WEBHOOK CREATION:
POST  https://online.moysklad.ru/api/remap/1.1/entity/webhook/9a59ca4c-5d07-11e9-9107-504800033e26
{
            "entityType": "counterparty",
            "url": "http://api.dreamwhite.ru/entities/counterparty/registration/counterparty-created-webhook.php",
            "action": "CREATE"
}
*/

ini_set('display_startup_errors', 1);
ini_set('display_errors', 1);
error_reporting(-1);

$input = file_get_contents('php://input');

$data = json_decode($input, true);

$url = $data['events'][0]['meta']['href'];

$manager = new CounterpartyManager();
$counterparty = $manager->getByUrl($url);


$allowedOwners = [
    'https://online.moysklad.ru/api/remap/1.1/entity/employee/57e00517-e00e-11e6-7a69-9711001f6490', // Анна - Флигель
    'https://online.moysklad.ru/api/remap/1.1/entity/employee/9bc26ef1-7160-11e8-9ff4-34e80003dae8', // Аня Прусакова - Москва
];

$ownerToSiteMap = [
    'https://online.moysklad.ru/api/remap/1.1/entity/employee/57e00517-e00e-11e6-7a69-9711001f6490' => 'dreamwhite.ru',
    'https://online.moysklad.ru/api/remap/1.1/entity/employee/9bc26ef1-7160-11e8-9ff4-34e80003dae8' => 'msk.dreamwhite.ru',
];

//$anna = 'https://online.moysklad.ru/api/remap/1.1/entity/employee/57e00517-e00e-11e6-7a69-9711001f6490'; // anna

$name = $counterparty->props['name'];
$phone = $counterparty->props['phone'];

$owner = $counterparty->raw['owner']['meta']['href'];

echo $owner . PHP_EOL;

$tags = $counterparty->props['tags'];

if (empty($tags) && in_array($owner, $allowedOwners)) {
    $sms = new SendSMS();
    $sms->sendBySite($phone, $name, $ownerToSiteMap[$owner]);
}

echo $counterparty->props['name'];
echo $counterparty->props['phone'];


//file_put_contents('log.json', $counterparty);

exit;

