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


    protected $OfficalAccountToken = '';

    /**
     * 获取用户的access_token
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
    public function sns_userinfo()
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

    /**
     * 获取公众号的access_token
     *
     * 目前文档规定的有效期是7200秒。
     * @return mixed
     * @throws \Exception
     * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140183
     */
    public function getOfficialAccountsToken()
    {
        if (!empty($this->OfficalAccountToken)) {
            return $this->OfficalAccountToken;
        }
        $response = $this->client->request('GET', '/cgi-bin/token', [
            'query' => [
                'grant_type' => 'client_credential',
                'appid' => OauthConfig::getAppId(OauthConfig::WEIXIN),
                'secret' => OauthConfig::getAppSecret(OauthConfig::WEIXIN)
            ]
        ]);
        $arr = json_decode((string)$response->getBody(), true);
        if (!isset($arr['access_token'])) {
            throw new \Exception('无法获取公众号access_token');
        }
        $this->OfficalAccountToken = $arr['access_token'];
        return $this->OfficalAccountToken;
    }

    /**
     * 自定义菜单创建接口
     * <p>
     * 要求传入使用json表示的菜单数据（具体结构见文档）<br>
     * 成功返回true,失败则抛出异常，由外部自行处理。
     * </p>
     * 请求类型：<b>POST</b>
     * @param string $json 描述菜单的JSON数据（具体结构见文档）
     * @return bool
     * @throws \Exception
     * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141013
     */
    public function menu_create(string $json): bool
    {
        $response = $this->client->request('POST', '/cgi-bin/menu/create', [
            'query' => [
                'access_token' => $this->getOfficialAccountsToken()
            ],
            'body' => $json
        ]);
        $arr = json_decode((string)$response->getBody(), true);
        if ($arr['errcode'] === 0) {
            return true;
        }
        throw new \Exception($arr['errmsg'], $arr['errcode']);
    }

    /**
     * 自定义菜单查询接口
     * <p>
     * 成功返回true,失败则抛出异常，由外部自行处理。
     * </p>
     * 请求类型：<b>GET</b>
     * @return array
     * @throws \Exception
     * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141014
     */
    public function menu_get(): array
    {
        $response = $this->client->request('GET', '/cgi-bin/menu/get', [
            'query' => [
                'access_token' => $this->getOfficialAccountsToken()
            ]
        ]);
        $arr = json_decode((string)$response->getBody(), true);
        if (isset($arr['menu'])) {
            return $arr;
        }
        throw new \Exception($arr['errmsg'], $arr['errcode']);
    }

    /**
     * 自定义菜单删除接口
     * <p>
     * 成功返回true,失败则抛出异常，由外部自行处理。
     * </p>
     * 请求类型：<b>GET</b>
     * @return bool
     * @throws \Exception
     * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141015
     */
    public function menu_delete(): bool
    {
        $response = $this->client->request('GET', '/cgi-bin/menu/delete', [
            'query' => [
                'access_token' => $this->getOfficialAccountsToken()
            ]
        ]);
        $arr = json_decode((string)$response->getBody(), true);
        if ($arr['errcode'] === 0) {
            return true;
        }
        throw new \Exception($arr['errmsg'], $arr['errcode']);
    }

    /**
     * 获取自定义菜单配置接口
     */
    public function get_current_selfmenu_info()
    {
        $response = $this->client->request('GET', '/cgi-bin/get_current_selfmenu_info', [
            'query' => [
                'access_token' => $this->getOfficialAccountsToken()
            ]
        ]);
        $arr = json_decode((string)$response->getBody(), true);
        return $arr;
    }

    /**
     * 获取用户列表
     * @param string|null $next_openid
     * @return mixed
     */
    public function user_get(string $next_openid = null)
    {
        $response = $this->client->request('GET', '/cgi-bin/user/get', [
            'query' => [
                'access_token' => $this->getOfficialAccountsToken(),
                'next_openid' => $next_openid
            ]
        ]);
        $arr = json_decode((string)$response->getBody(), true);
        return $arr;
    }

    /**
     * 获取用户基本信息
     * @param string $openid
     * @return mixed
     */
    public function user_info(string $openid)
    {

        $response = $this->client->request('GET', '/cgi-bin/user/info', [
            'query' => [
                'access_token' => $this->getOfficialAccountsToken(),
                'next_openid' => $openid,
                'lang' => 'zh_CN'
            ]
        ]);
        $arr = json_decode((string)$response->getBody(), true);
        return $arr;
    }

    /**
     * 批量获取用户基本信息
     * @param $openid_list
     * @return mixed
     */
    public function user_info_batchget($openid_list)
    {
        $response = $this->client->request('GET', '/cgi-bin/user/info/batchget', [
            'query' => [
                'access_token' => $this->getOfficialAccountsToken(),
                'next_openid' => $openid_list,
                'lang' => 'zh_CN'
            ]
        ]);
        $arr = json_decode((string)$response->getBody(), true);
        return $arr;
    }

    /**
     * 设置用户备注名
     *
     * 该接口暂时开放给微信认证的服务号。
     */
    public function user_info_updatemark()
    {
        $response = $this->client->request('POST', '/cgi-bin/user/info/updateremark', [
            'query' => [
                'access_token' => $this->getOfficialAccountsToken()
            ],
            'body' => ''
        ]);
        $arr = json_decode((string)$response->getBody(), true);
        return $arr;
    }

    /**
     * 创建标签
     * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140837
     */
    public function tags_create()
    {
        $response = $this->client->request('POST', '/cgi-bin/tags/create', [
            'query' => [
                'access_token' => $this->getOfficialAccountsToken()
            ],
            'body' => ''
        ]);
    }

    /**
     * 获取公众号已创建的标签
     * @return mixed
     * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140837
     */
    public function tags_get()
    {
        $response = $this->client->request('GET', '/cgi-bin/tags/get', [
            'query' => [
                'access_token' => $this->getOfficialAccountsToken()
            ]
        ]);
        $arr = json_decode((string)$response->getBody(), true);
        return $arr;
    }

    /**
     * 编辑标签
     * @return mixed
     * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140837
     */
    public function tags_update()
    {
        $response = $this->client->request('POST', '/cgi-bin/tags/update', [
            'query' => [
                'access_token' => $this->getOfficialAccountsToken()
            ],
            'body' => ''
        ]);
        $arr = json_decode((string)$response->getBody(), true);
        return $arr;
    }

    /**
     *
     * 请注意，当某个标签下的粉丝超过10w时，后台不可直接删除标签。此时，开发者可以对该标签下的openid列表，先进行取消标签的操作，直到粉丝数不超过10w后，才可直接删除该标签。
     */
    public function tags_delete()
    {
        $response = $this->client->request('POST', '/cgi-bin/tags/delete', [
            'query' => [
                'access_token' => $this->getOfficialAccountsToken()
            ],
            'body' => ''
        ]);
        $arr = json_decode((string)$response->getBody(), true);
        return $arr;
    }

    /**
     * 获取标签下粉丝列表
     * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140837
     */
    public function user_tag_get()
    {
        $response = $this->client->request('GET', '/cgi-bin/user/tag/get', [
            'query' => [
                'access_token' => $this->getOfficialAccountsToken()
            ]
        ]);
        $arr = json_decode((string)$response->getBody(), true);
        return $arr;
    }

    /**
     * 批量为用户打标签
     */
    public function tags_members_batchtagging()
    {
        $response = $this->client->request('POST', '/cgi-bin/tags/members/batchtagging', [
            'query' => [
                'access_token' => $this->getOfficialAccountsToken()
            ],
            'body' => ''
        ]);
        $arr = json_decode((string)$response->getBody(), true);
        return $arr;
    }

    /**
     * 批量为用户取消标签
     * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140837
     */
    public function tags_members_batchuntagging()
    {
        $response = $this->client->request('POST', '/cgi-bin/tags/members/batchuntagging', [
            'query' => [
                'access_token' => $this->getOfficialAccountsToken()
            ],
            'body' => ''
        ]);
        $arr = json_decode((string)$response->getBody(), true);
        return $arr;
    }

    /**
     * 获取用户身上的标签列表
     * @link https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421140837
     */
    public function tags_getidlist()
    {
        $response = $this->client->request('POST', '/cgi-bin/tags/getidlist', [
            'query' => [
                'access_token' => $this->getOfficialAccountsToken()
            ],
            'body' => ''
        ]);
        $arr = json_decode((string)$response->getBody(), true);
        return $arr;
    }
}
