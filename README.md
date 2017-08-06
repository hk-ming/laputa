# Laputa

by HyperQing 20170703

>“天空之城。”

>PHP 网络应用基础库 (A PHP web application basic libary.)

[TOC]

## 环境要求

PHP7 or latest

## 安装方法

1. `composer.json`添加本仓库(暂未发布到composer仓库，使用git代替，未来会取消这一步)
```
"repositories": [
    {
       "type": "vcs",
       "url": "https://git.coding.net/clyoko/laputa.git"
     }
 ]
```
2. 引入依赖(尚未发布正式版，需带上版本`dev-master`)
```
composer require hyperqing/laputa:dev-master
```
3. 引入`autoload.php`文件
```
require_once __DIR__ . '/vendor/autoload.php';
```

## 基础库

本库包含以下功能

- 数字+大写字母字典，共36个字符的数组。
- 在 ThinkPHP5 中的任何地方返回JSON的响应方法（为接口开发而设计的）。

### 命名空间
```php
use \hyperqing\Base;
```

### 立即响应请求

>在ThinkPHP5的控制器内进行函数调用时，或利用反射实现用户权限检查时等情况，返回一个信息是非常困难的。
>于是通过分析TP的响应行为，封装了以下响应方法。如果你使用了标志位进行数据统计或写入日志等后置的操作，可以放心，该方法会继续执行原
生命周期中应有的方法。

>(仅能在ThinkPHP5.x中使用，否则将抛出一个异常)

使用项目规范的返回格式对数据进行封装

这个操作完毕后程序会按生命周期正确结束，处理后续的app_end标志位后再exit。

使用示例
```php
Base::response('添加成功',1,[
    'token'=>'df3h4ze53rh843zd4h',
    'name'=>'username'
    ]);
```
```php
Base::response('添加失败',0);
```
最终输出示例
```
{
"info":"添加成功",
"status":1,
"data":{
 "token":"df3h4ze53rh843zd4h",
 "name":"username"
    }
}
```
```
{
"info":"添加失败",
"status":0
}
```

### 数字+大写字母字典

```
Base::$dic[0]; // =>0
Base::$dic[10]; // =>A
Base::$dic[35]; // =>Z
```

## 密码库

以面向对象的方式使用 PHP5.5 的`password_hash()`和`password_verify()`。

加密后的密文固定长度60位。
例：`$2y$10$9RTa6zmUkkYTVTHDkSNcU.4m8WJl/TA4eeSplFhc3ha904k/3o58u`

### 用法
```
use hyperqing\Password;

// 密码加密
echo Password::crypt('123456'); // =》 密文$2y$10$9RTa6zmUkkYTVTHDkSNcU.4m8WJl/TA4eeSplFhc3ha904k/3o58u

// 密码验证
var_dump(Password::verify('123456', '$2y$10$9RTa6zmUkkYTVTHDkSNcU.4m8WJl/TA4eeSplFhc3ha904k/3o58u'));
// =>bool(true)
var_dump(Password::verify('123', '$2y$10$9RTa6zmUkkYTVTHDkSNcU.4m8WJl/TA4eeSplFhc3ha904k/3o58u'));
// =>bool(false)
```

## OAuth

提供 OAuth 2.0 第三方登录功能，不依赖MVC框架，可以在ThinkPHP5、Laravel等框架中快速使用。
使用本库进行OAuth操作非常简单，只需关注几个必要的位置即可完成。

本库封装了主流服务商获取access_token和调用厂商API的功能。不仅如此，要添加其他厂商也是非常简单的。

**本库默认支持的服务商列表**
- Coding.net

### 命名空间
```
use hyperqing\oauth\厂商名;
```

### 书写位置

开通 OAuth 服务时，会填写一个回调地址，例如`localhost/oauth/callback`。
- 获取 access_token 部分（后文快速开始所示）在该回调地址的逻辑代码中书写。
- 已经存在 access_token 的，在任何位置书写均可。

### 使用 Coding.net

**快速开始**

考虑到用于开源项目或不适宜在git中流通应用密钥等需求，请添加以下环境变量，即可自动识别。
```
CODING_CLIENT_ID=你的应用id
CODING_CLIENT_SECRET=你的应用密钥
```
PHP代码
```php
use hyperqing\oauth\Coding;

// 实例化OAuth对象
$coding = new Coding();

// 获取access_token数组，包括access_token,refresh_token,expires_in
$access_token = $coding->getAccessToken();

// 获取Coding第三方用户信息
$data = $coding->getCurrentUser();

// 你的业务逻辑
// ...
```
如果你已经存储了上面得到的access_token数组，那么可以赋值给$coding对象，直接调用第三方API。
```php
$coding = new Coding();

// 设置access_token
$coding->setAccessToken([
    'access_token'=>,
    'refresh_token'=>'',
    'expire_in'=>123
]);

// 获取Coding第三方用户信息
$data = $coding->getCurrentUser();
```
就是这么简单！

#### API

>参照 Coding.net 官方文档。
>本库不会修改请求参数和返回值用法，多个请求值用数组传递，最终以数组方式返回。

**用户信息**
```php
$coding->getAccessToken()
```

### 使用 GitHub





## Lincense

本项目遵循Apache2开源协议发布，并提供免费使用。