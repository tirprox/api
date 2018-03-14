<?php
/**
 * Created by PhpStorm.
 * User: dreamwhite
 * Date: 06.03.2018
 * Time: 16:06
 */

namespace dreamwhiteAPIv1;
//require_once "../../includes.php";

class CounterpartyManager
{
    const COUNTERPARTY_BASE_URL = "https://online.moysklad.ru/api/remap/1.1/entity/counterparty/";
    const MS_BASE_URL = "https://online.moysklad.ru/api/remap/1.1/entity/";

    private $client;

    function __construct()
    {
        $connector = new Connector();
        $this->client = $connector->client;
    }


    //returns encoded json string ready to be sent
    function encode(Counterparty $counterparty) : string {

        $encoded = [
            "name" => $counterparty->props['name'],
            "phone" => $counterparty->props['phone'],
            "email" => $counterparty->props['email'],
            "tags" => $counterparty->props['tags'],
            "companyType" => "individual",
            "group" => [
                "meta" => [
                    "href" => "https://online.moysklad.ru/api/remap/1.1/entity/group/59c74466-a4ef-11e7-7a69-8f5500021289",
                    "metadataHref" => "https://online.moysklad.ru/api/remap/1.1/entity/group/metadata",
                    "type" => "group",
                    "mediaType" => "application/json"
                ]
            ],
           // "attributes" => $counterparty->attrs
        ];

        $a = [];

        foreach ($counterparty->attrs as $name => $parameters) {
            $a[] = [
                'id' => $parameters['id'],
                'name' => array_search ($name, Counterparty::$attrAlias),
                'type' => $parameters['type'],
                'value' => $parameters['value']
            ];
        }

        $encoded['attributes'] = $a;

        //echo json_encode($encoded,JSON_UNESCAPED_UNICODE);
        return json_encode($encoded, JSON_UNESCAPED_UNICODE);

    }

    function decode($json) /*: Counterparty*/ {
        $json = json_decode($json, true);
        $counterparty = new Counterparty();


        /* When accessing a counterparty by id, it won't return a row, but it returns rows on search and filter */
        $data = [];
        if (isset($json['rows'])) {
            $data = $json['rows'][0];
        }
        else {
            $data = $json;
        }

        $counterparty->raw = $json;


        // Encoding props
        foreach ($counterparty->props as $key => $value) {
            $counterparty->props[$key] = $data[$key] ?? '';
        }

        // Encoding attributes
        if (array_key_exists('attributes', $data)) {
            foreach ($data['attributes'] as $attr) {
                $a = [
                    'id' => $attr['id'],
                    'value' => $attr['value'],
                    'type' => $attr['type'],
                ];
                $counterparty->attrs[ Counterparty::$attrAlias[ $attr['name'] ] ] = $a;
            }
        }


        return $counterparty;
    }


    function getByPhone(string $phone) : Counterparty {
        return $this->search($phone);
    }

    function getByEmail(string $email) : Counterparty {
        return $this->search($email);
    }

    function filter(string $attr, string $query) {
        $url = self::MS_BASE_URL . 'counterparty?filter=' . $attr . '=' . $query;
        $response = $this->client->get($url);
        return $this->decode($response->getBody());
    }

    function search(string $query) {
        $url = self::MS_BASE_URL . 'counterparty?search=' . $query;
        $response = $this->client->get($url);
        return $this->decode($response->getBody());
    }

    function getById(string $id) : Counterparty {
        return $this->getByUrl(self::COUNTERPARTY_BASE_URL . $id);
    }

    function getByUrl(string $url) : Counterparty {
        $response = $this->client->get($url);
        return $this->decode($response->getBody());
    }


    function post(Counterparty $counterparty) {
        $body = $this->encode($counterparty);
        $this->client->post(self::COUNTERPARTY_BASE_URL, ['body' => $body]);
    }

    function put(Counterparty $counterparty) {
        $body = $this->encode($counterparty);
        $this->client->put($counterparty->props['href'], ['body' => $body]);
    }
}