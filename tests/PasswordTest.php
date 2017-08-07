<?php

use PHPUnit\Framework\TestCase;

//require_once __DIR__ . '/../vendor/autoload.php';

class PasswordTest extends TestCase
{
    /**
     * 加密
     */
    public function testEncry()
    {
        $hash = \hyperqing\Password::crypt('987654321');
        $this->assertEquals(60, strlen($hash));
        return $hash;
    }

    /**
     * 验证密码
     * @depends testEncry
     * @param string $hash
     */
    public function testVerify(string $hash)
    {
        // 密码正确的情况
        $this->assertTrue(\hyperqing\Password::verify('987654321', $hash));
        // 密码不正确的情况
        $this->assertFalse(\hyperqing\Password::verify('98765', $hash));
    }
}