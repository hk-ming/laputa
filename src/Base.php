<?php

namespace hyperqing;


class Base
{
    /**
     * 数字字母字典
     * @static
     * @access public
     * @var array
     */
    public static $dic = [
        0 => '0', 1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9',
        10 => 'A', 11 => 'B', 12 => 'C', 13 => 'D', 14 => 'E', 15 => 'F', 16 => 'G', 17 => 'H', 18 => 'I', 19 => 'J',
        20 => 'K', 21 => 'L', 22 => 'M', 23 => 'N', 24 => 'O', 25 => 'P', 26 => 'Q', 27 => 'R', 28 => 'S', 29 => 'T',
        30 => 'U', 31 => 'V', 32 => 'W', 33 => 'X', 34 => 'Y', 35 => 'Z'
    ];

    /**
     * 生成两位随机串
     * @static
     * @access public
     * @return string 例如'A8'
     */
    public static function randDic()
    {
        return self::$dic[mt_rand(10, 35)] . mt_rand(0, 9);
    }

    /**
     * 立即响应请求
     *
     * 使用项目规范的返回格式对数据进行封装
     * 这个操作完毕后程序会按生命周期正确结束，处理后续的app_end标志位后再exit
     * 规定使用JSON，返回格式示例：
     *
     * {
     *  "info":"操作成功",
     *  "status":1,
     *  "data":{
     *     "token":"df3h4ze53rh843zd4h",
     *     "name":"username"
     *   }
     * }
     *
     * @static
     * @access public
     * @param string $info 响应的说明信息
     * @param integer $status 1成功，0失败。默认为1
     * @param array $data 要返回的信息数组。默认为空数组
     * @param int $code HTTP状态码
     * @throws \Exception
     */
    public static function response(string $info, int $status = 1, $data = [], $code = 200)
    {
        if (!class_exists('\\think\\Response')) {
            throw new \Exception('This method is only used in ThinkPHP5.x');
        }
        $response['info'] = $info;
        $response['status'] = $status;
        if (!empty($data)) {
            $response['data'] = $data;
        }
        // 此写法避免IDE报错，\think\Response::create()
        $class = '\\think\\Response';
        $response = $class::create($response, 'json', $code);
        // 此写法避免IDE报错，\think\Hook::listen()
        $class = '\\think\\Hook';
        $class::listen('app_end', $response);
        $response->send();
        exit;
    }
}
