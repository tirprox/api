<?php

namespace dreamwhiteAPIv1;
header("Access-Control-Allow-Origin: *");
require "../../../includes.php";
$data = json_decode(file_get_contents('php://input'), true);
if (array_key_exists('data', $data)) {
    $data = $data['data'];
}
//file_put_contents('post_test.txt', json_encode($data, JSON_UNESCAPED_UNICODE));


$counterparty = DataHelper::encode($data, 'anketa-site');

$manager = new CounterpartyManager();

$exists = $manager->getByPhone($counterparty->phone())->id() !== '';

if (!$exists)  {
    $manager->post($counterparty);
}

else {
    $manager->put($counterparty);
}

//$manager->post($counterparty);

$userManager = new WPUserManager();
$userManager->createUser($counterparty);