<?php

namespace hyperqing;

/**
 * 密码库
 * <br>
 * 使用PHP自带的密码 B-Crypt 算法
 * <br>
 * 要求 PHP5.5 以上
 * @require PHP 5.5+
 * @package hyperqing
 * @author HyperQing<469379004@qq.com>
 */
class Password
{
    /**
     * 密码加密
     * @return string 密文60位
     */
    public function crypt($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    public function
}