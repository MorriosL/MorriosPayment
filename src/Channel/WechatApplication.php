<?php
/**
 * WechatApplication.php
 *
 * User: LvShuai
 * Date: 2021/1/7
 * Email: <morrios@163.com>
 */

namespace Morrios\Payment\Channel;


use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use Morrios\Base\Helper\GuzzleHelper;
use Morrios\Base\Helper\StringHelper;
use Morrios\Payment\Constant\WechatApi;
use Morrios\Payment\Exception\PaymentChannelException;
use Morrios\Payment\Param\CommonTradeParam;
use Morrios\Payment\Param\CommonTradeResultParam;
use Morrios\Payment\Param\ConfigParam;
use Morrios\Payment\Param\RefundParam;
use Morrios\Payment\Param\RefundResultParam;
use Morrios\Payment\Param\TradeQueryParam;
use Morrios\Payment\Param\TradeResultParam;
use Morrios\Payment\Param\CommonTradeWechatParam;
use WechatPay\GuzzleMiddleware\Util\AesUtil;
use WechatPay\GuzzleMiddleware\Util\PemUtil;
use WechatPay\GuzzleMiddleware\WechatPayMiddleware;

/**
 * 微信渠道
 *
 * @package Morrios\Payment\Channel
 */
class WechatApplication extends BaseApplication
{
    /**
     * GuzzleClient
     *
     * @var GuzzleHelper
     */
    private $_guzzleClient;

    /**
     * WechatApplication constructor.
     *
     * @param ConfigParam $configParam
     */
    public function __construct(ConfigParam $configParam)
    {
        parent::__construct($configParam);

        if (!$this->_guzzleClient instanceof GuzzleHelper) {
            // 创建Guzzle客户端
            $stack = HandlerStack::create();
            $stack->push(
                WechatPayMiddleware::builder()
                    ->withMerchant($this->config->mch_id, $this->config->certificate_serial_no, PemUtil::loadPrivateKey($this->config->private_key))
                    ->withWechatPay([PemUtil::loadCertificate($this->config->certificate)])
                    ->build(),
                'wechatPay'
            );

            $this->_guzzleClient = GuzzleHelper::instance(WechatApi::BASE_URI, $stack);
            $this->_guzzleClient->setParamsType('json');
        }

    }

    /**
     * JsApi支付
     *
     * @param CommonTradeWechatParam $tradeParam
     * @return array
     * @throws PaymentChannelException
     * @author LvShuai
     */
    public function jsApiTrade(CommonTradeWechatParam $tradeParam): array
    {
        return $this->signParams([
            'appId'     => $this->config->app_id,
            'timeStamp' => (string)time(),
            'nonceStr'  => StringHelper::random(),
            'package'   => 'prepay_id=' . $this->commonTrade('JSAPI', $tradeParam)->prepay_id,
        ]);
    }

    /**
     * APP支付
     *
     * @param CommonTradeWechatParam $tradeParam
     * @return string
     * @throws PaymentChannelException
     * @author LvShuai
     */
    public function appTrade(CommonTradeWechatParam $tradeParam): string
    {
        return $this->commonTrade('APP', $tradeParam)->prepay_id;
    }

    /**
     * H5支付
     *
     * @param CommonTradeWechatParam $tradeParam
     * @return string
     * @throws PaymentChannelException
     * @author LvShuai
     */
    public function h5Trade(CommonTradeWechatParam $tradeParam): string
    {
        return $this->commonTrade('H5', $tradeParam)->pay_url;
    }

    /**
     * 扫码支付
     *
     * @param CommonTradeWechatParam $tradeParam
     * @return string
     * @throws PaymentChannelException
     * @author LvShuai
     */
    public function nativeTrade(CommonTradeWechatParam $tradeParam): string
    {
        return $this->commonTrade('NATIVE', $tradeParam)->code_url;
    }

    /**
     * @inheritDoc
     */
    protected function commonTrade(string $tradeType, CommonTradeParam $tradeParam): CommonTradeResultParam
    {
        try {
            /** @var CommonTradeWechatParam $tradeParam */
            $params = [
                'appid'        => $this->config->app_id,
                'mchid'        => $this->config->mch_id,
                'description'  => $tradeParam->detail,
                'out_trade_no' => $tradeParam->order_no,
                'attach'       => $tradeParam->attach,
                'notify_url'   => $tradeParam->notify_url,
                'amount'       => ['total' => $tradeParam->money * 100],
                'scene_info'   => ['payer_client_ip' => $tradeParam->client_ip],
            ];
            if ($tradeType == 'JSAPI') $params['payer'] = ['openid' => $tradeParam->openid];

            return new CommonTradeResultParam($this->_guzzleClient->post(WechatApi::TRADE . strtolower($tradeType), $params, [], ['Accept' => 'application/json']));
        } catch (RequestException $requestException) {
            $requestException->getRequest()->getBody()->rewind();
            $response = json_decode($requestException->getResponse()->getBody()->getContents(), true);

            throw new PaymentChannelException($response['code'] . '-' . $response['message']);
        }
    }

    /**
     * @inheritdoc
     */
    public function tradeCallback(): TradeResultParam
    {
        // 签名验证
        if (!$this->signVerify()) throw new PaymentChannelException('签名验证失败', 400);

        // 解析通知参数
        $callbackParams = json_decode($this->getCallbackParams(), true);

        // 解析通知结果
        $callbackResult = json_decode((new AesUtil($this->config->pay_key))->decryptToString(
            $callbackParams['resource']['associated_data'],
            $callbackParams['resource']['nonce'],
            $callbackParams['resource']['ciphertext']
        ), true);
        if ($callbackResult['trade_state'] != 'SUCCESS') throw new PaymentChannelException($callbackResult['trade_state'] . '-' . $callbackResult['trade_state_desc']);

        return new TradeResultParam([
            'out_trade_no'   => $callbackResult['out_trade_no'],
            'transaction_id' => $callbackResult['transaction_id'],
            'total'          => $callbackResult['amount']['total'] / 100,
            'payer_total'    => $callbackResult['amount']['payer_total'] / 100,
            'success_time'   => date_create($callbackResult['success_time'])->format('Y-m-d H:i:s'),
            'attach'         => $callbackResult['attach'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function tradeClose(string $outTradeNo)
    {
        try {
            $this->_guzzleClient->post(WechatApi::TRADE . 'out-trade-no/' . $outTradeNo . '/close', ['mchid' => $this->config->mch_id], ['Accept' => 'application/json']);
        } catch (RequestException $requestException) {
            $requestException->getRequest()->getBody()->rewind();
            $response = json_decode($requestException->getResponse()->getBody()->getContents(), true);

            throw new PaymentChannelException($response['code'] . '-' . $response['message']);
        }
    }

    /**
     * @inheritdoc
     */
    public function tradeQuery(TradeQueryParam $queryParam): TradeResultParam
    {
        try {
            return new TradeResultParam($this->_guzzleClient->get(
                WechatApi::TRADE . ($queryParam->transaction_id ? 'id/' . $queryParam->transaction_id : 'out-trade-no/' . $queryParam->out_trade_no),
                ['mchid' => $this->config->mch_id],
                ['Accept' => 'application/json']
            ));
        } catch (RequestException $requestException) {
            $requestException->getRequest()->getBody()->rewind();
            $response = json_decode($requestException->getResponse()->getBody()->getContents(), true);

            throw new PaymentChannelException($response['code'] . '-' . $response['message']);
        }
    }

    /**
     * @inheritdoc
     */
    public function refund(RefundParam $refundParam): RefundResultParam
    {
        try {
            $params = [
                'out_refund_no' => $refundParam->out_refund_no,
                'notify_url'    => $refundParam->notify_url,
                'reason'        => $refundParam->refund_desc,
                'amount'        => [
                    'refund'   => $refundParam->refund_fee * 100,
                    'total'    => $refundParam->total_fee * 100,
                    'currency' => 'CNY',
                ],
            ];
            if ($refundParam->transaction_id) {
                $params['transaction_id'] = $refundParam->transaction_id;
            } elseif ($refundParam->out_trade_no) {
                $params['out_trade_no'] = $refundParam->out_trade_no;
            } else {
                throw new PaymentChannelException('transaction_id或out_trade_no二选一必填');
            }

            $result = $this->_guzzleClient->post(WechatApi::REFUND, $params, [], ['Accept' => 'application/json']);

            return new RefundResultParam($result + $result['amount']);
        } catch (RequestException $requestException) {
            $requestException->getRequest()->getBody()->rewind();
            $response = json_decode($requestException->getResponse()->getBody()->getContents(), true);

            throw new PaymentChannelException($response['code'] . '-' . $response['message']);
        }

    }

    /**
     * @inheritdoc
     */
    public function refundCallback(): RefundResultParam
    {
        // 签名验证
        if (!$this->signVerify()) throw new PaymentChannelException('签名验证失败', 400);

        // 解析通知参数
        $callbackParams = json_decode($this->getCallbackParams(), true);

        // 解析通知结果
        $callbackResult = json_decode((new AesUtil($this->config->pay_key))->decryptToString(
            $callbackParams['resource']['associated_data'],
            $callbackParams['resource']['nonce'],
            $callbackParams['resource']['ciphertext']
        ), true);
        if ($callbackResult['trade_state'] != 'SUCCESS') throw new PaymentChannelException($callbackResult['trade_state'] . '-' . $callbackResult['trade_state_desc']);

        return new RefundResultParam($callbackResult + $callbackResult['amount']);
    }

    /**
     * @inheritdoc
     */
    public function refundQuery(string $outTradeNo): RefundResultParam
    {
        try {
            return new RefundResultParam($this->_guzzleClient->get(WechatApi::REFUND . '/' . $outTradeNo, [], ['Accept' => 'application/json']));
        } catch (RequestException $requestException) {
            $requestException->getRequest()->getBody()->rewind();
            $response = json_decode($requestException->getResponse()->getBody()->getContents(), true);

            throw new PaymentChannelException($response['code'] . '-' . $response['message']);
        }
    }

    /**
     * @inheritdoc
     */
    public function getCallbackParams(): string
    {
        return file_get_contents('php://input');
    }

    /**
     * @inheritdoc
     */
    public function callbackResponse(bool $success): string
    {
        return json_encode([
            'code'    => $success ? 'SUCCESS' : 'FAIL',
            'message' => $success ? '成功' : '失败',
        ]);
    }

    /**
     * @inheritDoc
     */
    protected function signParams(array $signData): array
    {
        openssl_sign(implode("\n", $signData) . "\n", $sign, PemUtil::loadPrivateKey($this->config->private_key), 'sha256WithRSAEncryption');

        $signData['signType'] = 'RSA';
        $signData['paySign']  = base64_encode($sign);

        return $signData;
    }

    /**
     * @inheritdoc
     */
    protected function signVerify(): bool
    {
        $certificate = PemUtil::loadCertificate($this->config->certificate);

        if (PemUtil::parseCertificateSerialNo($certificate) !== $_SERVER['HTTP_WECHATPAY_SERIAL']) return false;

        $publicKey = openssl_pkey_get_public($certificate);

        $result = openssl_verify(implode("\n", [
            $_SERVER['HTTP_WECHATPAY_TIMESTAMP'],
            $_SERVER['HTTP_WECHATPAY_NONCE'],
            $this->getCallbackParams(),
        ]) . "\n", base64_decode($_SERVER['HTTP_WECHATPAY_SIGNATURE']), $publicKey, OPENSSL_ALGO_SHA256);

        openssl_free_key($publicKey);

        return $result == 1;
    }
}