# Laputa

by HyperQing 20170703

> “天空之城。”

> PHP 网络应用基础库 (A PHP web application basic libary.)

[TOC]

## 环境要求

PHP7 or latest

## 安装方法

1. 通过Composer安装本库
```
composer require hyperqing/laputa --prefer-dist
```
2. 引入`autoload.php`文件
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

本库封装了主流服务商开放API的功能。不仅如此，要添加其他厂商也是非常简单的。

**本库默认支持的服务商列表**

- Weixin 微信公众号
- Coding 扣钉Coding.net
- Github 你懂的

### 命名空间
```
use hyperqing\oauthlib\厂商名;
```

### 使用微信

**快速开始**

1. 配置AppId、AppSecret、Token(用于微信接口验证)。这个代码可以书写在控制器的构造函数，或框架生命周期中，适合处理初始化配置的地方。
```
OauthConfig::setAppId(OauthConfig::WEIXIN, '你的公众号AppId');
OauthConfig::setAppSecret(OauthConfig::WEIXIN, '你的公众号AppSecret');
OauthConfig::setToken(OauthConfig::WEIXIN, '你自定义的Token');
```
2. 微信接口验证。只需在微信接口测试平台设置好URL和Token，在第一步中设置你自定义的Token，简单两句即可完成接口验证。
```
public function weixinVerify(){
    $weixin = new Weixin();
    return $weixin->weixinVerify(); // 这里将返回微信传入的echostr或“请求错误”
}
```
3. 第三方登录回调获取accessToken，获取用户基本信息。
```
public function callback(){
    $weixin = new Weixin();
    $weixin->getAccessToken(); // 返回包含accessToken等信息的数组。
    $arr = $weixin->getUserinfo(); // 返回包含openid、用户名称等信息的数组
    // ...你的业务逻辑
}
```

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

> 参照 Coding.net 官方文档。
> 本库不会修改请求参数和返回值用法，多个请求值用数组传递，最终以数组方式返回。

**用户信息**
```php
$coding->getAccessToken()
```

### 使用 GitHub

## 单元测试

### 在Windows中安装PHPUnit

1. 将 php.exe 所在目录（以下称`PHP目录`）添加到 PATH 环境变量中。

2. 下载 https://phar.phpunit.de/phpunit.phar 并将文件保存到`PHP目录`。

3. 打开命令行（例如，按 Windows+R » 输入 cmd » ENTER)

4. 建立批处理脚本（最后得到 `PHP目录\phpunit.cmd`）：
```
C:\> cd PHP目录
C:\PHP目录> echo @php "%~dp0phpunit.phar" %* > phpunit.cmd
C:\PHP目录> exit
```
5. 新开一个命令行窗口，确认一下可以在任意路径下执行 PHPUnit：
```
C:\> phpunit --version
PHPUnit 6.3.0 by Sebastian Bergmann and contributors.
```

### Composer提供代码提示

如果希望在IDE中得到更多代码提示，可以引入phpunit源码。
```
composer require --dev phpunit/phpunit
```

### 运行测试

要运行全部测试很简单，在项目目录中执行以下命令即可。
```
phpunit
```
这将会直接按 phpunit.xml 配置运行测试。

**其他**

忽略phpunit.xml配置直接运行，
```
phpunit --bootstrap vendor/autoload.php tests
```
运行单个测试用例
```
phpunit --bootstrap src/autoload.php tests/PasswordTest
```

## Lincense

本项目遵循Apache2开源协议发布，并提供免费使用。
