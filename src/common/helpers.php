<?php

if (!function_exists('is_fpm_mode')) {
    /**
     * 是否运行在fpm模式下
     *
     * @return bool
     */
    function is_fpm_mode()
    {
        return substr(php_sapi_name(), 0, 3) == 'fpm';
    }
}

if (!function_exists('access_header')) {
    /**
     * 获取跨域头
     *
     * @return array
     */
    function access_header()
    {
        return [
            'Allow' => 'GET, PUT, PATCH, DELETE, HEAD, OPTIONS',
            'Access-Control-Allow-Origin' => \Illuminate\Support\Facades\Request::server('HTTP_ORIGIN'),
            'Access-Control-Allow-Methods' => 'GET, PUT, PATCH, DELETE, HEAD, OPTIONS',
            'Access-Control-Allow-Headers' => 'Authorization, Content-Type, device, AppType',
        ];
    }
}

if (!function_exists('output')) {
    /**
     * 数据格式化
     *
     * @param  mixed $data
     * @param  int $code
     * @param  string $msg
     * @param  array $args
     * @return array
     *
     */
    function output($data, $code = 0, $msg = '', $args = [])
    {
        $ret = [
            'code' => $code,
            'msg' => $msg == '' ? __('code.' . $code, $args) : $msg,
            'data' => $data,
        ];
        if (env('APP_DEBUG') == true) {
            $ret['sql_log'] = \Illuminate\Support\Facades\DB::getQueryLog();
            \Illuminate\Support\Facades\DB::flushQueryLog();
        }
        if ($ret['data'] === '') {
            unset($ret['data']);
        }
        return $ret;
    }
}

if (!function_exists('error_exit')) {
    /**
     * 自定义错误
     *
     * @param  int $code
     * @param  string|array $data
     *
     */
    function error_exit($code, $data = '', $extends = [])
    {
        throw new \App\Exceptions\CustomException($code, $data, $extends);
    }
}

if (!function_exists('custom_resp')) {
    /**
     * 返回自定义响应
     *
     * @param  mixed $data
     * @param  int $status
     *
     */
    function custom_resp($data, $status = 200)
    {
        throw new \App\Exceptions\CustomException($status, $data, [], true);
    }
}

if (!function_exists('salt')) {
    /**
     * 产生不重复的随机数
     *
     * @param int $len
     * @return bool|string
     */
    function salt($len = 6)
    {
        $string = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $string = str_shuffle($string);

        return substr($string, 0, $len);
    }
}

if (!function_exists('password_encrypt')) {
    function password_encrypt($password, $salt)
    {
        return md5(md5($password) . $salt);
    }
}

if (!function_exists('get_page_data')) {
    /**
     * @param Illuminate\Database\Query\Builder $query
     * @param int $page
     * @param int $size
     * @return array
     */
    function get_page_data($query, $page = 1, $size = 20)
    {
        $groupByMode = false;
        $countTotal = 0;//count计算的总条数
        $sql = strtolower($query->toSql());
        $sql = preg_replace("/\(.*\)/", '', $sql);
        //兼容自定义groupBy字段问题
        if(strpos($sql, 'group by') !== false){
            $groupByMode = true;
            // 兼容DB，Model模式
            if (method_exists($query, 'getQuery')) {
                $columns = $query->getQuery()->columns;
            } else {
                $columns = $query->columns;
            }

            // 兼容自定义groupBy字段问题
            if (empty($columns)) {
                $columns = [Illuminate\Support\Facades\DB::raw("SQL_CALC_FOUND_ROWS *")];
            } else {
                $raw = sprintf('SQL_CALC_FOUND_ROWS %s', $columns[0]);
                $columns[0] = Illuminate\Support\Facades\DB::raw($raw);
            }
            $query->select($columns);
        }else{
            $countTotal = $query->count();
        }


        $page = $page < 1 ? 1 : $page;
        $size = $size < 0 ? 20 : $size;
        $offset = ($page - 1) * $size;

        $data = $query->offset($offset)
            ->limit($size)
            ->get()
            ->toArray();

        $total = $groupByMode ? Illuminate\Support\Facades\DB::select('SELECT FOUND_ROWS() as total')[0]->total : $countTotal;

        //根据结果修正当前页
        if($total <= $size){
            $page = 1;
            $data = $query->offset(($page - 1) * $size)
                ->limit($size)
                ->get()
                ->toArray();
        }elseif($total < $offset){
            $page = ceil($total / $size);
            $data = $query->offset(($page - 1) * $size)
                ->limit($size)
                ->get()
                ->toArray();
        }

        return [
            'pagination' => [
                'page' => (int)$page,
                'size' => (int)$size,
                'total' => $total,
            ],
            'list' => $data
        ];
    }
}

if (!function_exists('amount_format_array')) {
    /**
     * 批量金额格式转换 12345 => 12.35  12.35 => 12350
     * @param array $arr 需转换的金额数组
     * @param array $keys 需转换的键，为空则转换全部键
     * @param bool $float 是否浮点数
     * @return array
     */
    function amount_format_array(&$arr, $keys = [], $float = false)
    {
        if (empty($arr)) {
            return $arr;
        }
        // 只处理数组
        if (is_array($arr)) {
            foreach ($arr as $k => &$v) {
                if (is_array($v)) {
                    amount_format_array($v, $keys, $float);
                } else {
                    if (empty($keys) || in_array($k, $keys)) {
                        $arr[$k] = amount_format($v, $float);
                    }
                }
            }
        }
        return $arr;
    }
}

if (!function_exists('amount_format')) {
    /**
     * 金额格式转换 12345 => 12.34  12.35 => 12350
     * @param int|float $value 需转换的金额
     * @param bool $float 是否浮点数
     * @return float|int
     */
    function amount_format($value, $float = false)
    {
        if (!is_numeric($value)) {
            return $value;
        }

        return $float ? intval(round ($value*1000)) : substr(sprintf("%.3f", $value / 1000), 0, -1);//输出金额格式化为两位小数
    }
}

if (!function_exists('per_format_array')) {
    /**
     * 批量费率格式转换 12345 => 12.35  12.35 => 12350
     * @param array $arr 需转换的费率数组
     * @param array $keys 需转换的键，为空则转换全部键
     * @param bool $float 是否浮点数
     * @return array
     */
    function per_format_array(&$arr, $keys = [], $float = false)
    {
        if (empty($arr)) {
            return $arr;
        }
        // 只处理数组
        if (is_array($arr)) {
            foreach ($arr as $k => &$v) {
                if (is_array($v)) {
                    per_format_array($v, $keys, $float);
                } else {
                    if (empty($keys) || in_array($k, $keys)) {
                        $arr[$k] = per_format($v, $float);
                    }
                }
            }
        }
        return $arr;
    }
}

if (!function_exists('per_format')) {
    /**
     * 费率格式转换 12345 => 12.34  12.35 => 12350
     * @param int|float $value 需转换的费率
     * @param bool $float 是否浮点数
     * @return float|int
     */
    function per_format($value, $float = false)
    {
        if (empty($value)) {
            return 0;
        }
        if (!is_numeric($value)) {
            return $value;
        }
        return $float ? intval($value * 100) : $value / 100;
    }
}

if (!function_exists('uniqueCode')) {
    /**
     * 生成唯一值
     */
    function uniqueCode()
    {
        return md5(microtime(true));
    }
}

if (!function_exists('request')) {
    /**
     * 第三方 api http 请求
     * @param string $url 请求地址
     * @param string $method 请求api的方法，get，post，patch 等
     * @param array $data 数据，body数据，param 数据等
     * @param array $header 头部信息
     * @param bool $json 请求数据是否为json串
     * @param array $options 额外的请求选项 https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html
     *
     * @return \Psr\Http\Message\ResponseInterface $response
     */
    function request($url, $method, $data = [], $header = [], $json = true, $options = [], $timeout = 30)
    {
        try {
            //获取调用id
            $rpcInvokeId = app('request')->header('rpc-invoke-id', '');
            if(empty($rpcInvokeId)){
                $rpcInvokeId = get_rpc_invoke_id();
            }
            //设置调用id请求头
            $header['rpc-invoke-id'] = $rpcInvokeId;

            $startTime = microtime(true);
            // ========= rpc调用链log start ================
            $invokeParams = [
                'server_from' => 'user',
                'rpc_invoke_id' => $rpcInvokeId,
                'url' => $url,
                'method' => $method,
                'data' => $data,
                'code' => 0,
            ];
            \App\Services\RpcService::addRpcLog($invokeParams);
            // ========= rpc调用链log end ================
        } catch (\Throwable $e) {
            //忽略错误
            \Illuminate\Support\Facades\Log::error("fffffffffffff" . $e->getMessage() . $e->getLine());
        }

        $client = new \GuzzleHttp\Client();
        $options['timeout'] = $timeout;
        $options['verify'] = false;
        $method = strtoupper($method);
        if (!empty($data)) {
            if ($method === 'GET') {
                $key = 'query';
            } elseif ($json) {
                $key = 'json';
            } else {
                $key = 'form_params';
            }
            $options[$key] = $data;
        }

        if (!empty($header)) {
            $options['headers'] = $header;
        }

        try {
            $resp = $client->request($method, $url, $options);
            $content = $resp->getBody()->getContents();
            try {
                // =========== 调用结果rpc log start ===================
                $invokeParams['response'] = json_decode($content, true);;
                $invokeParams['time'] = (microtime(true) - $startTime);
                $invokeParams['status_code'] = $resp->getStatusCode();
                \App\Services\RpcService::addRpcLog($invokeParams);
                // =========== 调用结果rpc log end ===================
            } catch (\Throwable $e) {
                //忽略错误
                \Illuminate\Support\Facades\Log::error("eeeeeeeeeeee" . $e->getMessage() . $e->getLine());
            }
            $resp->getBody()->rewind();
            $resp->getBody()->write($content);//将结果重新写入
            $resp->getBody()->rewind();
            return $resp;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            $invokeParams['response'] = ['msg' => "请求报错：" . $e->getLine() . $e->getMessage()];
            \App\Services\RpcService::addRpcLog($invokeParams);

            if (!$e->hasResponse()) {
                $ctx = $e->getHandlerContext();
                return new \GuzzleHttp\Psr7\Response($ctx['errno'], [], json_encode($ctx));
            }
            return $e->getResponse();
        }
    }
}

if (!function_exists('build_relation_array')) {
    /**
     * 格式化数组为某个字段作为key的关联数组-处理列表二维数组用（用于控制器拼装数据, 避免嵌套循环）
     * @param $array
     * @param $field
     * @return array
     */
    function build_relation_array($array, $field)
    {
        if (empty($array) || empty($field)) {
            return array();
        }

        $returnList = array();
        foreach ($array as $key => $value) {
            $value = (array)$value;
            if (isset($value[$field]) && $value[$field]) {
                $returnList[$value[$field]] = $value;
            }
        }

        return $returnList;
    }
}

if (!function_exists('generate_order_number')) {
    /**
     * 获取系统通用订单号
     * @param string $preFix 允许自定义顶大好前缀
     * @return string
     */
    function generate_order_number($subfix = '')
    {
        usleep(mt_rand(1,100));
        list($t1, $t2) = explode(' ', microtime());
        $mTime = (float)sprintf('%.0f', floatval($t1) * 10000);
        $rand1 = mt_rand(0, 9999);
        $leftLen = 4 - strlen($rand1);
        if($leftLen > 0){
            $rand1 = str_pad($rand1, 4, mt_rand(0, pow(10, $leftLen) - 1));
        }
        return date('Ymdhis') .$rand1  . str_pad((int)$mTime, 4, 0) . $subfix;
    }
}

if (!function_exists('recharge_trade_no')) {
    /**
     * 获取充值订单号
     * @param string $preFix 允许自定义顶大好前缀
     * @return string
     */
    function recharge_trade_no($preFix = '')
    {
        return generate_order_number();
    }
}

if (!function_exists('batch_snake_case')) {
    /**
     * 批量驼峰转下划线
     * @param array $params
     * @return array
     */
    function batch_snake_case(array $params)
    {
        $ret = [];
        foreach ($params as $k => $v) {
            $ret[snake_case($k)] = $v;
        }

        return $ret;
    }
}

if (!function_exists('batch_camel_case')) {
    /**
     * 批量下划线转驼峰
     * @param array $params
     * @return array
     */
    function batch_camel_case(array $params)
    {
        $ret = [];
        foreach ($params as $k => $v) {
            $ret[camel_case($k)] = $v;
        }

        return $ret;
    }
}

if (!function_exists('get_rpc_invoke_id')) {
    /**
     * 获取rpc调用链唯一id标识
     * @param array $subfix 调用端工程标识
     * @return array
     */
    function get_rpc_invoke_id($subfix = "user")
    {
        list($t1, $t2) = explode(' ', microtime());
        $mTime = (float)sprintf('%.0f', floatval($t1) * 10000);
        return date('Ymdhis') . mt_rand(1000, 9999) . mt_rand(1000, 9999) . $mTime . '_' . $subfix;
    }
}

if (!function_exists('get_register_source')) {
    //获取注册来源 设备类型
    //注册来源: 0reserved,1pc,2h5,3android,4ios,5admin,6agent
    //优先使用设备类型传参：
    //  $device  该参数只有pc  和  h5 的区分
    //  $appType 新增加app类型参数， 最优先使用
    //默认返回pc类型
    function get_register_source($device = '', $appType = '')
    {
        //全部变成小写字母
        $agent = strtolower(\Illuminate\Support\Facades\Request::userAgent());

        //分别进行判断
        //有传参， 优先使用
        if (strtolower($appType) == 'android') {
            return 3;
        }
        if (strtolower($appType) == 'ios') {
            return 4;
        }
        if (strtolower($device) == 'pc') {
            return 1;
        }

        //其它移动端， 都认为是h5类型
        if (strtolower($device) == 'h5'
            || strpos($agent, 'mobile')
            || strpos($agent, 'iphone')
            || strpos($agent, 'ipad')
            || strpos($agent, 'android')
        ) {
            return 2;
        }

        //默认为pc
        return 1;
    }
}
