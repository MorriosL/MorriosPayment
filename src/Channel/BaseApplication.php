<?php
/**
 * BaseApplication.php
 *
 * @author  Carl <morrios@163.com>
 * @ctime   2020/2/24 2:09 下午
 */

namespace Morrios\Payment\Channel;


use Morrios\Base\Exception\BusinessException;
use Morrios\Base\Helper\GuzzleHelper;
use Morrios\Payment\Param\{
    RefundCallbackResultParam,
    RefundParam,
    RefundQueryParam,
    RefundQueryResultParam,
    RefundResultParam,
    TradeCallbackResultParam,
    ConfigParam,
    TradeQueryParam,
    TradeQueryResultParam,
    CommonTradeParam,
    CommonTradeResultParam
};


/**
 * Class BaseApplication
 *
 * @package Morrios\Payment\Channel
 */
abstract class BaseApplication
{
    /**
     * @var GuzzleHelper
     */
    protected $guzzleClient;

    /**
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
     * @throws BusinessException
     */
    abstract protected function commonTrade(string $tradeType, CommonTradeParam $tradeParam): CommonTradeResultParam;

    /**
     * 订单支付回调
     *
     * @return TradeCallbackResultParam
     * @throws BusinessException
     */
    abstract public function tradeCallback(): TradeCallbackResultParam;

    /**
     * 订单查询
     *
     * @param TradeQueryParam $queryParam
     * @return TradeQueryResultParam
     * @throws BusinessException
     */
    abstract public function tradeQuery(TradeQueryParam $queryParam): TradeQueryResultParam;

    /**
     * 发起退款
     *
     * @param RefundParam $refundParam
     * @return RefundResultParam
     * @throws BusinessException
     */
    abstract public function refund(RefundParam $refundParam): RefundResultParam;

    /**
     * 退款回调
     *
     * @return RefundCallbackResultParam
     */
    abstract public function refundCallback(): RefundCallbackResultParam;

    /**
     * 退款查询
     *
     * @param RefundQueryParam $refundQueryParam
     * @return RefundQueryResultParam
     * @throws BusinessException
     */
    abstract public function refundQuery(RefundQueryParam $refundQueryParam): RefundQueryResultParam;

    /**
     * 获取签名
     *
     * @param array $signData
     * @return string
     */
    abstract protected function getSign(array $signData): string;
}