<?php

namespace hyperqing;

/**
 * OAuth协议接口
 *
 * 该接口用于：表示一个三方OAuth类至少包含这些方法。
 * @package hyperqing
 */
interface OauthInterface
{
    /**
     * 获取 AccessToken
     * @return mixed
     */
    public function getAccessToken();
}
