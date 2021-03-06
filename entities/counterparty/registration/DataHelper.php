<?php

namespace dreamwhiteAPIv1;
//require "../includes.php";

class DataHelper
{
    static function encode($data, $tag) {
        $counterparty = new Counterparty();

        $counterparty
            ->id($data['id'])
            ->addTag($tag)
            ->name($data['client']['name'])
            ->lastName($data['client']['lastName'] ?? "")
            ->birthday($data['client']['birthday'] ?? "")
            ->phone($data['client']['phone']['full'] ?? "")
            ->email($data['client']['email'] ?? "")
            ->country($data['client']['location']['country'] ?? "")
            ->city($data['client']['location']['city'] ?? "")
            ->address($data['client']['location']['address'] ?? "")
            ->postcode($data['client']['location']['postcode'] ?? "")
            ->promoCode($data['client']['promoCode'] ?? "")
            ->metricaClientId($data['client']['metricaClientId'] ?? "")
          ->gaClientId($data['client']['gaClientId'] ?? "")
          ->utmstatClientId($data['client']['utmstatClientId'] ?? "");

        $source = $data['infoSource']['isCustom'] ? $data['infoSource']['custom'] : $data['infoSource']['source'];
        $counterparty->source($source);
        $counterparty->feedback($data['feedback']);

        //echo json_encode($counterparty,JSON_UNESCAPED_UNICODE);
        return $counterparty;
    }
}