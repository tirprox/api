<?php
namespace dreamwhiteAPIv1;
require "../includes.php";

header('Content-type: application/json');

$filename = "assortment.json";

$url = "https://online.moysklad.ru/api/remap/1.1/entity/assortment?limit=100";

$conn = new Connector();

$response = $conn->client->get($url)->getBody();



$decoded = json_decode($response, true);

$data = $decoded;
$size = $decoded['meta']['size'];


//$size = 500;

$limit = 100;

$offset = 100;

while ($offset<$size) {
    $offsetUrl = "https://online.moysklad.ru/api/remap/1.1/entity/assortment?limit=100&offset=$offset";
    $offsetResponse  = $conn->client->get($offsetUrl)->getBody();

    //var_dump($offsetResponse);
    $rows = json_decode( $offsetResponse, true );
    //var_dump($rows);

    $data['rows'] = array_merge($data['rows'], $rows['rows']);

    $offset+=$limit;
}

$assortment = [];

foreach ($data['rows'] as $row) {
    if (isset($row['id'])) {
        $assortment[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'sku' => $row['code'],
            'barcode' => $row['barcodes'][0]
        ];
    }

}

$fileContents = json_encode($assortment, JSON_UNESCAPED_UNICODE);
echo ($fileContents);
file_put_contents($filename,  $fileContents, FILE_APPEND | LOCK_EX);



