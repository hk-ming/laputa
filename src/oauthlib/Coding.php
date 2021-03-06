<?php

namespace hyperqing\oauthlib;

use hyperqing\Oauth;
use hyperqing\OauthInterface;

/**
 * 接入coding厂商
 * @package app\index\controller
 * @author HyperQing<469379004@qq.com>
 */
class Coding extends Oauth implements OauthInterface
{
    /**
     * 构造函数
     *
     * 实例化HTTP客户端
     *
     * 应用id和应用secret从环境变量中获取
     *
     * CODING_CLIENT_ID,CODING_CLIENT_SECRET
     * @throws \Exception
     */
    public function __construct()
    {
        if (!$client_id = getenv('CODING_CLIENT_ID')) {
            throw new \Exception('OAuth: 缺少环境变量 CODING_CLIENT_ID');
        }
        if (!$client_secret = getenv('CODING_CLIENT_SECRET')){
            throw new \Exception('OAuth: 缺少环境变量 CODING_CLIENT_SECRET');
        }
        $this->config = [
            'client_id' => $client_id, // 应用id
            'client_secret' => $client_secret, // 应用密钥
            'base_uri' => 'https://coding.net', // 服务商域名
            'access_token_uri' => '/api/oauth/access_token' // 获取授权码的URI
        ];
        parent::__construct();
    }

    public function getAccessToken()
    {
        if (!empty($this->accessToken)) {
            return $this->accessToken;
        }
        // 取得授权码
        $response = $this->client->request('GET', $this->config['access_token_uri'], [
            'query' => [
                'client_id' => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
                'grant_type' => 'authorization_code',
                'code' => $_GET['code']
            ]
        ]);
        $this->accessToken = json_decode((string)$response->getBody(), true);
        return $this->accessToken;
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
