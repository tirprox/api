<?php
/**
 * Created by PhpStorm.
 * User: dreamwhite
 * Date: 14.03.2018
 * Time: 12:24
 */

namespace dreamwhiteAPIv1;


class WPUserManager
{


    /**** WooCommerce user_meta fields ****
    billing_first_name
    billing_last_name
    billing_address_1
    billing_city
    billing_state
    billing_postcode
    billing_country
    billing_email
    billing_phone
    */

    function createUser(Counterparty $counterparty)
    {

        $login = $counterparty->props['email'];
        $password = $this->randomPassword(12,5,"lower_case,upper_case,numbers")[0];

        $email = $counterparty->props['email'];
        $phone = $counterparty->props['phone'];

        $firstName = $counterparty->props['name'];
        $lastName = $counterparty->attrs['lastName']['value'];

        $country = '';

        switch ($counterparty->attrs['country']['value'])
        {
            case 'Россия':
                $country = 'RU';
                break;
            case 'Казахстан':
                $country = 'KZ';
                break;
            case 'Беларусь':
                $country = 'BY';
                break;
        }

        //$country = $counterparty->attrs['country']; // probably should fix with code like RU
        $city = $counterparty->attrs['city']['value'];
        $address = $counterparty->attrs['address']['value'];
        $postcode = $counterparty->attrs['postcode']['value'];

        $role = 'customerregistered';

        $userdata = [
            'user_login' => $login,
            'user_email' => $email,
            'user_pass' => $password,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'role' => $role,
        ];


        if ($email !== 'samsonov.gleb@gmail.com') {
            $user = get_user_by('email', $email);

            if (empty($user)) {
                $user_id = wp_insert_user( $userdata ); // creating user with set parameters
            }
            else {
                unset($userdata['user_login']);
                $userdata['ID'] = $user->ID;
                $user_id = wp_update_user($userdata);

            }

            //$user_id = wp_insert_user( $userdata ); // creating user with set parameters

            update_user_meta( $user_id, 'billing_first_name', $firstName );
            update_user_meta( $user_id, 'billing_last_name', $lastName );

            update_user_meta( $user_id, 'billing_country', $country );
            update_user_meta( $user_id, 'billing_city', $city );
            update_user_meta( $user_id, 'billing_address_1', $address );
            update_user_meta( $user_id, 'billing_postcode', $postcode );
            //update_user_meta( $user_id, 'billing_state', 1 );

            update_user_meta( $user_id, 'billing_email', $email );
            update_user_meta( $user_id, 'billing_phone', $phone );


            $this->sendMail($userdata); // email credentials to user
        }


    }

    function updateUser($id, $data)
    {

    }


    function sendMail(array $userdata){
        add_filter( 'wp_mail_from', [__CLASS__, 'my_mail_from'] );
        $to = $userdata['user_email'];
        $subject = 'Регистрация на сайте dreamwhite.ru';
        $body = "Ваши данные для входа: " . "\nЛогин: " . $userdata['user_login'] . "\nПароль: " . $userdata['user_pass'];

        $message = '<html><body>';
        $message .= '<h3>Ваши данные для входа:</h3>';
        $message .= '<p>Логин: ' . $userdata['user_login'] . '</p>';
        $message .= '<p>Пароль: ' . $userdata['user_pass'] . '</p>';
        $message .= '<p><a href="https://dreamwhite.ru/my-account/">Ссылка для входа</a></p>';
        $message .= '</body></html>';

        $headers = [
            'Content-Type: text/html; charset=UTF-8',
            'From: DreamWhite <info@dreamwhite.ru>',
            'MIME-Version: 1.0',
            'Content-Type: text/html'
        ];

        wp_mail($to,$subject,$message,$headers);
    }

    function my_mail_from( $email ) {
        return "info@dreamwhite.ru";
    }

    function randomPassword($length, $count, $characters)
    {
        // $length - the length of the generated password
        // $count - number of passwords to be generated
        // $characters - types of characters to be used in the password

        $symbols = array();
        $passwords = array();
        $used_symbols = '';
        $pass = '';

        // an array of different character types
        $symbols["lower_case"] = 'abcdefghijklmnopqrstuvwxyz';
        $symbols["upper_case"] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $symbols["numbers"] = '1234567890';
        $symbols["special_symbols"] = '!?~@#-_+<>[]{}';

        $characters = explode(",", $characters); // get characters types to be used for the passsword
        foreach ($characters as $key => $value) {
            $used_symbols .= $symbols[$value]; // build a string with all characters
        }
        $symbols_length = strlen($used_symbols) - 1; //strlen starts from 0 so to get number of characters deduct 1

        for ($p = 0; $p < $count; $p++) {
            $pass = '';
            for ($i = 0; $i < $length; $i++) {
                $n = rand(0, $symbols_length); // get a random character from the string with all characters
                $pass .= $used_symbols[$n]; // add the character to the password string
            }
            $passwords[] = $pass;
        }

        return $passwords; // return the generated passwordЙ
    }
}