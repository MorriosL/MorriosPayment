<?php
/**
 * CommonTradeParam.php
 *
 * @author  Carl <morrios@163.com>
 * @ctime   2020/2/24 12:00 下午
 */

namespace Morrios\Payment\Param;


use Morrios\Base\Param\MorriosParam;

/**
 * 统一下单参数类
 *
 * @package Morrios\Payment\Param
 */
class CommonTradeParam extends MorriosParam
{
    /**
     * 系统内部订单号
     *
     * @var string
     */
    public $order_no;

    /**
     * 订单金额
     *
     * @var float
     */
    public $money;

    /**
     * 订单标题
     *
     * @var string
     */
    public $subject;

    /**
     * 回调地址
     *
     * @var string
     */
    public $notify_url;

    /**
     * 商品ID（微信NATIVE必传）
     *
     * @var string
     */
    public $product_id;

    /**
     * 微信openid（微信JSAPI必传）
     *
     * @var string
     */
    public $openid;

    /**
     * 客户端IP
     *
     * @var string
     */
    public $client_ip;

    /**
     * 透传参数
     *
     * @var string
     */
    public $attach;

    /**
     * 终端设备号
     *
     * @var string
     */
    public $device_info;

    /**
     * 订单优惠标记（微信）
     * 优惠参数（支付宝）
     *
     * @var string
     */
    public $discount;

    /**
     * 可用渠道，用户只能在指定渠道范围内支付
     *
     * @var string
     */
    public $enable_pay_channels;

    /**
     * 禁用渠道，用户不可用指定渠道支付
     *
     * @var string
     */
    public $disable_pay_channels;

    /**
     * 商品详细描述
     *
     * @var string
     */
    public $detail;

    /**
     * 电子发票入口开放标识（微信专用）
     *
     * @var string
     */
    public $receipt;

    /**
     * 场景信息（微信专用）Json String
     *
     * @var string
     */
    public $scene_info;

    /**
     * 跳回地址（支付宝专用）
     *
     * @var string
     */
    public $return_url;

    /**
     * 应用授权Token（支付宝专用）
     *
     * @var string
     */
    public $app_auth_token;

    /**
     * 商品主类型 :0-虚拟类商品,1-实物类商品
     *
     * @var int
     */
    public $goods_type;

    /**
     * 用户付款中途退出返回商户网站的地址（支付宝专用）
     *
     * @var string
     */
    public $quit_url;

    /**
     * 针对用户授权接口，获取用户相关数据时，用于标识用户授权关系（支付宝专用）
     *
     * @var string
     */
    public $auth_token;

    /**
     * PC扫码支付的方式，支持 简约前置模式(0)、前置模式(1)、跳转模式(2)、迷你前置模式(3)、可定义宽度的嵌入式二维码(4)
     *
     * @var int
     */
    public $qr_pay_mode;

    /**
     * 商户自定义二维码宽度
     *
     * @var int
     */
    public $qrcode_width;

    /**
     * 请求后页面的集成方式，支持 ALIAPP：支付宝钱包内、PCWEB：PC端访问
     *
     * @var string
     */
    public $integration_type;
}