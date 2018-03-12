<?php
/**
 * Created by PhpStorm.
 * User: dreamwhite
 * Date: 12.03.2018
 * Time: 18:46
 */

namespace dreamwhiteAPIv1;
require_once "../../../includes.php";

class DataHelper
{
    static function encode($data) {
        $counterparty = new Counterparty();
        $counterparty
            ->id($data['id'])
            ->name($data['client']['name'])
            ->lastName($data['client']['lastName'])
            ->birthday($data['client']['birthday'])
            ->phone($data['client']['phone']['full'])
            ->email($data['client']['email'])
            ->country($data['client']['location']['country'])
            ->city($data['client']['location']['city'])
            ->address($data['client']['location']['address'])
            ->postcode($data['client']['location']['postcode']);

        $source = $data['infoSource']['isCustom'] ? $data['infoSource']['custom'] : $data['infoSource']['source'];
        $counterparty->source($source);
        return $counterparty;
    }
}