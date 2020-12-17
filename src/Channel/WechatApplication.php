<?php
/**
 * WechatApplication.php
 *
 * @author  Carl <morrios@163.com>
 * @ctime   2020/2/24 11:53 上午
 */

namespace Morrios\Payment\Channel;


use Exception;
use Morrios\Base\Exception\BusinessException;
use Morrios\Base\Helper\{GuzzleHelper, StringHelper, XmlHelper};
use Morrios\Payment\Constant\WechatApi;
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
 * Class WechatApplication
 *
 * @package Morrios\Payment\Channel
 */
class WechatApplication extends BaseApplication
{
    /**
     * 签名类型
     *
     * @var string
     */
    protected $signType = 'MD5';

    /**
     * WechatApplication constructor.
     *
     * @param ConfigParam $configParam
     */
    public function __construct(ConfigParam $configParam)
    {
        parent::__construct($configParam);

        $this->guzzleClient = GuzzleHelper::instance(WechatApi::BASE_URI);
        $this->guzzleClient->setParamsType('body');
    }

    /**
     * APP支付
     *
     * @param CommonTradeParam $tradeParam
     * @return CommonTradeResultParam
     * @throws BusinessException
     */
    public function appTrade(CommonTradeParam $tradeParam): CommonTradeResultParam
    {
        return $this->commonTrade('APP', $tradeParam);
    }

    /**
     * 扫码支付
     *
     * @param CommonTradeParam $tradeParam
     * @return CommonTradeResultParam
     * @throws BusinessException
     */
    public function qrTrade(CommonTradeParam $tradeParam): CommonTradeResultParam
    {
        return $this->commonTrade('NATIVE', $tradeParam);
    }

    /**
     * WAP支付
     *
     * @param CommonTradeParam $tradeParam
     * @return CommonTradeResultParam
     * @throws BusinessException
     */
    public function wapTrade(CommonTradeParam $tradeParam): CommonTradeResultParam
    {
        return $this->commonTrade('MWEB', $tradeParam);
    }

    /**
     * JsApi支付
     *
     * @param CommonTradeParam $tradeParam
     * @return CommonTradeResultParam
     * @throws BusinessException
     */
    public function jsApiTrade(CommonTradeParam $tradeParam): CommonTradeResultParam
    {
        return $this->commonTrade('JSAPI', $tradeParam);
    }

    /**
     * 小程序支付
     *
     * @param string $prepayId
     * @return array
     * @throws Exception
     * @author LvShuai
     */
    public function mpTrade(string $prepayId): array
    {
        $tradeParams = [
            'appId'     => $this->config->app_id,
            'timeStamp' => time(),
            'nonceStr'  => StringHelper::random(),
            'package'   => 'prepay_id=' . $prepayId,
            'signType'  => $this->signType,
        ];

        $tradeParams['paySign'] = $this->getSign($tradeParams);

        return $tradeParams;
    }

    /**
     * @inheritDoc
     */
    protected function commonTrade(string $tradeType, CommonTradeParam $tradeParam): CommonTradeResultParam
    {
        try {
            // 下单参数
            $params         = array_filter([
                'appid'            => $this->config->app_id,
                'mch_id'           => $this->config->mch_id,
                'device_info'      => $tradeParam->device_info,
                'nonce_str'        => StringHelper::random(),
                'sign_type'        => $this->signType,
                'body'             => $tradeParam->subject,
                'detail'           => $tradeParam->detail,
                'attach'           => $tradeParam->attach,
                'out_trade_no'     => $tradeParam->order_no,
                'fee_type'         => 'CNY',
                'total_fee'        => $tradeParam->money * 100,
                'spbill_create_ip' => $tradeParam->client_ip ?: $_SERVER['REMOTE_ADDR'],
                'goods_tag'        => $tradeParam->discount,
                'notify_url'       => $tradeParam->notify_url,
                'trade_type'       => $tradeType,
                'product_id'       => $tradeParam->product_id,
                'limit_pay'        => $tradeParam->disable_pay_channels,
                'openid'           => $tradeParam->openid,
                'receipt'          => $tradeParam->receipt,
                'scene_info'       => $tradeParam->scene_info,
            ]);
            $params['sign'] = $this->getSign($params);

            // 请求下单
            $result = XmlHelper::xmlToArray($this->guzzleClient->post(WechatApi::TRADE, XmlHelper::arrayToXml($params)));

            // 处理下单结果
            if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
                return new CommonTradeResultParam([
                    'trade_type' => $result['trade_type'],
                    'pay_url'    => $result['code_url'] ?? $result['mweb_url'] ?? '',
                    'prepay_id'  => $result['prepay_id'],
                ]);
            } else {
                throw new BusinessException($result['return_msg'] ?? $result['err_code_des'] ?? '微信响应异常');
            }
        } catch (Exception $exception) {
            throw new BusinessException($exception->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function tradeCallback(): TradeCallbackResultParam
    {
        // 获取请求参数
        $result = XmlHelper::xmlToArray(file_get_contents('php://input'));

        // 处理回调结果
        if ($result && $result['return_code'] == 'SUCCESS' && $result['return_msg'] == 'OK') {

            // 校验签名
            if ($result['sign'] != $this->getSign($result)) throw new BusinessException('回调签名错误');

            // 金额单位转换
            $result['total_fee'] /= 100;
            $result['cash_fee']  /= 100;

            // 响应参数
            $tradeCallbackResultParam                = new TradeCallbackResultParam($result);
            $tradeCallbackResultParam->origin_params = file_get_contents('php://input');

            return $tradeCallbackResultParam;
        } else {
            throw new BusinessException($result['return_msg'] ?? 'Callback content is null.');
        }
    }

    /**
     * @inheritDoc
     */
    public function tradeCallbackResponse(bool $success): string
    {
        if ($success) {
            return XmlHelper::arrayToXml(['return_code' => 'SUCCESS', 'return_msg' => 'OK']);
        } else {
            return XmlHelper::arrayToXml(['return_code' => 'FAIL', 'return_msg' => 'FAIL']);
        }
    }

    /**
     * @inheritdoc
     */
    public function tradeQuery(TradeQueryParam $queryParam): TradeQueryResultParam
    {
        // 查询参数
        try {
            $params         = [
                'appid'          => $this->config->app_id,
                'mch_id'         => $this->config->mch_id,
                'transaction_id' => $queryParam->transaction_id,
                'out_trade_no'   => $queryParam->out_trade_no,
                'nonce_str'      => StringHelper::random(),
                'sign_type'      => $this->signType,
            ];
            $params['sign'] = $this->getSign($params);

            // 请求查询
            $result = XmlHelper::xmlToArray($this->guzzleClient->post(WechatApi::TRADE_QUERY, XmlHelper::arrayToXml($params)));

            // 处理查询结果
            if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
                return new TradeQueryResultParam($result);
            } else {
                throw new BusinessException($result['return_msg']);
            }
        } catch (Exception $exception) {
            throw new BusinessException($exception->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function refund(RefundParam $refundParam): RefundResultParam
    {
        try {
            // 退款参数
            $params         = [
                'appid'           => $this->config->app_id,
                'mch_id'          => $this->config->mch_id,
                'nonce_str'       => StringHelper::random(),
                'sign_type'       => $this->signType,
                'transaction_id'  => $refundParam->transaction_id,
                'out_trade_no'    => $refundParam->out_trade_no,
                'out_refund_no'   => $refundParam->out_refund_no,
                'total_fee'       => $refundParam->total_fee * 100,
                'refund_fee'      => $refundParam->refund_fee * 100,
                'refund_fee_type' => 'CNY',
                'refund_desc'     => $refundParam->refund_desc,
                'notify_url'      => $refundParam->notify_url,
            ];
            $params['sign'] = $this->getSign($params);

            // 请求退款
            $result = XmlHelper::xmlToArray($this->guzzleClient->post(WechatApi::REFUND, XmlHelper::arrayToXml($params)));

            // 处理退款结果
            if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
                // 金额单位转换
                $result['total_fee']  /= 100;
                $result['refund_fee'] /= 100;

                return new RefundResultParam($result);
            } else {
                throw new BusinessException($result['return_msg']);
            }
        } catch (Exception $exception) {
            throw new BusinessException($exception->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function refundCallback(): RefundCallbackResultParam
    {
        // TODO: Implement refundCallback() method.
    }

    /**
     * @inheritDoc
     */
    public function refundQuery(RefundQueryParam $refundQueryParam): RefundQueryResultParam
    {
        // 查询参数
        $params         = [
            'appid'          => $this->config->app_id,
            'mch_id'         => $this->config->mch_id,
            'nonce_str'      => StringHelper::random(),
            'transaction_id' => $refundQueryParam->transaction_id,
            'out_trade_no'   => $refundQueryParam->out_trade_no,
            'out_refund_no'  => $refundQueryParam->out_refund_no,
            'refund_id'      => $refundQueryParam->refund_id,
        ];
        $params['sign'] = $this->getSign($params);

        // 请求查询
        $result = XmlHelper::xmlToArray($this->guzzleClient->post(WechatApi::REFUND_QUERY, XmlHelper::arrayToXml($params)));

        if ($result['return_code'] == 'SUCCESS' && $result['result_code'] == 'SUCCESS') {
            return new RefundQueryResultParam([
                'refund_count' => $result['refund_count'],
            ]);
        } else {
            throw new BusinessException($result['return_msg']);
        }
    }

    /**
     * @inheritDoc
     */
    protected function getSign(array $signData): string
    {
        $sign = '';

        ksort($signData);

        foreach ($signData as $key => $value) {
            if ($value != '' && $key != 'sign' && !is_array($value)) $sign .= $key . '=' . $value . '&';
        }

        $sign .= 'key=' . $this->config->pay_key;

        return strtoupper(md5($sign));
    }
}