<?php
namespace dreamwhiteAPI;

require '../vendor/autoload.php';
include "Auth.php";

use GuzzleHttp\Promise;
use GuzzleHttp\Client;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

class CounterpartyGetter
{
  const MS_BASE_URL = "https://online.moysklad.ru/api/remap/1.1/entity/";
  const MS_POST_URL = "https://online.moysklad.ru/api/remap/1.1/entity/counterparty/";
  var $HEADERS = [];
  var $client;

  function __construct()
  {
    $this->HEADERS = [
      'auth' => [Auth::login, Auth::password],

      'headers' => ['Content-Type' => 'application/json',
        'Authorization' => 'Basic '. base64_encode(Auth::login.':'.Auth::password),],
      'stream_context' => [
        'ssl' => [
          'allow_self_signed' => true
        ],
      ],
      'verify' => false,
    ];
    $this->client = new Client($this->HEADERS);
  }

  function getByPhone(string $phone) : array
  {
    $data = [];
    $requestUrl = self::MS_BASE_URL . "counterparty?search=" . $phone;
    $response = $this->client->get($requestUrl, $this->HEADERS);
    $response = json_decode($response->getBody(), true);

    if (count($response['rows']) === 0) {
      $data['error'] = 'Not Found';
      return $data;
    } else {
      $row = $response['rows'][0];

      $attrs = $row['attributes'];

      $getAttr = function(string $name) use ($attrs) {
        foreach ($attrs as $attr) {
          if ($attr['name'] === $name) return $attr['value'];
        }
        return "";
      };

      $getProp = function(string $name) use ($row) {
        if (array_key_exists($name, $row))
          return $row[$name];
        else return "";
      };

      $data['id'] = $row['id'];
      $data['firstName'] =  $row['name'];
      $data['lastName'] = $getAttr("Фамилия");
      $data['birthday'] = $this->formatBirthday($getAttr("Дата рождения"));

      $data['country'] = $getAttr("Страна");
      $data['city'] = $getAttr("Город");
      $data['address'] = $getAttr("Адрес");
      $data['postcode'] = $getAttr("Почтовый индекс");
      $data['phone'] = $this->formatPhone($getProp('phone'));
      $data['email'] = $getProp('email');

      return $data;
    }

  }


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

}
