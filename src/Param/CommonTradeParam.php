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
     * 回调地址
     *
     * @var string
     */
    public $notify_url;

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
    public $attach = '';

    /**
     * 商品详细描述
     *
     * @var string
     */
    public $detail;
}