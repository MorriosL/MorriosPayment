<?php
/**
 * RefundQueryResultParam.php
 *
 * User: LvShuai
 * Date: 2020/11/3
 * Email: <morrios@163.com>
 */

namespace Morrios\Payment\Param;


use Morrios\Base\Param\MorriosParam;

/**
 * 退款查询结果参数类
 *
 * @package Morrios\Payment\Param
 */
class RefundQueryResultParam extends MorriosParam
{
    /**
     * 退款笔数
     *
     * @var int
     */
    public $refund_count;

    /**
     * out_refund_no
     *
     * @var string
     */
    public $out_refund_no;

    /**
     * refund_id
     *
     * @var string
     */
    public $refund_id;

    /**
     * refund_channel
     *
     * @var string
     */
    public $refund_channel;

    /**
     * refund_fee
     *
     * @var float
     */
    public $refund_fee;

    /**
     * refund_status
     *
     * @var string
     */
    public $refund_status;

    /**
     * refund_account
     *
     * @var string
     */
    public $refund_account;

    /**
     * refund_recv_accout
     *
     * @var string
     */
    public $refund_recv_accout;

    /**
     * refund_success_time
     *
     * @var string
     */
    public $refund_success_time;
}