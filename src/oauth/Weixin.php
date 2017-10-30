<?php

namespace app\index\controller;

use hyperqing\Oauth;
use hyperqing\oauth\WeixinConfig;
use hyperqing\OauthConfig;
use hyperqing\OauthInterface;

/**
 * 微信开放平台
 * @package app\index\controller
 */
class Weixin extends Oauth implements OauthInterface
{
    /**
     * 基本的固定配置
     * @var array
     */
    protected $config = [
        'base_uri' => 'https://api.weixin.qq.com',
        'access_token_uri' => '/sns/oauth2/access_token'
    ];

    /**
     * 获取授权码
     *
     * 如果未有access_token将在线获取。已经存在的则直接返回。
     * @return array
     * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140842
     */
    public function getAccessToken()
    {
        if (!empty($this->accessToken)) {
            return $this->accessToken;
        }
        // 取得授权码
        $response = $this->client->request('GET', $this->config['access_token_uri'], [
            'query' => [
                'appid' => OauthConfig::getAppId(OauthConfig::WEIXIN),
                'secret' => OauthConfig::getAppSecret(OauthConfig::WEIXIN),
                'code' => $_GET['code'],
                'grant_type' => 'authorization_code'
            ]
        ]);
        $arr = json_decode((string)$response->getBody(), true);
        $this->accessToken = $arr['access_token'];
        $this->openid = $arr['openid'];
        return $arr;
    }

    /**
     * 获取用户信息
     * @return mixed
     * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140842
     */
    public function getUserinfo()
    {
        $response = $this->client->request('GET', '/sns/userinfo', [
            'query' => [
                'access_token' => $this->accessToken,
                'openid' => $this->openid,
                'lang' => 'zh_CN'
            ]
        ]);
        $arr = json_decode((string)$response->getBody(), true);
        return $arr;
    }
}
