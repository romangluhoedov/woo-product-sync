<?php

namespace nongkuschoolubol;

class SunlightsuplyApi
{
    const URL = 'https://services.sunlightsupply.com/v1/';

    protected $apiKey;
    protected $apiSecret;
    protected $errors = [];

    public function __construct($apiKey, $apiSecret)
    {
        $this->apiKey       = $apiKey;
        $this->apiSecret    = $apiSecret;
    }

    public function sendRequest($query, $format = 'json')
    {
        $queryInfo  = parse_url($query);
        $queryParams= [];

        if (!empty($queryInfo['query']))
            parse_str($queryInfo['query'], $queryParams);

        $queryParams['format']  = $format;
        $queryParams['X-ApiKey']= $this->apiKey;
        $queryParams['time']    = gmdate("Y-m-d\TH:i:s\Z");

        $queryInfo['query'] = urldecode(http_build_query($queryParams));

        $url = self::URL . $queryInfo['path'] . '?' . $queryInfo['query'];
        $url .= '&signature=' . strtoupper(
            hash_hmac("sha256", $url, $this->apiSecret)
        );

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $result = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        switch ($code) {
            case 401:
                $this->errors[] = 'API authentication error';
        }

        return json_decode($result, true);
    }

    public function getSingleProducts()
    {
        return $this->sendRequest('part');
    }

    public function getFamilies()
    {
        return $this->sendRequest('partfamily');
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function hasErrors()
    {
        return !empty($this->errors);
    }
}