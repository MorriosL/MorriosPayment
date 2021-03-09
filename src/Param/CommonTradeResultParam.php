<?php
/**
 * CommonTradeResultParam.php
 *
 * @author  Carl <morrios@163.com>
 * @ctime   2020/2/24 1:14 下午
 */

namespace Morrios\Payment\Param;


use Morrios\Base\Param\MorriosParam;

/**
 * 下单结果参数类
 *
 * @package Morrios\Payment\Param
 */
class CommonTradeResultParam extends MorriosParam
{
    /**
     * 订单支付类型
     *
     * @var string
     */
    public $trade_type;

    /**
     * 支付链接
     *
     * @var string
     */
    public $pay_url;

    /**
     * 微信生成的预支付会话标识
     *
     * @var string
     */
    public $prepay_id;

    /**
     * 二维码链接
     *
     * @var string
     */
    public $code_url;
}