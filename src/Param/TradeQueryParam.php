<?php
/**
 * TradeQueryParam.php
 *
 * User: LvShuai
 * Date: 2020/11/3
 * Email: <morrios@163.com>
 */

namespace Morrios\Payment\Param;


use Morrios\Base\Param\MorriosParam;

/**
 * 订单查询参数类
 *
 * @package Morrios\Payment\Param
 */
class TradeQueryParam extends MorriosParam
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
}