<?php
/**
 * PaymentFactory.php
 *
 * @author  Carl <morrios@163.com>
 * @ctime   2020/2/21 5:38 下午
 */

namespace Morrios\Payment;


use Morrios\Base\Exception\BusinessException;
//use Morrios\Payment\Channel\AlipayApplication;
use Morrios\Payment\Channel\BaseApplication;
use Morrios\Payment\Channel\WechatApplication;
use Morrios\Payment\Param\ConfigParam;

/**
 * Class PaymentFactory
 *
 * @package Morrios\Payment
// * @method static AlipayApplication Alipay(ConfigParam $configParam)
 * @method static WechatApplication Wechat(ConfigParam $configParam)
 */
class PaymentFactory
{
    /**
     * Dynamically pass methods to the application.
     *
     * @param $name
     * @param $arguments
     * @return BaseApplication
     * @throws BusinessException
     */
    public static function __callStatic($name, $arguments)
    {
        return self::make($name, ...$arguments);
    }

    /**
     * Generating class.
     *
     * @param string      $provider
     * @param ConfigParam $configParam
     * @return BaseApplication
     * @throws BusinessException
     */
    protected static function make(string $provider, ConfigParam $configParam): BaseApplication
    {
        $application = __NAMESPACE__ . "\\Channel\\{$provider}Application";

        if (class_exists($application)) {
            $application = new $application($configParam);

            if ($application instanceof BaseApplication) return $application;

            throw new BusinessException('Provider Must Be An Instance Of GatewayInterface');
        }

        throw new BusinessException('Provider Not Found');
    }
}