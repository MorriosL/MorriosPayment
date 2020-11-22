<?php
/**
 * RefundParam.php
 *
 * User: LvShuai
 * Date: 2020/11/3
 * Email: <morrios@163.com>
 */

namespace Morrios\Payment\Param;


use Morrios\Base\Param\MorriosParam;

/**
 * 发起退款参数类
 *
 * @package Morrios\Payment\Param
 */
class RefundParam extends MorriosParam
{
    /**
     * 支付渠道订单号
     *
     * @var string
     */
    public $transaction_id;

    /**
     * 商户订单号
     *
     * @var string
     */
    public $out_trade_no;

    /**
     * 商户退款订单号
     *
     * @var string
     */
    public $out_refund_no;

    /**
     * 订单总金额
     *
     * @var float
     */
    public $total_fee;

    /**
     * 退款金额
     *
     * @var float
     */
    public $refund_fee;

    /**
     * 退款原因
     *
     * @var string
     */
    public $refund_desc;

    /**
     * 退款结果通知url
     *
     * @var string
     */
    public $notify_url;

}