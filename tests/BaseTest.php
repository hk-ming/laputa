<?php

use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    /**
     * 生成两位随机串
     */
    public function testRandDic()
    {
        $this->assertRegExp("/^[A-Z][0-9]$/", \hyperqing\Base::randDic());
    }
}
