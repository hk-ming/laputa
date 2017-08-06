<?php

namespace hyperqing\oauth;

use hyperqing\Oauth;

class Github extends Oauth
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
        if (!$client_id = getenv('GITHUB_CLIENT_ID')) {
            throw new \Exception('OAuth: 缺少环境变量 GITHUB_CLIENT_ID');
        }
        if (!$client_secret = getenv('GITHUB_CLIENT_SECRET')) {
            throw new \Exception('OAuth: 缺少环境变量 GITHUB_CLIENT_SECRET');
        }
        $this->config = [
            'client_id' => $client_id, // 应用id
            'client_secret' => $client_secret, // 应用密钥
            'base_uri' => 'https://github.com', // 服务商域名
            'access_token_uri' => '/login/oauth/access_token' // 获取授权码的URI
        ];
        parent::__construct();
    }

    /**
     * 获取用户信息
     * @return array 用户信息数组
     */
    public function getUser()
    {
        $response = $this->client->request('GET', 'https://api.github.com/user', [
            'query' => [
                'access_token' => $this->accessToken['access_token']
            ]
        ]);
        return json_decode((string)$response->getBody(), true);
    }

    /**
     * 获取授权码
     *
     * 如果未有access_token将在线获取。已经存在的则直接返回。
     * @return array 数组，包括:access_token,refresh_token,expires_in
     */
    public function getAccessToken()
    {
        if (!empty($this->accessToken)) {
            return $this->accessToken;
        }
        // 取得code和scope
        $code = $_GET['code'];
//        $scope = $_GET['scope'];
        // 取得授权码
        $response = $this->client->request('POST', $this->config['access_token_uri'], [
            'headers' => [
                'Accept' => 'application/json'
            ],
            'form_params' => [
                'client_id' => $this->config['client_id'],
                'client_secret' => $this->config['client_secret'],
                'code' => $code
            ]
        ]);
        var_dump((string)$response->getBody());
        $this->accessToken = json_decode((string)$response->getBody(), true);
        return $this->accessToken;
    }
}