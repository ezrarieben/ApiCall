<?php

namespace ezrarieben;

/**
* This class is used to make a JSON REST API call
*
* @var $curl --> The cURL session
* @var $result --> API Result
* @var $responseCode --> HTTP response code
*/
class ApiCall
{
    private $curl;

    private $response;
    private $responseCode;

    /**
    * Constructor
    *
    * @param string $url --> URL to call
    * @param array $data --> Data to send to API via POST
    * @param bool $dataViaGET --> Send the data via GET instead? (default: false)
    *
    * @return ApiCall
    */
    public function __construct(string $url = '', array $data = array(), bool $dataViaGET = false)
    {
        $this->curl = curl_init();

        if (!empty($data)) {
            if (!$dataViaGet) {
                curl_setopt($this->curl, CURLOPT_POST, 1);
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($this->curl, CURLOPT_URL, $url);
            } elseif ($dataViaGET) {
                $query = http_build_query($params);
                curl_setopt($ch, CURLOPT_URL, "{$url}?{$query}");
            }
        }

        curl_setopt($this->curl, CURLOPT_HEADER, 1);
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);

        // Get API response
        $this->response = curl_exec($this->curl);

        // Get response HTTP code from API
        $this->responseCode = curl_getinfo($this->curl, CURLINFO_RESPONSE_CODE);
    }

    /**
    * Getter function for API request result
    *
    * @return string --> API Response
    */
    public function getResponse(): String
    {
        // Remove header from response
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $response = substr($this->response, $headerSize);

        return $response;
    }

    /**
    * Getter function for API HTML response code
    *
    * @return int --> HTML response code as int
    */
    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    /**
      * This function gets the header from the cURL request
      * NOTE: Requires 'CURLOPT_HEADER' to be set to true
      * Thanks to: Markus Knappen Johansson on StackOverflow --> https://stackoverflow.com/questions/10589889/returning-header-as-array-using-curl
      *
      * @param $ch --> cURL call reference
      * @param $response --> Response from curl_exec()
      *
      * @return array --> Array of headers
      */
    public function getHeader(): array
    {
        $headerSize = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
        $header = substr(($this->response, 0, $headerSize);
        $headers = array();
        $arrRequests = explode("\r\n\r\n", $header);
        for ($index = 0; $index < count($arrRequests) -1; $index++) {
            foreach (explode("\r\n", $arrRequests[$index]) as $i => $line) {
                if ($i === 0) {
                    $headers[$index]['http_code'] = $line;
                } else {
                    list($key, $value) = explode(': ', $line);
                    $headers[$index][$key] = $value;
                }
            }
        }

        return $headers;
    }

    /**
    * This function closes the open cURL session
    *
    * @return Void
    */
    public function close(): Void
    {
        curl_close($this->curl);
    }
}
