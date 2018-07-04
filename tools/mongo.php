<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 7/2/18
 * Time: 2:16 PM
 */

namespace dreamwhiteAPIv1;
require "../includes.php";


require "TagRewriteRules.php";

$user = "admin";
$pwd = '6h8s4ksoq';

$client = new \MongoDB\Client("mongodb://${user}:${pwd}@localhost:27017");

$collection = $client->test->test;


$data = \TagRewriteRules::$rules;

$update = [
    'name' => 'test',
    'price' => '120'
];

$filter  = ['name' => 'test'];
$options = ['upsert' => true];

foreach ($data as $key => $value) {
    $filter  = ['name' => $key];
    $values = explode(",", $value);

    $record = [
      'name' => $key,
      'colors' => $values
    ];


    //$collection->updateOne($filter, ['$set' => $record], $options);
    $collection->updateOne($filter, ['$pull' => ['colors' => 'testColor']], $options);
}

//$collection->updateOne($filter, ['$set' => $update], $options);


$result = $collection->findOne(['name' => 'krasnye-zhenskie-palto']);
foreach ($result['colors'] as $color) {
    print($color . PHP_EOL);
}
