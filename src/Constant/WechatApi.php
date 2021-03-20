<?php
/**
 * WechatApi.php
 *
 * @author  Carl <morrios@163.com>
 * @ctime   2020/2/24 1:32 下午
 */

namespace Morrios\Payment\Constant;


/**
 * 微信API枚举类
 *
 * @package Morrios\Payment\Constant
 */
class WechatApi
{
    // BASE URI
    const BASE_URI = 'https://api.mch.weixin.qq.com';

    // 订单
    const TRADE = '/v3/pay/transactions/';

    // 退款
    const REFUND = '/v3/refund/domestic/refunds';
}