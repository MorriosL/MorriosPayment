<?php
/**
 * WechatApi.php
 *
 * @author  Carl <morrios@163.com>
 * @ctime   2020/2/24 1:32 下午
 */

namespace Morrios\Payment\Constant;


/**
 * Class WechatApi
 *
 * @package Morrios\Payment\Constant
 */
class WechatApi
{
    // URI
    const BASE_URI = 'https://api.mch.weixin.qq.com';

    // 统一下单
    const TRADE = '/pay/unifiedorder';

    // 查询订单
    const TRADE_QUERY = '/pay/orderquery';

    // 申请退款
    const REFUND = '/secapi/pay/refund';

    // 退款查询
    const REFUND_QUERY = '/pay/refundquery';
}