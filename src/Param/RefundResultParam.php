<?php
/**
 * RefundResultParam.php
 *
 * User: LvShuai
 * Date: 2020/11/3
 * Email: <morrios@163.com>
 */

namespace Morrios\Payment\Param;


use Morrios\Base\Param\MorriosParam;

/**
 * 发起退款结果参数类
 *
 * @package Morrios\Payment\Param
 */
class RefundResultParam extends MorriosParam
{
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
     * 支付渠道订单号
     *
     * @var string
     */
    public $transaction_id;

    /**
     * 支付渠道退款订单号
     *
     * @var string
     */
    public $refund_id;

    /**
     * 退款金额
     *
     * @var float
     */
    public $refund_fee;
}