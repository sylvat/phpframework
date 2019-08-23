<?php

if (! function_exists('config')) {
    /**
     * 获取配置
     * @param string $item 例如config('sys');config('application.debug');
     * @return bool
     */
    function config($item = '')
    {
        static $config = null;
        if ($config == null)
        {
            $di = Phalcon\Di::getDefault();
            $config = $di->get('config')->toArray();
        }
        if(!empty($item)) {
            $value = $config;
            $itemArr = explode('.', $item);
            foreach($itemArr as $v) {
                if(!isset($value[$v])) {
                    return false;
                }
                $value = $value[$v];
            }
            return $value;
        }
        return $config;
    }
}

if (! function_exists('truncation')) {
    /**
     * 字符串截断
     * @param $text
     * @param $length
     * @return string
     */
    function truncation($text, $length)
    {
        if (mb_strlen($text, 'utf8') > $length)
            return mb_substr($text, 0, $length, 'utf8') . '...';
        return $text;
    }
}

if (! function_exists('formatNumber')) {
    /**
     * 数字转化
     * @param $number
     * @return string
     */
    function formatNumber($number) {
        $units = array('', '万', '亿');
        for ($i = 0; $number >= 10000 && $i < 3; $i++) $number /= 10000;
        return round($number, 1).$units[$i];
    }
}

if (! function_exists('getMicroTime')) {
    /**
     * 获取毫秒时间戳
     * @return float
     */
    function getMicroTime()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }
}


if (! function_exists('safeCheck')) {
    /**
     * 安全检查
     * @param $str
     * @return string
     */
    function safeCheck($str)
    {
        $str = trim($str);

        if (!get_magic_quotes_gpc()) {
            $str = addslashes($str);
        }
        $str = str_replace('%20', '', $str);
        $str = str_replace('%27', '', $str);
        $str = str_replace('%2527', '', $str);

        return $str;
    }
}


if (! function_exists('safeFilter')) {
    /**
     * 安全过滤
     * @param $params
     * @return string | array
     */
    function safeFilter($params)
    {
        $filter = null;
        if (is_array($params)) {
            foreach ($params as $key => $val) {
                $filter[safeCheck($key)] = safeFilter($val);
            }
        } else {
            $filter = safeCheck($params);
        }

        return $filter;
    }
}

if (! function_exists('clientIP')) {
    /**
     * 获取客户端IP
     * @return string
     */
    function clientIP()
    {
        $cIP = getenv('REMOTE_ADDR');
        $cIP1 = getenv('HTTP_X_FORWARDED_FOR');
        $cIP2 = getenv('HTTP_CLIENT_IP');
        $cIP1 ? $cIP = $cIP1 : '0';
        $cIP2 ? $cIP = $cIP2 : '0';
        return $cIP;
    }
}

if (! function_exists('serverIP')) {
    /**
     * 获取服务器IP
     * @return string
     */
    function serverIP()
    {
        return @gethostbyname($_SERVER["SERVER_NAME"]);
    }
}








