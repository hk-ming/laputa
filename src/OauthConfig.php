<?php

namespace hyperqing;

/**
 * OAuth 配置类
 *
 * 使用OAtuh库之前，请先通过本类配置AppId和AppSecret
 *
 * @package hyperqing
 * @author HyperQing<469379004@qq.com>
 */
class OauthConfig
{
    /**
     * 厂商常量
     */
    const WEIXIN = 'weixin';
    const CODING = 'coding';
    const GITHUB = 'github';

    /**
     * OAuth配置数组
     * @var array
     * @example ["weixin"=>['AppId'=>'123456','AppSecret'=>'456789','token'=>'123456789']]
     */
    private static $config = [];

    /**
     * 设置指定厂商的AppId
     * @param string $providers 服务提供商，输入OauthConfig::自动提示可使用的常量
     * @param string $param AppId字符串
     *
     * <br>providers 取值：
     * <ul>
     * <li>OauthConfig::WEIXIN</li>
     * <li>OauthConfig::CODING</li>
     * <li>OauthConfig::GITHUB</li>
     * </ul>
     */
    public static function setAppId(string $providers, string $param)
    {

        if (!isset(self::$config[$providers])) {
            self::$config[$providers] = [];
        }
        if (!isset(self::$config[$providers]['AppId'])) {
            self::$config[$providers]['AppId'] = $param;
        }
    }

    /**
     * 设置指定厂商的AppSecret
     * @param string $providers 服务提供商，输入OauthConfig::自动提示可使用的常量
     * @param string $param AppSecret字符串
     */
    public static function setAppSecret(string $providers, string $param)
    {
        if (!isset(self::$config[$providers])) {
            self::$config[$providers] = [];
        }
        if (!isset(self::$config[$providers]['AppSecret'])) {
            self::$config[$providers]['AppSecret'] = $param;
        }
    }

    /**
     * 设置指定厂商的token
     * @param string $providers 服务提供商，输入OauthConfig::自动提示可使用的常量
     * @param string $param token
     */
    public static function setToken(string $providers, string $param)
    {
        if (!isset(self::$config[$providers])) {
            self::$config[$providers] = [];
        }
        if (!isset(self::$config[$providers]['token'])) {
            self::$config[$providers]['token'] = $param;
        }
    }

    /**
     * 获取指定厂商的AppId
     * @param string $providers 服务提供商，输入OauthConfig::自动提示可使用的常量
     * @return mixed
     * @throws \Exception
     */
    public static function getAppId(string $providers)
    {
        if (isset(self::$config[$providers]) && isset(self::$config[$providers]['AppId'])) {
            return self::$config[$providers]['AppId'];
        }
        throw new \Exception('OAuth:没有配置 ' . $providers . ' 的AppId。');
    }

    /**
     * 获取指定厂商的AppSecret
     * @param string $providers 服务提供商，输入OauthConfig::自动提示可使用的常量
     * @return mixed
     * @throws \Exception
     */
    public static function getAppSecret(string $providers)
    {
        if (isset(self::$config[$providers]) && isset(self::$config[$providers]['AppSecret'])) {
            return self::$config[$providers]['AppSecret'];
        }
        throw new \Exception('OAuth:没有配置 ' . $providers . ' 的 AppSecret。');
    }

    /**
     * token
     * @param string $providers 服务提供商，输入OauthConfig::自动提示可使用的常量
     * @return mixed
     * @throws \Exception
     */
    public static function getAToken(string $providers)
    {
        if (isset(self::$config[$providers]) && isset(self::$config[$providers]['token'])) {
            return self::$config[$providers]['token'];
        }
        throw new \Exception('OAuth:没有配置 ' . $providers . ' 的 token。');
    }
}

OauthConfig::setAppSecret(OauthConfig::WEIXIN, '456');
OauthConfig::setAppId(OauthConfig::WEIXIN, '123');
echo OauthConfig::getAppId(OauthConfig::WEIXIN);
echo OauthConfig::getAppSecret(OauthConfig::WEIXIN);