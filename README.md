<div style="text-align: center;">
  <img height="100" src="https://cdn.morrios.top/assets/logo-payment.png" alt="MorriosPayment"/>
</div>

## 目录

- [目录](#--)
- [说明](#--)
- [安装](#%e5%ae%89%e8%a3%85)
  * [依赖要求](#----)
  * [composer安装方式](#composer----)
- [使用方法](#----)
  * [说明](#---1)
  * [实例化支付实例](#-------)
  * [支付下单](#----)
  * [DEMO](#demo)
- [参数类说明](#-----)
  * [配置参数类（ConfigParam）](#------configparam-)
  * [下单参数类（TradeParam）](#------tradeparam-)
  * [下单结果参数类（TradeResultParam）](#--------traderesultparam-)
- [贡献指南](#----)
- [LICENSE](#license)

## 说明

- Morrios工具包支付库

## 安装

### 依赖要求

| Morrios Payment版本 | PHP 版本 | PHP 拓展 |
|:--------------------:|:---------------------------:|:---------------------------:|
|          1.x         |  7.0 + |  `ext-json` `ext-mbstring` `ext-openssl` |

### composer安装方式

- 通过composer，这是推荐的方式，可以使用composer.json 声明依赖，或者运行下面的命令

```shell
composer require morrios/payment
```

## 使用方法

### 说明

- 目前支持渠道：官方支付宝、官方微信支付
- 所有参数传递及结果响应均通过已实例化的参数类进行传递

### 实例化支付实例

- 支付配置参数类`ConfigParam`详见 [说明](#------configparam-)

```php
use Morrios\Base\Exception\BusinessException;
use Morrios\Payment\PaymentFactory;
use Morrios\Payment\Param\ConfigParam;

try {
    // 根据支付渠道需要实例化不同的支付配置参数类
    $alipayConfig = new ConfigParam([
        'app_id'  => 'APP_ID',
        'pay_key' => 'PAY_KEY',
    ]);
    $wechatConfig = new ConfigParam([
        'app_id'  => 'APP_ID',
        'pay_key' => 'PAY_KEY',
        'mch_id'  => 'MCH_ID',
    ]);
    
    // 根据支付渠道需要实例化不同的支付实例
    $instance = PaymentFactory::alipay($alipayConfig); // 支付宝实例
    $instance = PaymentFactory::wechat($wechatConfig); // 微信实例
} catch (BusinessException $exception) { // 失败抛BusinessException异常
    // 根据业务自行处理
    // ...
}
```

### 支付下单

- 支付下单参数类`TradeParam`详见 [说明](#------tradeparam-)
- 支付结果参数类`TradeResultParam`详见 [说明](#--------traderesultparam-)

```php
use Morrios\Base\Exception\BusinessException;
use Morrios\Payment\Param\CommonTradeParam;

try {
    // 实例化下单参数类
    $tradeParam = new CommonTradeParam([
        'description' => '充值',
        'attach'      => 'ATTACH',
        'order_no'    => 'ORDER_NO',
        'money'       => 100,
        'notify_url'  => 'https://xx.com/route/to/notify',
    ]);

    // 下单操作（根据支付方式需要进行不同调用）
    $result = $instance->appTrade($tradeParam);   // APP支付
    $result = $instance->wapTrade($tradeParam);   // H5支付
    $result = $instance->qrTrade($tradeParam);    // 二维码支付
    $result = $instance->jsApiTrade($tradeParam); // 微信特有，JsApi支付，eg：公众号

    // 根据业务自行处理下单结果（成功响应TradeResultParam实例）
    // ...
} catch (BusinessException $exception) { // 失败抛BusinessException异常
    // 根据业务自行处理
    // ...
}
```

### DEMO

- 以微信渠道拉起APP支付订单为例

```php
use Morrios\Payment\Param\{ConfigParam, CommonTradeParam};
use Morrios\Payment\PaymentFactory;
use Morrios\Base\Exception\BusinessException;

try {
    // 实例化支付配置参数类
    $config = new ConfigParam([
        'app_id'  => 'APP_ID',
        'pay_key' => 'PAY_KEY',
        'mch_id'  => 'MCH_ID',
    ]);
    
    // 实例化微信支付实例
    $instance = PaymentFactory::wechat($config);
    
    // 实例化下单参数类
    $tradeParam = new CommonTradeParam([
        'description' => '游戏充值',
        'attach'      => '',
        'order_no'    => 'PayTestOrderNoByCarl',
        'money'       => 100,
        'notify_url'  => 'https://xx.com/route/to/notify',
    ]);

    // 下单操作
    $result = $instance->appTrade($tradeParam);   // APP支付

    // 处理下单结果（成功响应TradeResultParam实例，具体说明见下方表格）
    $result->prepay_id; // 微信生成的预支付会话标识
    $result->code_url;  // 微信生成支付二维码，提供给用户进行扫码支付

    // 如需数组类型结果可以调用toArray()
    $resultAsArray = $result->toArray();

    // 根据业务自行处理
    echo '微信支付APP下单成功';
    
    // ...
    
} catch (BusinessException $exception) { // 失败抛BusinessException异常
    // 根据业务自行处理
    echo '微信支付APP下单失败：' . $exception->getMessage();
    
    // ...
}
```

## 参数类说明

### 配置参数类（ConfigParam）

| 属性  | 类型  | 是否必传  | 说明  | 备注  |
| ------------ | ------------ | ------------ | ------------ | ------------ |
| app_id  | string  | 是  | 支付应用ID  | 支付宝 => 支付宝分配给开发者的应用ID<br>微信   => 公众账号ID|应用ID|小程序ID  |
| pay_key  | string  | 是  | 支付KEY  | 支付宝 => 应用私钥<br>微信   => 商户平台设置的密钥key  |
| mch_id  | string  | 微信必传  | 商户号  | 微信 => 商户号（微信专用）  |

### 下单参数类（TradeParam）

| 属性  | 类型  | 是否必传  | 说明  | 备注  |
| ------------ | ------------ | ------------ | ------------ | ------------ |
| order_no  | string  | 是  | 系统内部唯一订单号  | - |
| money  | number  | 是  | 订单金额  | 单位：元  |
| subject  | string  | 是  | 订单标题  | -  |
| notify_url  | string  | 是  | 回调地址  | 异步接收支付结果通知的回调地址 |
| product_id  | string  | [`微信qrTrade` `支付宝qrTrade` `支付宝wapTrade`]必传  | 产品标识  | 微信 => 二维码中包含的商品ID，商户自行定义<br>支付宝 => 销售产品码，与支付宝签约的产品码名称  |
| openid  | string  | [`微信jsApiTrade`]必传  | 用户标识  | 微信 => 用户标识  |
| quit_url  | string  | [`支付宝qrTrade` `支付宝wapTrade`]必传  | 用户付款中途退出返回商户网站的地址  |   |
| body  | string  | 否  | 订单描述  | -  |
| client_ip  | string  | 否  | 客户端IP  | 若未传则默认使用 ```$_SERVER['REMOTE_ADDR']``` 的值 |
| attach  | string  | 否  | 透传参数  | 透传参数，如果请求时传递了该参数，则返回给商户时会回传该参数  |
| device_info  | string  | 否  | 设备信息  | 微信 => 自定义参数，可以为终端设备号(门店号或收银设备ID)，PC网页或公众号内支付可以传"WEB"<br>支付宝 => 商户门店编号 |
| discount  | string  | 否  | 优惠参数  | 微信 => 订单优惠标记，使用代金券或立减优惠功能时需要的参数，详见[代金券或立减优惠](https://pay.weixin.qq.com/wiki/doc/api/tools/sp_coupon.php?chapter=12_7&index=3)<br>支付宝 => 仅与支付宝协商后可用 |
| enable_pay_channels  | string  | 否  | 可用渠道  | 支付宝 => 用户只能在指定渠道范围内支付，多个渠道以逗号分割用，与disable_pay_channels互斥，[渠道列表](https://docs.open.alipay.com/common/wifww7) |
| disable_pay_channels  | string  | 否  | 禁用渠道  | 微信 => 上传此参数 no_credit 可限制用户不能使用信用卡支付<br>支付宝 => 用户不可用指定渠道支付，多个渠道以逗号分割，与enable_pay_channels互斥，[渠道列表](https://docs.open.alipay.com/common/wifww7)|
| receipt  | string  | 否  | 电子发票入口开放标识  | 微信 => Y，传入Y时，支付成功消息和支付详情页将出现开票入口。需要在微信支付商户平台或微信公众平台开通电子发票功能，传此字段才可生效  |
| detail  | string  | 否  | 订单详情  | 微信 => 商品详细描述，对于使用单品优惠的商户，该字段必须按照规范上传，详见[单品优惠参数说明](https://pay.weixin.qq.com/wiki/doc/api/danpin.php?chapter=9_102&index=2)<br>支付宝 => 订单包含的商品列表信息，json格式，详见[goods_detail](https://docs.open.alipay.com/api_1/alipay.trade.app.pay/)  |
| scene_info  | string  | 否  | 场景信息  | 微信 => 该字段常用于线下活动时的场景信息上报，支持上报实际门店信息，商户也可以按需求自己上报相关信息。该字段为JSON对象数据，对象格式为{"store_info":{"id": "门店ID","name": "名称","area_code": "编码","address": "地址" }} |
| return_url  | string  | 否  | 返回地址  | 支付宝 => 支付流程结束跳回地址 |
| app_auth_token  | string  | 否  | 应用授权Token  | 支付宝 => 详见[应用授权概述](https://docs.open.alipay.com/common/105193) |
| goods_type  | integer  | 否  | 商品主类型  | 支付宝 => 0-虚拟类商品,1-实物类商品  |
| auth_token  | string  | 否  | 用户授权Token  | 支付宝 => 针对用户授权接口，获取用户相关数据时，用于标识用户授权关系  |
| qr_pay_mode  | string  | 否  | 扫码支付方式  | 支付宝 => PC扫码支付的方式，支持前置模式和跳转模式。<br><br>前置模式是将二维码前置到商户的订单确认页的模式。需要商户在自己的页面中以 iframe 方式请求支付宝页面。具体分为以下几种：<br>0：订单码-简约前置模式，对应 iframe 宽度不能小于600px，高度不能小于300px；<br>1：订单码-前置模式，对应iframe 宽度不能小于 300px，高度不能小于600px；<br>3：订单码-迷你前置模式，对应 iframe 宽度不能小于 75px，高度不能小于75px；<br>4：订单码-可定义宽度的嵌入式二维码，商户可根据需要设定二维码的大小。<br><br>跳转模式下，用户的扫码界面是由支付宝生成的，不在商户的域名下。<br>2：订单码-跳转模式  |
| qrcode_width  | string  | 否  | 二维码宽度  | 支付宝 => 商户自定义二维码宽度<br>注：qr_pay_mode=4时该参数生效|
| integration_type  | string  | 否  | 商户号  | 支付宝 => 请求后页面的集成方式。取值范围：<br>1. ALIAPP：支付宝钱包内<br>2. PCWEB：PC端访问<br>默认值为PCWEB。  |

### 下单结果参数类（TradeResultParam）

| 属性  | 类型  | 是否必传  | 说明  | 备注  |
| ------------ | ------------ | ------------ | ------------ | ------------ |
| trade_type  | string  | 是  | 订单支付类型  | - |
| pay_url  | string  | 是  | 微信qrTrade：二维码链接<br>微信wapTrade：mweb_url微信支付收银台的中间页面<br>支付宝：拉起支付链接  | -  |
| prepay_id  | string  | 否  | 微信生成的预支付会话标识  | - |

## 贡献指南

- 如果发现了Bug， 欢迎提交 [Issue](https://github.com/MorriosL/MorriosPayment/issues)
- 如果要提交代码，欢迎提交 Pull request

## LICENSE

[MIT LICENSE](LICENSE) &copy; morrios