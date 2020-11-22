<?php
/**
 * TradeCallbackResultParam.php
 *
 * @author  Carl <morrios@163.com>
 * @ctime   2020/3/1 5:25 下午
 */

namespace Morrios\Payment\Param;


use Morrios\Base\Param\MorriosParam;

/**
 * 订单支付回调参数类
 *
 * @package Morrios\Payment\Param
 */
class TradeCallbackResultParam extends MorriosParam
{
    /**
     * 付款银行
     *
     * @var string
     */
    public $bank_type;

    /**
     * 订单金额
     *
     * @var float
     */
    public $total_fee;

    /**
     * 现金支付金额
     *
     * @var float
     */
    public $cash_fee;

    /**
     * 商户订单号
     *
     * @var string
     */
    public $out_trade_no;

    /**
     * 支付渠道订单号
     *
     * @var string
     */
    public $transaction_id;

    /**
     * 透传参数
     *
     * @var string
     */
    public $attach;

    /**
     * 支付完成时间
     *
     * @var string
     */
    public $time_end;
}