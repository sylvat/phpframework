<?php

namespace App\Utils;

/**
 * @Desc HTTP Request
 * @Author tangqin
 * @Date 2016/8/12
 * @Time 15:19
 */
class Http
{
    public static $connectTimeOut = 5;

    public static $timeOut = 5;

    /**
     * Make a POST request.
     *
     * @param string $url A request url like "https://example.com".
     * @param array $data An array to make query string like "example1=&example2="
     * @param string $responseType
     *
     * @return API results.
     */
    public static function post($url, $data = [], $responseType = 'json')
    {
        $query = self::buildHttpQuery($data);

        $response = self::makeRequest($url, 'POST', $query);

        self::responseHandle($response, $responseType);

        return $response;
    }

    /**
     * Make a POST request.
     *
     * @param string $url A request url like "https://example.com".
     * @param array $data An Array to make query string like "{"param1":"a","param1":"b"}"
     * @param string $responseType
     *
     * @return API results.
     */
    public static function postRaw($url, $data = [], $responseType = 'json')
    {
        $response = self::makeRequest($url, 'POST_RAW', json_encode($data));

        self::responseHandle($response, $responseType);

        return $response;
    }

    /**
     * Make a GET request.
     *
     * @param string $url A request url like "https://example.com".
     * @param array $data An array to make query string like "example1=&example2=" .
     * @param string $responseType
     * @return API results.
     */
    public static function get($url, $data = [], $responseType = 'json')
    {
        if (!empty($data)) {
            $url .= "?" . self::buildHttpQuery($data);
        }
        $response = self::makeRequest($url, 'GET');

        self::responseHandle($response, $responseType);

        return $response;
    }

    /**
     * Handle response
     *
     * @param $response
     * @param $responseType
     */
    protected static function responseHandle(&$response, $responseType)
    {
        if ('json' === $responseType) {
            $response = json_decode($response, true);
        } elseif('xml' === $responseType) {
            $response = json_decode(json_encode(simplexml_load_string($response)), true);
        }
    }
    /**
     * Make a HTTP request.
     *
     * @param string $url A request url like "https://example.com/xx.json?example1=&example2=".
     * @param string $requestType Request method is "GET" or "POST".
     * @param string $postFields A query string post to $url.
     *
     * @return API results.
     */
    public static function makeRequest($url, $requestType, $postFields = NULL)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ('POST' === $requestType || 'POST_RAW' === $requestType) {
            curl_setopt($ch, CURLOPT_POST, 1);
            if (!empty($postFields)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
            }
            if ('POST_RAW' === $requestType) {
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json; charset=utf-8',
                        'Content-Length: ' . strlen($postFields))
                );
            }
        }
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, self::$connectTimeOut);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::$timeOut);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }

    /**
     * Build HTTP Query.
     *
     * @param array $params
     *
     * @return string
     */
    public static function buildHttpQuery(array $params)
    {
        $query = http_build_query($params);
        return $query;
    }

}
