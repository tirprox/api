<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 7/2/18
 * Time: 2:16 PM
 */

namespace dreamwhiteAPIv1;
require "../includes.php";


$user = "admin";
$pwd = '6h8s4ksoq';

$client = new \MongoDB\Client("mongodb://${user}:${pwd}@localhost:27017");

$collection = $client->test->test;


$data = [
    [
        'name' => 'test',
        'price' => '110'
    ],
    [
        'name' => 'test2',
        'price' => '1000'
    ]
];

$update = [
    'name' => 'test',
    'price' => '120'
];

$filter  = ['name' => 'test'];
$options = ['upsert' => true];

$collection->updateOne($filter, ['$set' => $update], $options);


$result = $collection->findOne(['name' => 'test']);
var_dump($result);