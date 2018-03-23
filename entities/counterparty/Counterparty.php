<?php
namespace dreamwhiteAPIv1;

//require_once "../../includes.php";

class Counterparty
{
    const COUNTERPARTY_BASE_URL = "https://online.moysklad.ru/api/remap/1.1/entity/counterparty/";

    public $raw = [];

    // Hardcoded into MS
    public $props = [
        'id' => '',
        'href' => '',
        'name' => '',
        'phone' => '',
        'email' => '',
        'tags' => [],
        'owner' => '',
        'group' => ''
    ];


    // DreamWhite-specific, could change

    public static $attrIDs = [
        'lastName' => 'c6597688-cf9b-11e7-7a6c-d2a9000ec13c',
        'birthday' => 'c5be5aa5-f79a-11e7-7a69-9711000e03aa',
        'country' => 'f2e3ea7d-1e11-11e8-9107-50480007765e',
        'city' => 'f2e3f108-1e11-11e8-9107-50480007765f',
        'address' => 'f2e3f460-1e11-11e8-9107-504800077660',
        'postcode' => 'f2e3f7d2-1e11-11e8-9107-504800077661',
        'facebook' => 'f7acf60c-fc67-11e7-7a31-d0fd000e939f',
        'dateRegistered' => 'fe06e948-d034-11e7-7a34-5acf0006a4c3',
        'source' => 'fe06e4f2-d034-11e7-7a34-5acf0006a4c2',
        'feedback' => 'b3d9786a-d361-11e7-7a6c-d2a9001aff01',
    ];

    public static $attrAlias = [
        'Фамилия' => 'lastName',
        'Дата рождения' => 'birthday',
        'Страна' => 'country',
        'Город' => 'city',
        'Адрес' => 'address',
        'Почтовый индекс' => 'postcode',
        'Facebook' => 'facebook',
        'Дата регистрации анкеты' => 'dateRegistered',
        'Источник' => 'source',
        'Отзыв' => 'feedback',
    ];

    public $attrs = [];

    function __construct(string $name = null, string $phone = null, string $email = null)
    {
        $this->props['name']  = $name;
        $this->props['phone']  = self::prepare_phone($phone);
        $this->props['email']  = $email;
    }

    function addTag(string $tag) {
        $this->props['tags'][] = $tag;
        return $this;
    }

    function tags(array $tags = null)  {
        if ($tags != null) {
            $this->props['tags'] = $tags;
            return $this;
        }
        else {
            return $this->props['tags'];
        }
    }

    function removeTag(string $tag)  {
        if (in_array($tag, $this->props['tags']))
        {
            unset($this->props['tags'][array_search($tag,$this->props['tags'])]);
        }
        return $this;
    }

    function raw($raw = null) {
        if ($raw===null) {
            return $this->raw;
        }
        $this->raw = $raw;
        return $this;
    }

    function id($id = null) {
        if ($id===null) {
            return $this->props['id'];
        }
        $this->props['id'] = $id;
        $this->props['href'] = self::COUNTERPARTY_BASE_URL . $id;
        return $this;
    }

    function href($href = null) {

        // Should not be called for setting href ever! only to get a value

        if ($href===null) {
            return $this->props['href'];
        }
        $this->props['href'] = $href;
        return $this;
    }


    function name($name = null) {
        if ($name===null) {
            return $this->props['name'];
        }
        $this->props['name'] = $name;
        return $this;
    }

    function phone($phone = null) {
        if ($phone===null) {
            return $this->props['phone'];
        }
        $this->props['phone'] = $phone;
        return $this;
    }

    function email($email = null) {
        if ($email===null) {
            return $this->props['email'];
        }
        $this->props['email'] = $email;
        return $this;
    }

    function owner($ownerUrl = null) {
        if ($ownerUrl===null) {
            return $this->props['owner'];
        }
        $this->props['owner'] = $ownerUrl;
        return $this;
    }

    function group($groupUrl = null) {
        if ($groupUrl===null) {
            return $this->props['group'];
        }
        $this->props['group'] = $groupUrl;
        return $this;
    }


    /*---------------------------------------------*/

    function stringAttr(string $name, $value) {
        if ($value === null) {
            return $this->attrs[$name]['value'];
        }
        else {
            $this->attrs[$name] = [
                "id" =>  Counterparty::$attrIDs[$name],
                "type" =>  "string",
                "value" => $value
            ];
            return $this;
        }

    }

    function timeAttr(string $name, $date = null, string $time = '12:00:00') {
        if ($date === null) {
            return $this->attrs[$name]['value'];
        }
        else if ($date!=='') {
            $this->attrs[$name] = [
                "id" =>  Counterparty::$attrIDs[$name],
                "type" =>  "string",
                "value" => self::prepare_time($date, $time),
            ];
            return $this;
        }
        else {
            /*$this->attrs[$name] = [
                "id" =>  Counterparty::$attrIDs[$name],
                "type" =>  "string",
                "value" => self::prepare_time('', $time),
            ];*/
            return $this;
        }
    }

    /*---------------------------------------------*/

    function lastName(string $arg = null) {
        $fname = __FUNCTION__;
        return $this->stringAttr($fname, $arg);
    }

    function source(string $arg = null) {
        $fname = __FUNCTION__;
        return $this->stringAttr($fname, $arg);
    }

    function feedback(string $arg = null) {
        $fname = __FUNCTION__;
        return $this->stringAttr($fname, $arg);
    }

    function dateRegistered(string $arg = null, string $time = '12:00:00') {
        $fname = __FUNCTION__;
        return $this->timeAttr($fname, $arg, $time);
    }

    function birthday(string $arg = null, string $time = '12:00:00') {
        $fname = __FUNCTION__;

        return $this->timeAttr($fname, $arg, $time);
    }

    function facebook(string $arg = null) {
        $fname = __FUNCTION__;
        return $this->stringAttr($fname, $arg);
    }

    function country(string $arg = null) {
        $fname = __FUNCTION__;
        return $this->stringAttr($fname, $arg);
    }

    function city(string $arg = null) {
        $fname = __FUNCTION__;
        return $this->stringAttr($fname, $arg);
    }

    function address(string $arg = null) {
        $fname = __FUNCTION__;
        return $this->stringAttr($fname, $arg);
    }

    function postcode(string $arg = null) {
        $fname = __FUNCTION__;
        return $this->stringAttr($fname, $arg);
    }


    /***************** SMS verification part *******************/
    var $allowedOwners = [
        'https://online.moysklad.ru/api/remap/1.1/entity/employee/57e00517-e00e-11e6-7a69-9711001f6490'// Анна - Флигель
    ];


    const ANNA_FLIGEL = 'https://online.moysklad.ru/api/remap/1.1/entity/employee/57e00517-e00e-11e6-7a69-9711001f6490';

    function isSMS() {
        //return in_array($this->props['owner'], $this->allowedOwners);
        return self::ANNA_FLIGEL===$this->props['owner'];
    }

    /*---------------------------------------------*/

    static function prepare_phone($phone): string {
        //$phone = str_replace("+", "", $phone);
        $phone = str_replace("-", "", $phone);
        $phone = str_replace(" ", "", $phone);
        return $phone;
    }

    static function prepare_time($date, $time): string {
        $dateArray = date_parse_from_format("j.n.Y", $date);
        //$dateArray = date_parse($date);
        $timeString = $dateArray['year']. "-" . $dateArray['month']. "-" . $dateArray['day'] . " " . $time;
        return $timeString;
    }


}