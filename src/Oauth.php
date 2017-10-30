<?php

namespace hyperqing;

use GuzzleHttp\Client;

/**
 * 第三方开放平台OAuth操作父类
 *
 * 请继承本类后，实现getAccessToken等方法。
 * 具体可以模仿先有的OAuth类库。
 *
 * 支持 HTTPS 链接。
 *
 * @package app\index\controller
 * @author HyperQing<469379004@qq.com>
 */
class Oauth
{
    /**
     * OAuth固定的基本信息
     *
     * 需要在继承子类后，在子类中覆盖该配置
     * @var array
     */
    protected $config = [
        'base_uri' => '' // 服务商域名
    ];

    /**
     * 授权码数组
     *
     * 包括:access_token,refresh_token,expires_in等。具体各厂商略有不同，以实际为准。<br>
     * 请使用getAccessToken方法获取本数组
     * @var array
     */
    protected $accessToken = [];

    protected $openid = [];

    /**
     * HTTP客户端
     *
     * 用于向三方平台发送请求的HTTP客户端
     * @var \GuzzleHttp\Client
     */
    protected $client = null;

    /**
     * 构造函数
     *
     * 执行内容：<br>
     * 1. 检查base_uri<br>
     * 2. 实例化HTTP客户端
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
            'verify' => __DIR__ . '/oauthlib/cacert.pem'
        ]);
    }
}
