<?php
/**
 * Created by PhpStorm.
 * User: dreamwhite
 * Date: 06.03.2018
 * Time: 16:06
 */

namespace dreamwhiteAPIv1;
//require_once "../../includes.php";

class CounterpartyManager {
    const COUNTERPARTY_BASE_URL = "https://online.moysklad.ru/api/remap/1.1/entity/counterparty/";
    const MS_BASE_URL = "https://online.moysklad.ru/api/remap/1.1/entity/";
    const MS_REPORT_URL = "https://online.moysklad.ru/api/remap/1.1/report/counterparty";
    private $client;

    function __construct() {
        $connector = new Connector();
        $this->client = $connector->client;
    }

    //returns encoded json string ready to be sent
    function encode(Counterparty $counterparty): string {

        $encoded = [];

        $encoded['tags'] = $counterparty->props['tags'];
        $encoded['companyType'] = "individual";
        $encoded['group'] = [
            "meta" => [
                "href" => "https://online.moysklad.ru/api/remap/1.1/entity/group/59c74466-a4ef-11e7-7a69-8f5500021289",
                "metadataHref" => "https://online.moysklad.ru/api/remap/1.1/entity/group/metadata",
                "type" => "group",
                "mediaType" => "application/json"
            ]
        ];

        if ($counterparty->props["name"] !== '') $encoded["name"] = $counterparty->props["name"];
        if ($counterparty->props["phone"] !== '') $encoded["phone"] = $counterparty->props["phone"];
        if ($counterparty->props["email"] !== '') $encoded["email"] = $counterparty->props["email"];

        $a = [];

        foreach ($counterparty->attrs as $name => $parameters) {
            if ($parameters['value'] !== '' && $parameters['value'] !== null) {
                $a[] = [
                    'id' => $parameters['id'],
                    'name' => array_search($name, Counterparty::$attrAlias),
                    'type' => $parameters['type'],
                    'value' => $parameters['value']
                ];

            }

        }

        $encoded['attributes'] = $a;

        //echo json_encode($encoded,JSON_UNESCAPED_UNICODE);
        file_put_contents('last_encoded.json', json_encode($encoded, JSON_UNESCAPED_UNICODE));
        return json_encode($encoded, JSON_UNESCAPED_UNICODE);

    }

    function decode($json) /*: Counterparty*/ {
        $json = json_decode($json, true);
        $counterparty = new Counterparty();

        /* When accessing a counterparty by id, it doesn't have rows, but it returns rows on search and filter */
        $data = [];
        if (isset($json['rows'])) {
            if (empty($json['rows'])) return $counterparty;
            $data = $json['rows'][0];
        }
        else {
            $data = $json;
        }

        $counterparty->raw = $json;

        // Encoding props
        foreach ($counterparty->props as $key => $value) {
            if ($key === 'owner' || $key === 'group') {
                $counterparty->props[$key] = $data[$key]['meta']['href'] ?? '';
            }
            else {
                $counterparty->props[$key] = $data[$key] ?? '';
            }

        }

        $counterparty->id($data['id']);

        // Encoding attributes
        if (array_key_exists('attributes', $data)) {
            foreach ($data['attributes'] as $attr) {
                if ($attr['value'] !== '') {
                    $a = [
                        'id' => $attr['id'],
                        'value' => $attr['value'],
                        'type' => $attr['type'],
                    ];
                    $counterparty->attrs[Counterparty::$attrAlias[$attr['name']]] = $a;
                }

            }
        }

        return $counterparty;
    }

    function getByPhone(string $phone): Counterparty {
        return $this->search($phone);
    }

    function getByEmail(string $email): Counterparty {
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

    function getAll() {
        $limitedUrl = self::MS_BASE_URL . "counterparty?limit=100";

        $responses = [];
        $response = $this->client->get($limitedUrl);
        $response = json_decode($response->getBody(), true);
        $size = $response['meta']['size'];
        $responses = array_merge($response['rows'], $responses);

        $offset = 100;

        $requests = [];

        while ($offset < $size) {
            //print $offset . " ";
            $url = $limitedUrl . "&offset=" . $offset;
            $requests[] = new \GuzzleHttp\Psr7\Request('GET', $url);
            $offset += 100;
        }
        $counter = 0;

        $pool = new \GuzzleHttp\Pool($this->client, $requests, [
            'concurrency' => 5,
            'fulfilled' => function ($response, $index) use (&$responses, $counter) {
                $response = json_decode($response->getBody(), true);
                print $response['meta']['href'] . "\n";
                $responses = array_merge($responses, $response['rows']);

                // this is delivered each successful response
            },
            'rejected' => function ($reason, $index) {
                // this is delivered each failed request
            },
        ]);

        $pool->promise()->wait();

        print count($responses) . " \n";
        return $responses;
    }

    function getAllReports() {
        $limitedUrl = self::MS_REPORT_URL . "?limit=100";

        $responses = [];
        $response = $this->client->get($limitedUrl);
        $response = json_decode($response->getBody(), true);
        $size = $response['meta']['size'];
        $responses = array_merge($response['rows'], $responses);

        $offset = 100;

        $requests = [];

        while ($offset < $size) {
            //print $offset . " ";
            $url = $limitedUrl . "&offset=" . $offset;
            $requests[] = new \GuzzleHttp\Psr7\Request('GET', $url);
            $offset += 100;
        }
        $counter = 0;

        $pool = new \GuzzleHttp\Pool($this->client, $requests, [
            'concurrency' => 5,
            'fulfilled' => function ($response, $index) use (&$responses, $counter) {
                $response = json_decode($response->getBody(), true);
                print $response['meta']['href'] . "\n";
                $responses = array_merge($responses, $response['rows']);

                // this is delivered each successful response
            },
            'rejected' => function ($reason, $index) {
                // this is delivered each failed request
            },
        ]);

        $pool->promise()->wait();

        print count($responses) . " \n";
        return $responses;
    }

    function getById(string $id): Counterparty {
        return $this->getByUrl(self::COUNTERPARTY_BASE_URL . $id);
    }

    function getByUrl(string $url): Counterparty {
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