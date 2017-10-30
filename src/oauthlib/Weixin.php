<?php

namespace hyperqing\oauthlib;

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
        'base_uri' => 'https://api.weixin.qq.com'
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
        $response = $this->client->request('GET', '/sns/oauth2/access_token', [
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
     * @return string
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
        $temp = [
            OauthConfig::getToken(OauthConfig::WEIXIN),
            $timestamp,
            $nonce
        ];
        sort($temp, SORT_STRING);
        $hash = sha1(implode($temp));
        if ($hash === $signature) {
            return $echostr;
        } else {
            return "请求错误";
        }
    }

    /**
     * 获取用户信息
     *
     * <b>数组包括以下信息：</b><br>
     * openid    用户的唯一标识<br>
     * nickname    用户昵称<br>
     * sex    用户的性别，值为1时是男性，值为2时是女性，值为0时是未知<br>
     * province    用户个人资料填写的省份<br>
     * city    普通用户个人资料填写的城市<br>
     * country    国家，如中国为CN<br>
     * headimgurl    用户头像，最后一个数值代表正方形头像大小（有0、46、64、96、132数值可选，0代表640*640正方形头像），用户没有头像时该项为空。若用户更换头像，原有头像URL将失效。<br>
     * privilege    用户特权信息，json 数组，如微信沃卡用户为（chinaunicom）<br>
     * unionid    只有在用户将公众号绑定到微信开放平台帐号后，才会出现该字段。<br>
     * @return array
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
