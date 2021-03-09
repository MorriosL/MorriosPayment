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
     * 支付渠道退款订单号
     *
     * @var string
     */
    public $refund_id;

    /**
     * 商户退款订单号
     *
     * @var string
     */
    public $out_refund_no;

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
     * 退款渠道
     *
     * @var string
     */
    public $channel;

    /**
     * 退款入账账户
     *
     * @var string
     */
    public $user_received_account;

    /**
     * 退款创建时间
     *
     * @var string
     */
    public $create_time;

    /**
     * 退款成功时间
     *
     * @var string
     */
    public $success_time;

    /**
     * 退款状态
     *
     * @var string
     */
    public $status;

    /**
     * 资金账户
     *
     * @var string
     */
    public $funds_account;

    /**
     * 用户支付金额
     *
     * @var float
     */
    public $payer_total;

    /**
     * 用户退款金额
     *
     * @var float
     */
    public $payer_refund;
}