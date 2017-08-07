<?php
declare(strict_types=1);

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
     * @static
     * @param string $password
     * @return string 密文60位
     */
    public static function crypt(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * 验证密码
     * @static
     * @param string $password 用户输入的密码
     * @param string $hash 正确密码的密文
     * @return bool 正确返回true，否则返回false
     */
    public static function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
