<?php
/**
 * Created by PhpStorm.
 * User: gleb
 * Date: 4/20/18
 * Time: 3:10 PM
 */

namespace dreamwhiteAPIv1;


class VisitorManager
{

    var $visitors = [];
    var $data = [];

    function __construct()
    {
        //$this->visitors = json_decode(file_get_contents("counterparty.json"), true);
        $this->data = json_decode(file_get_contents("map.json"), true);

        //$this->visitors = $this->filterGroups($this->visitors);
    }

    function filterGroups($visitors) {
        $result = [];
        foreach ($visitors as $visitor) {
            if ($visitor['group']['meta']['href'] ===  "https://online.moysklad.ru/api/remap/1.1/entity/group/59c74466-a4ef-11e7-7a69-8f5500021289") {
                $result[] = $visitor;
            }
        }

        return $result;
    }

    function buckets() {

        // anketa-site
        $anketaSite  = [];

        // bucket 1: anketa-site
        foreach ($this->data as $visitor) {
            if ($visitor['tags'] !== null) {
                if (in_array("anketa-site", $visitor['tags']) || in_array("anketa-updated-site", $visitor['tags']) ) {

                    $anketaSite[] = $visitor;
                }
            }
        }
        $registered = $anketaSite;

        print "Registered: " . count($registered) . PHP_EOL;


        $notBought = [];

        //bucket 2: salesAmount
        foreach ($anketaSite as $siteVisitor) {
            if ($siteVisitor['salesAmount'] == 0) {
                //step 3: registered in more than 14 days

                if ($siteVisitor['updated'] !== null) {
                    $updated = strtotime($siteVisitor['updated']);
                    if( $updated < $this->olderThan(14)) {
                        $notBought[] = $siteVisitor;
                    }
                }
                else {
                    $notBought[] = $siteVisitor;
                }
            }
        }
        print "Not bought: " . count($notBought) . PHP_EOL;

        $bought = [];
        $counter = 0;
        //bucket 3: bought
        foreach ($this->data as $visitor) {
            if ($visitor['salesAmount'] > 0 || ($visitor['tags'] !== null && (in_array("anketa", $visitor['tags']) || in_array("anketa-paper", $visitor['tags']))) ) {



                $bought[] = $visitor;
                $counter++;

            }
        }

        print "Bought: " . count($bought) . PHP_EOL;

        //not bought in 90/150 days
        $notBoughtIn90Days = [];
        $notBoughtIn150Days = [];

        foreach ($this->data as $visitor) {

            if ($visitor['lastDemandDate'] !== null) {
                $updated = strtotime($visitor['lastDemandDate']);
                if( $updated < $this->olderThan(90)) {
                    $notBoughtIn90Days[] = $visitor;
                }
                if( $updated < $this->olderThan(150)) {
                    $notBoughtIn150Days[] = $visitor;
                }
            }
            else if ($visitor['salesAmount'] > 0) {
               $notBoughtIn90Days[] = $visitor;
               $notBoughtIn150Days[] = $visitor;
            }

        }

        print "Not bought in 90 days: " . count($notBoughtIn90Days) . PHP_EOL;
        print "Not bought in 150 days: " . count($notBoughtIn150Days) . PHP_EOL;

        //Names
        //Ekaterina
        $ekaterina = [
          "екатерина", "катя", "катерина"
        ];
        $nastya = [
            "анастасия", "настя"
        ];

        $visitorKatyas = [];
        $visitorNastyas = [];


        foreach($this->data as $visitor) {
            if (in_array(mb_strtolower($visitor['name']), $ekaterina)) {
                $visitorKatyas[] = $visitor;
            }
            if (in_array(mb_strtolower($visitor['name']), $nastya)) {
                $visitorNastyas[] = $visitor;
            }
        }

        print "Кати: " . count($visitorKatyas) . PHP_EOL;
        print "Насти: " . count($visitorNastyas) . PHP_EOL;


        // Empty tags + Anna + salesAmount > 0
        $buyersNoTag = [];

        foreach($this->data as $visitor) {
            if ($visitor['tags']!== null) {
                if (empty($visitor['tags']) && $visitor['salesAmount'] > 0) {
                    $buyersNoTag[] = $visitor;
                }
            }
        }

        print "Buyers without tag: " . count($buyersNoTag) . PHP_EOL;

        //birthdays
        $birthdaySoon = [];
        $startDate = date("F d, Y h:i:s", strtotime("today"));
        $endDate = date("F d, Y h:i:s", time()+ $this->days(14));

        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $startDay = (int) $start->format('z');
        $endDay = (int) $end->format('z');

        foreach($this->data as $visitor) {
            if ($visitor['attributes']!== null) {
                if (isset($visitor['attributes']['Дата рождения'])) {
                    $birthday = date("F d, Y h:i:s", strtotime($visitor['attributes']['Дата рождения']));
                    $birthdate = new \DateTime($birthday);
                    $birthday = (int) $birthdate->format('z');

                    if ($birthday >= $startDay && $birthday <= $endDay) {
                        $birthdaySoon[] = $visitor;
                    }
                }
            }
        }

        print "Birthday in 10 days: " . count($birthdaySoon) . PHP_EOL;


        $lists = [
          'registered' => $registered,
          'bought' => $bought,
          'not-bought'  => $notBought,
            'not-bought-90-days' => $notBoughtIn90Days,
            'not-bought-150-days' => $notBoughtIn150Days,
            'katyas' => $visitorKatyas,
            'nastyas' => $visitorNastyas,
            'buyers-no-tags' => $buyersNoTag,
            'birthday-soon' => $birthdaySoon
        ];

        $this->makeLists($lists);

    }

    function days($days) {
        return 86400*$days;
    }
    function olderThan($days) {
        return time()-(86400*$days);
    }

    function save($fileName, $data) {
        $ext = ".txt";
        $dir = "files";
        file_put_contents($dir ."/" . $fileName . $ext, $data);

        //file_put_contents($fileName . $ext, json_encode($data, JSON_UNESCAPED_UNICODE));
    }

    function makeLists($lists) {
        foreach ($lists as $title => $list) {
            $phones = "";

            foreach ($list as $visitor) {
               if (isset($visitor['phone'])) {
                  $phones .= $visitor['phone'] . PHP_EOL;
               }
               
            }

            $this->save($title, $phones);
        }
    }
    
    function isOldClient($tags) {
       return (in_array("anketa", $tags) || in_array("anketa-paper", $tags));
    }
    
    function lastKnownInteraction($visitor) {
       
    }
   
   function lastKnownPurchase($visitor) {
   
   }

    function postToVK() {
        $vk = new \VKApiClient();

        $oauth = new \VKOAuth();
        $client_id = 1234567;
        $redirect_uri = 'https://example.com/vk';
        $display = \VKOAuthDisplay::PAGE;
        $scope = array(\VKOAuthUserScope::WALL, \VKOAuthUserScope::GROUPS);
        $state = 'secret_state_code';

        $browser_url = $oauth->getAuthorizeUrl(\VKOAuthResponseType::CODE, $client_id, $redirect_uri, $display, $scope, $state);


    }
}