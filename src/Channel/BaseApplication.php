<?php
/**
 * BaseApplication.php
 *
 * @author  Carl <morrios@163.com>
 * @ctime   2020/2/24 2:09 下午
 */

namespace Morrios\Payment\Channel;


use Morrios\Payment\Exception\PaymentChannelException;
use Morrios\Payment\Param\RefundParam;
use Morrios\Payment\Param\RefundResultParam;
use Morrios\Payment\Param\ConfigParam;
use Morrios\Payment\Param\TradeQueryParam;
use Morrios\Payment\Param\CommonTradeParam;
use Morrios\Payment\Param\CommonTradeResultParam;
use Morrios\Payment\Param\TradeResultParam;

/**
 * Class BaseApplication
 *
 * @package Morrios\Payment\Channel
 */
abstract class BaseApplication
{
    /**
     * 支付渠道配置
     *
     * @var ConfigParam
     */
    protected $config;

    /**
     * BaseApplication constructor.
     *
     * @param ConfigParam $configParam
     */
    public function __construct(ConfigParam $configParam)
    {
        $this->config = $configParam;
    }

    /**
     * 统一下单
     *
     * @param string           $tradeType
     * @param CommonTradeParam $tradeParam
     * @return CommonTradeResultParam
     * @throws PaymentChannelException
     */
    abstract protected function commonTrade(string $tradeType, CommonTradeParam $tradeParam): CommonTradeResultParam;

    /**
     * 订单状态回调
     *
     * @return TradeResultParam
     * @throws PaymentChannelException
     */
    abstract public function tradeCallback(): TradeResultParam;

    /**
     * 订单查询
     *
     * @param TradeQueryParam $queryParam
     * @return TradeResultParam
     * @throws PaymentChannelException
     */
    abstract public function tradeQuery(TradeQueryParam $queryParam): TradeResultParam;

    /**
     * 关闭订单
     *
     * @param string $outTradeNo
     * @return void
     * @throws PaymentChannelException
     */
    abstract public function tradeClose(string $outTradeNo);

    /**
     * 发起退款
     *
     * @param RefundParam $refundParam
     * @return RefundResultParam
     * @throws PaymentChannelException
     */
    abstract public function refund(RefundParam $refundParam): RefundResultParam;

    /**
     * 退款状态回调
     *
     * @return RefundResultParam
     * @throws PaymentChannelException
     */
    abstract public function refundCallback(): RefundResultParam;

    /**
     * 退款查询
     *
     * @param string $outTradeNo
     * @return RefundResultParam
     * @throws PaymentChannelException
     */
    abstract public function refundQuery(string $outTradeNo): RefundResultParam;

    /**
     * 获取回调参数
     *
     * @return string
     */
    abstract public function getCallbackParams(): string;

    /**
     * 回调响应
     *
     * @param bool $success
     * @return string
     */
    abstract public function callbackResponse(bool $success): string;

    /**
     * 签名参数
     *
     * @param array $signData
     * @return array
     */
    abstract protected function signParams(array $signData): array;

    /**
     * 签名验证
     *
     * @return bool
     * @author LvShuai
     */
    abstract protected function signVerify(): bool;
}