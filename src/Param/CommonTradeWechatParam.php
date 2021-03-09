<?php
/**
 * CommonTradeWechatParam.php
 *
 * User: LvShuai
 * Date: 2021/1/18
 * Email: <morrios@163.com>
 */

namespace Morrios\Payment\Param;


/**
 * 微信统一下单参数
 *
 * @package Morrios\Payment\Param
 */
class CommonTradeWechatParam extends CommonTradeParam
{
    /**
     * openid
     *
     * @var string
     */
    public $openid;
}