<?php

namespace hyperqing\oauth;

use hyperqing\Oauth;

/**
 * 接入coding厂商
 * @package app\index\controller
 * @author HyperQing<469379004@qq.com>
 */
class Coding extends Oauth
{
    /**
     * 构造函数
     * 实例化HTTP客户端
     * @param string $client_id 应用id
     * @param string $client_secret 应用密钥
     */
    public function __construct(string $client_id, string $client_secret)
    {
        $this->config = [
            'client_id' => $client_id, // 应用id
            'client_secret' => $client_secret, // 应用密钥（开源项目建议保存在环境变量，）
            'base_uri' => 'https://coding.net', // 服务商域名
            'access_token_uri' => '/api/oauth/access_token' // 获取授权码的URI
        ];
        parent::__construct();
    }

    /**
     * 获取用户信息
     * @return array 用户信息数组
     */
    public function getCurrentUser()
    {
        $response = $this->client->request('GET', '/api/account/current_user', [
            'query' => [
                'access_token' => $this->accessToken['access_token']
            ]
        ]);
        return json_decode((string)$response->getBody(), true);
    }
}
