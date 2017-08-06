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
            throw new \Exception('OAuth: 未设置base_uri');
        }
        $this->client = new Client([
            'base_uri' => $this->config['base_uri'],
            'timeout' => 10.0,
            'verify' => __DIR__ . '/oauth/cacert.pem'
        ]);
    }

    /**
     * 获取授权码
     *
     * 如果未有access_token将在线获取。已经存在的则直接返回。
     * @return array
     */
    public function getAccessToken()
    {
        if (!empty($this->accessToken)) {
            return $this->accessToken;
        }
        // 取得code
        $code = $_GET['code'];
        // 取得授权码
        $response = $this->client->request('GET', $this->config['access_token_uri'], [
            'query' => [
                'client_id' => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
                'grant_type' => 'authorization_code',
                'code' => $code
            ]
        ]);
        $this->accessToken = json_decode((string)$response->getBody(), true);
        return $this->accessToken;
    }
}
