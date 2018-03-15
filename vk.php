<?php

namespace vkApi;

class vk {
    private $token;
    private $count = -1;
    private static $class = null;

    public static function create($token){
        if(!self::$class){
            self::$class = new vk($token);
        }
        return self::$class;
    }

    private function __clone(){}
    private function __construct($token){
        $this->token = $token;
    }

    function get($method, array $data){
        $this->count ++;
        if($this->count >= 3){
            $this->count = 0;
            sleep(1);
        }
        $params = array();
        foreach($data as $name => $val){
            $params[$name] = $val;
        }
        $params['access_token'] = $this->token;
        $params['v'] = '3.0';
        $json = file_get_contents('https://api.vk.com/method/' . $method . '?' . http_build_query($params));
        return json_decode($json);
    }
}

