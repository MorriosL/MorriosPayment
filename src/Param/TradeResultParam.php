<?php
/**
 * TradeResultParam.php
 *
 * User: LvShuai
 * Date: 2021/3/9
 * Email: <morrios@163.com>
 */

namespace Morrios\Payment\Param;


use Morrios\Base\Param\MorriosParam;

/**
 * Class TradeResultParam
 *
 * @package Morrios\Payment\Param
 */
class TradeResultParam extends MorriosParam
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
     * 交易类型
     *
     * @var string
     */
    public $trade_type;

    /**
     * 交易状态
     *
     * @var string
     */
    public $trade_state;

    /**
     * 交易状态描述
     *
     * @var string
     */
    public $trade_state_desc;

    /**
     * 付款银行
     *
     * @var string
     */
    public $bank_type;

    /**
     * 附加数据
     *
     * @var string
     */
    public $attach;

    /**
     * 支付完成时间
     *
     * @var string
     */
    public $success_time;

    /**
     * 用户标识
     *
     * @var string
     */
    public $openid;

    /**
     * 总金额
     *
     * @var float
     */
    public $total;

    /**
     * 用户支付金额
     *
     * @var float
     */
    public $payer_total;
}