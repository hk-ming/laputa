# Laputa

by HyperQing 20170703

>网络应用基础库

>A web application basic libary.

[TOC]

## 安装方法

1. 安装项目
(未发布)
```
composer require hyperqing/laputa
```

2. 引入`autoload.php`文件
```
require_once __DIR__ . '/vendor/autoload.php';
```

## 密码库

加密后的密文固定长度60位。
例：`$2y$10$9RTa6zmUkkYTVTHDkSNcU.4m8WJl/TA4eeSplFhc3ha904k/3o58u`

以面向对象的方式使用 PHP5.5 的`password_hash()` 。

### 密码加密
```
use hyperqing\Password;
echo Password::crypt('123456'); // =》 密文$2y$10$9RTa6zmUkkYTVTHDkSNcU.4m8WJl/TA4eeSplFhc3ha904k/3o58u
```

### 密码验证
```
use hyperqing\Password;
var_dump(Password::verify('123456', '$2y$10$9RTa6zmUkkYTVTHDkSNcU.4m8WJl/TA4eeSplFhc3ha904k/3o58u'));
// =>bool(true)
var_dump(Password::verify('123', '$2y$10$9RTa6zmUkkYTVTHDkSNcU.4m8WJl/TA4eeSplFhc3ha904k/3o58u'));
// =>bool(false)
```