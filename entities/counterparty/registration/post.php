<?php

namespace dreamwhiteAPIv1;
header("Access-Control-Allow-Origin: *");
require_once "../../../includes.php";
require_once "DataHelper.php";
$data = json_decode(file_get_contents('php://input'), true)['data'];

$counterparty = DataHelper::encode($data);

$manager = new CounterpartyManager();
$manager->post($counterparty);