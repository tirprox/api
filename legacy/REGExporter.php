<?php
//require '../vendor/autoload.php';
//include "REGAuth.php";

use GuzzleHttp\Promise;
use GuzzleHttp\Client;

use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

class REGExporter
{
  const MS_BASE_URL = "https://online.moysklad.ru/api/remap/1.1/entity/";
  const MS_POST_URL = "https://online.moysklad.ru/api/remap/1.1/entity/counterparty/";
  const HEADERS = [
    'auth' => [REGAuth::login, REGAuth::password],
    'headers' => ['Content-Type' => 'application/json'],
    'stream_context' => [
      'ssl' => [
        'allow_self_signed' => true
      ],
    ],
    'verify' => false,
  ];

  var $client, $promises;

  function __construct()
  {
    $this->client = new Client(self::HEADERS);
    $this->promises = [];
  }

  function encodeCounterpartyForPost($item)
  {
    $encoded = [
      "name" => $item->row->clientName,
      "phone" => $item->row->phone,
      "email" => $item->row->email,
      "tags" => ["anketa-site"],
      "companyType" => "individual",
      "group" => [
        "meta" => [
          "href" => "https://online.moysklad.ru/api/remap/1.1/entity/group/59c74466-a4ef-11e7-7a69-8f5500021289",
          "metadataHref" => "https://online.moysklad.ru/api/remap/1.1/entity/group/metadata",
          "type" => "group",
          "mediaType" => "application/json"
        ]
      ],

      "attributes" => [
        [
          "id" => "c6597688-cf9b-11e7-7a6c-d2a9000ec13c",
          "name" => "Фамилия",
          "type" => "string",
          "value" => $item->row->clientLastName
        ],
        [
          "id" => "fe06e4f2-d034-11e7-7a34-5acf0006a4c2",
          "name" => "Источник",
          "type" => "string",
          "value" => $item->row->infoSource
        ],
        [
          "id" => "fe06e948-d034-11e7-7a34-5acf0006a4c3",
          "name" => "Дата регистрации анкеты",
          "type" => "time",
          "value" => self::prepare_time($item->row->date, $item->row->time)
        ],
      ],
    ];

    if ($item->row->birthday !== "") {
      $encoded['attributes'][] = [
        "id" => "c5be5aa5-f79a-11e7-7a69-9711000e03aa",
        "name" => "Дата рождения",
        "type" => "time",
        "value" => self::prepare_time($item->row->birthday, $item->row->time)
      ];
    }

    return $encoded;
  }

  function encodeCounterpartyForPut($item)
  {
    $encoded = [
      "name" => $item->row->clientName,
      "phone" => $item->row->phone,
      "email" => $item->row->email,
      "tags" => ["anketa-updated-site"],
      "companyType" => "individual",
      "group" => [
        "meta" => [
          "href" => "https://online.moysklad.ru/api/remap/1.1/entity/group/59c74466-a4ef-11e7-7a69-8f5500021289",
          "metadataHref" => "https://online.moysklad.ru/api/remap/1.1/entity/group/metadata",
          "type" => "group",
          "mediaType" => "application/json"
        ]
      ],

      "attributes" => [
        [
          "id" => "c6597688-cf9b-11e7-7a6c-d2a9000ec13c",
          "name" => "Фамилия",
          "type" => "string",
          "value" => $item->row->clientLastName
        ],
      ],
    ];

    if ($item->row->birthday !== "") {
      $encoded['attributes'][] = [
        "id" => "c5be5aa5-f79a-11e7-7a69-9711000e03aa",
        "name" => "Дата рождения",
        "type" => "time",
        "value" => self::prepare_time($item->row->birthday, $item->row->time)
      ];
    }

    return $encoded;
  }

  function exportCounterpartyFromAnketaJSON($counterpartyJSON)
  {
    $this->requestCounterparty($counterpartyJSON);
  }

  function putCounterparty($counterparty, $id)
  {
    $requestUrl = self::MS_BASE_URL . "counterparty/" . $id;

    $postJSON = json_encode($counterparty, JSON_UNESCAPED_UNICODE);
    $options = array_merge(self::HEADERS, ['body' => $postJSON]);

    $response = $this->client->request('PUT', $requestUrl, $options);
  }

  function getCounterpartyByPhone($phone) {
    $requestUrl = self::MS_BASE_URL . "counterparty?search=" . $phone;
    //$response = $this->client->get($requestUrl);

    $response = $this->client->request('GET', $requestUrl, self::HEADERS);

    $response = json_decode($response->getBody());

    if(count($response->rows)===0) {
      return("not found");
    }

    else {
      $row = $response->rows[0];

      $attrs = $row->attributes;
      $lastName = function($attributes) {
        foreach ($attributes as $attr) {
          if ($attr->name === "Фамилия") return $attr->value;
        }
        return "";
      };

      $email = function($r)  {
        if (property_exists($r, "email"))
          return $r->email;
        else return "";
      };

      $data = [
        "id" => $row->id,
        "firstName"=>$row->name,
        "lastName"=> $lastName($attrs),
        "phone" => $phone,
        "email" => $email($row)
      ];

      return $data;
    }



  }

  function requestCounterparty($counterparty)
  {

    $requestUrl = self::MS_BASE_URL . "counterparty?search=" . self::prepare_phone($counterparty->row->phone);
    //$requestUrl = self::MS_BASE_URL . "counterparty?search=" . self::prepare_phone($counterparty['phone']);
    $promise = $this->client->requestAsync('GET', $requestUrl, self::HEADERS);


    $promise->then(
      function (ResponseInterface $res) use ($counterparty) {
        $response = json_decode($res->getBody());

        if (count($response->rows) === 0) {
          print("posting client\n");

          $this->postCounterparty($this->encodeCounterpartyForPost($counterparty));
        }
        else {
          $this->putCounterparty($this->encodeCounterpartyForPut($counterparty), $response->rows[0]->id);
        }
      },
      function (RequestException $e) {
        echo $e->getMessage() . "\n";
        echo $e->getRequest()->getMethod();
      }
    );
    $this->promises[] = $promise;
  }

  function postCounterparty($counterparty)
  {
    $postJSON = json_encode($counterparty, JSON_UNESCAPED_UNICODE);
    $options = array_merge(self::HEADERS, ['body' => $postJSON]);

    $postPromise = $this->client->requestAsync('POST', self::MS_POST_URL, $options);
    $postPromise->then(
      function (ResponseInterface $res) {
        $response = json_decode($res->getBody());
      },
      function (RequestException $e) {
        echo $e->getMessage() . "\n";
        echo $e->getRequest()->getMethod();
      });
    $this->promises[] = $postPromise;
  }

  function completeAllRequests()
  {
    Promise\settle($this->promises)->wait();
  }

  static function prepare_phone($phone)
  {
    $phone = str_replace("+", "", $phone);
    $phone = str_replace("-", "", $phone);
    $phone = str_replace(" ", "", $phone);
    return $phone;
  }

  static function prepare_time($date, $time)
  {
    $dateArray = date_parse_from_format("j.n.Y", $date);
    //$dateArray = date_parse($date);
    $timeString = $dateArray['year'] . "-" . $dateArray['month'] . "-" . $dateArray['day'] . " " . $time;
    return $timeString;
  }
}
