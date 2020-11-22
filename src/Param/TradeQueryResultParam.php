<?php
/**
 * TradeQueryResultParam.php
 *
 * User: LvShuai
 * Date: 2020/11/3
 * Email: <morrios@163.com>
 */

namespace Morrios\Payment\Param;


/**
 * 订单查询结果参数类
 *
 * @package Morrios\Payment\Param
 */
class TradeQueryResultParam extends TradeCallbackResultParam
{
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
}