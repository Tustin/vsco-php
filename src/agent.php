<?php

abstract class Agent {

    const URL = "https://api.vsco.co/2.0/";

    private static $CURL_OPTIONS = [
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_RETURNTRANSFER => TRUE,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_USERAGENT => 'VSCO/3635 CFNetwork/758.0.2 Darwin/15.0.0',
    ];

    //lol
    private static function buildBountrySegment($delimiter, $name, $content) {
        return "\nContent-Disposition: form-data; name=\"" . $name . "\"\n\n" . $content . "\n--" . $delimiter;
    }

    public static function get($endpoint, $auth_token = null) {
        $ch = curl_init();

        $options = self::$CURL_OPTIONS + [
            CURLOPT_URL => self::URL . $endpoint,
        ];

        if ($auth_token != null) {
            $options[CURLOPT_HTTPHEADER][] = 'Authorization: Bearer ' . $auth_token;
        }

        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);

        $result = json_decode($result);

        if ($result === false || curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            curl_close($ch);
            throw new Exception($result->errorType);
        }

        curl_close($ch);

        return $result;
    }


    public static function post($endpoint, $data, $boundry = null, $auth_token = null) {
        $ch = curl_init();

        $post_data = "";

        if ($boundry != null) {
            $post_data = "--{$boundry}";
            foreach ($data as $key => $item) {
                $post_data .= self::buildBountrySegment($boundry, $key, $item);
            }
        }

        echo $post_data;

        $options = self::$CURL_OPTIONS + [
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $boundry ? $post_data : json_encode($data),
            CURLOPT_URL => self::URL . $endpoint,
        ];

        if ($auth_token != null) {
            $options[CURLOPT_HTTPHEADER][] = 'Authorization: Bearer ' . $auth_token;
        } else {
            $options[CURLOPT_HTTPHEADER][] = 'Authorization: Basic dnV6ZWRhemViZWp1Z2UzeWh1Z3k5ZXFlbWE1YTV1cmU5dTJ1c2FyYTpyeWplNXlydXBlemUyZXN5YmVyeQ==';
        }

        if (!$boundry) {
            $options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
        } else {
            $options[CURLOPT_HTTPHEADER][] = 'Content-Type: multipart/form-data; boundary=' . $boundry;
        }

        curl_setopt_array($ch, $options);

        $result = curl_exec($ch);

        $result = json_decode($result);

        if ($result === false || curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
            curl_close($ch);
            throw new Exception($result->errorType);
        }

        curl_close($ch);

        return $result;
    }
}