<?php

namespace hyperqing;

use GuzzleHttp\Client;

/**
 * 第三方登录
 * @package app\index\controller
 * @author HyperQing<469379004@qq.com>
 */
class Oauth
{
    /**
     * Oauth配置
     * @var array
     */
    protected $config = [
        'client_id' => '', // 应用id
        'client_secret' => '', // 应用密钥（开源项目建议保存在环境变量）
        'base_uri' => '', // 服务商域名
        'access_token_uri' => '' // 获取授权码的URI
    ];

    /**
     * 授权码数组
     *
     * 包括:access_token,refresh_token,expires_in
     * @var array
     */
    protected $accessToken = [];

    /**
     * 自定义保存AccessToken的方法
     * @var callable
     */
    protected $saveAccessToken;

    /**
     * 自定义读取AccessToken的方法
     * @var callable
     */
    protected $loadAccessToken;

    /**
     * HTTP客户端
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * 构造函数
     *
     * 实例化HTTP客户端
     * @throws \Exception
     */
    public function __construct()
    {
        if ($this->config['base_uri'] == '') {
            throw new \Exception('Oauth: 未设置base_uri');
        }
        $this->client = new Client([
            'base_uri' => $this->config['base_uri'],
            'timeout' => 10.0,
            'verify' => __DIR__ . '/cacert.pem'
        ]);
    }

    /**
     * 获取授权码
     * @return $this
     */
    public function getAccessToken()
    {
        // 取得code和scope
        $code = $_GET['code'];
//        $scope = $_GET['scope'];
        // 取得授权码
        $response = $this->client->request('GET', $this->config['access_token_uri'], [
            'query' => [
                'client_id' => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
                'grant_type' => 'authorization_code',
                'code' => $code
            ]
        ]);
        // 执行自定义保存授权码操作
        call_user_func($this->saveAccessToken, (string)$response->getBody());
        // 保存授权码留待链式调用中使用
        $this->accessToken = json_decode((string)$response->getBody(), true);
        return $this;
    }

    /**
     * 自定义保存AccessToken的操作
     * @param callable $callback
     */
    public function onSaveAccessToken(callable $callback)
    {
        $this->saveAccessToken = $callback;
    }

    public function onLoadAccessToken(callable $callback)
    {
        $this->loadAccessToken = $callback;
    }
}
