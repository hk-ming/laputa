<?php

namespace app\index\controller;

use hyperqing\Oauth;
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
     * 验证服务器地址的有效性
     * @return mixed|string
     * https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421135319
     */
    public function weixinVerify()
    {

        $signature = $_GET['signature'];
        $timestamp = $_GET['timestamp'];
        $nonce = $_GET['nonce'];
        $echostr = $_GET['echostr'];
        /**
         * 1）将token、timestamp、nonce三个参数进行字典序排序
         * 2）将三个参数字符串拼接成一个字符串进行sha1加密
         * 3）开发者获得加密后的字符串可与signature对比，标识该请求来源于微信
         * 4）若确认此次GET请求来自微信服务器，请原样返回echostr参数内容，则接入生效，成为开发者成功，否则接入失败。
         */
        $temp = [OauthConfig::getToken(OauthConfig::WEIXIN), $timestamp, $nonce];
        sort($temp, SORT_STRING);
        $hash = sha1(implode($temp));
        if ($hash == $signature) {
            return $echostr;
        } else {
            return "请求错误";
        }
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
