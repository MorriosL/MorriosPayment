<?php
/**
 * ConfigParam.php
 *
 * @author  Carl <morrios@163.com>
 * @ctime   2020/2/24 2:08 下午
 */

namespace Morrios\Payment\Param;


use Morrios\Base\Param\MorriosParam;

/**
 * 支付渠道配置参数类
 *
 * @package Morrios\Payment\Param
 */
class ConfigParam extends MorriosParam
{
    /**
     * 支付宝 => 支付宝分配给开发者的应用ID
     * 微信   => 公众账号ID|应用ID|小程序ID
     *
     * @var string
     */
    public $app_id;

    /**
     * 支付宝 => 应用私钥
     * 微信   => 商户平台设置的密钥key
     *
     * @var string
     */
    public $pay_key;

    /**
     * 微信 => 商户号（微信专用）
     *
     * @var string
     */
    public $mch_id;
}