<?php
/**
 * PaymentChannelException.php
 *
 * User: LvShuai
 * Date: 2021/1/11
 * Email: <morrios@163.com>
 */

namespace Morrios\Payment\Exception;


use Morrios\Base\Exception\MorriosException;

/**
 * 支付渠道异常
 *
 * @package Morrios\Payment\Exception
 */
class PaymentChannelException extends MorriosException
{
    /**
     * BusinessException constructor.
     *
     * @param string $code
     * @param string $errorMessage
     */
    public function __construct(string $code, string $errorMessage)
    {
        $this->errorCode    = $code;
        $this->errorMessage = 'Payment Channel Error:' . $errorMessage;

        parent::__construct();
    }
}