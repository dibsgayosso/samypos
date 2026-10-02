<?php

class Mx_connect extends CI_Model
{
    public $uri;
    public $connection_key;
    public $username;
    public $password;
    public $domainId;
    
    private $token; // This gets set in the authenticate function
    
    public function __construct()
    {
        
        $this->uri = "https://api.mxconnect.com/";
        
        // API Key from the MX Connect portal (must come from a user)
        $this->connection_key = "API_f62a14fe3476975110168d30eaec2a9081e1fda3e414d115";
        
        // Currently using Ezra's username and password. If he changes it, it MUST be changed here as well
        $this->username = "ZXpyYS53ZWluc3RlaW5AY29yZXdhcmUuY29t";
        $this->password = "MlRoZUNvcmUmQmV5b25k";
        
        $this->domainId = "9e7a8491-d5ff-5a1c-85cf-2d2f8ae490d5";
    }
    
    public function authenticate()
    {
        $endpoint = "security/v1/apiKey/authenticate";
        
        $api_data = ["value" => $this->connection_key];
        
        $response = $this->send_request($endpoint, "POST", $api_data);
        
        if ($response['success'] == true) {
            $this->token = $response['data']->token;
            return true;
        }
        else {
            return false;
        }
    }
    
    public function get_statement_detail($statement_id, $statement_type)
    {
        if (!$this->authenticate()) return [];
        
        $endpoint = "statement/v1/statementV2/mxc/$statement_id?statementType=$statement_type";
        
        return $this->send_request($endpoint);
    }
    
    public function get_statements($account_numbers, $date_range)
    {
        if (!$this->authenticate()) return [];
        
        $filter = [
            "must" => [
                [
                    "terms" => [
                        "accountNumber" => $account_numbers
                    ]
                ],
                [
                    "bool" => [
                        "should"               => [],
                        "minimum_should_match" => 1
                    ]
                ]
            ]
        ];
        
        $params = [
            "domainId" => $this->domainId,
            "dr_type"  => 'q',
            "dr_quick" => $date_range,
            "filter"   => json_encode($filter),
            "size"     => '100',
            "sort"     => 'statementDate:desc'
        ];
        
        $query = http_build_query($params);
        $endpoint = "statement/v1/statements?" . $query;
        
        return $this->send_request($endpoint);
    }
    
    public function get_stats($account_numbers, $date_range)
    {
        if (!$this->authenticate()) return [];
        
        $filter = [
            "must" => [
                [
                    "terms" => [
                        "accountNumber" => $account_numbers
                    ]
                ],
                [
                    "bool" => [
                        "should"               => [],
                        "minimum_should_match" => 1
                    ]
                ]
            ]
        ];
        
        $params = [
            "domainId" => $this->domainId,
            "dr_type"  => 'q',
            "dr_quick" => $date_range,
            "filter"   => json_encode($filter)
        ];
        
        $query = http_build_query($params);
        $endpoint = "statement/v1/statementsV2?" . $query;
        
        return $this->send_request($endpoint);
    }
    
    public function get_disputes($account_numbers, $date_range)
    {
        if (!$this->authenticate()) return [];
        
        $filter = [
            "must" => [
                [
                    "terms" => [
                        "accountNumber" => $account_numbers
                    ]
                ],
                [
                    "bool" => [
                        "should"               => [],
                        "minimum_should_match" => 1
                    ]
                ]
            ]
        ];
        
        $params = [
            "dr_type"  => 'q',
            "dr_quick" => $date_range,
            "filter"   => json_encode($filter)
        ];
        
        $query = http_build_query($params);
        $endpoint = "report/v1/tsys/disputes?" . $query;
        
        return $this->send_request($endpoint);
    }
    
    public function get_ach_funding($account_numbers, $start_date, $end_date, $sort = 'depositDate', $limit = 10, $offset = 0)
    {
        if (!$this->authenticate()) return [];
        
        $filter = [
            "must" => [
                [
                    "terms" => [
                        "accountNumber" => $account_numbers
                    ]
                ],
                [
                    "bool" => [
                        "should"               => [],
                        "minimum_should_match" => 1
                    ]
                ]
            ]
        ];
        
        $params = [
            "dr_type" => 'abs',
            "dr_from" => $start_date,
            "dr_to"   => $end_date,
            "filter"  => json_encode($filter),
            "sort"    => $sort,
            "size"    => (int)$limit,
            "from"    => (int)$offset
        ];
        
        $query = http_build_query($params);
        $endpoint = "report/v1/tsys/ach?" . $query;
        
        return $this->send_request($endpoint);
    }
    
    private function send_request($endpoint, $method = "GET", $data = null)
    {
        $api_json_data = $data ? json_encode($data) : null;
        
        $curl = curl_init();
        
        $headers = [
            "Content-Type: application/json",
            "cache-control: no-cache"
        ];
        
        if (!empty($this->token)) {
            $headers[] = "Authorization: Bearer $this->token";
        }
        
        curl_setopt_array($curl, array(
            CURLOPT_URL            => $this->uri . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => "",
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 90,
            CURLOPT_CONNECTTIMEOUT => 1,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_POSTFIELDS     => $api_json_data,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_USERPWD        => "$this->username:$this->password"
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        $total_time = curl_getinfo($curl, CURLINFO_TOTAL_TIME) * 1000;
        
        curl_close($curl);
        
        $response_data = json_decode($response);
        
        $log_params = $api_json_data ?: $endpoint;
        
        if (!$response_data || $err) {
            $error_message = $err ?: lang('common_can_not_connect');
            return array('success' => false, 'error_message' => lang('common_can_not_connect'));
        }
        else {
            if (isset($response_data->code)) {
                return array('success' => false, 'error_message' => $response_data->message);
            }
            else {
                return array('success' => true, 'data' => $response_data);
            }
        }
    }
}

?>