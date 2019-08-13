<?php

namespace Vindi\Payment\Plugin;

use Magento\Sales\Model\Order\Payment;
use Vindi\Payment\Helper\Data;
use Vindi\Payment\Model\Payment\BankSlip;
use Vindi\Payment\Model\Payment\Vindi;

class SetOrderStatusOnPlace
{
    /**
     * @var Data
     */
    private $helperData;

    /**
     * SetOrderStatusOnPlace constructor.
     * @param Data $helperData
     */
    public function __construct(
        Data $helperData
    ) {
        $this->helperData = $helperData;
    }

    public function afterPlace(Payment $subject, $result)
    {
        $payment_status = $subject->getAdditionalInformation('vindi_bill_status'); 
        if (Bill::REVIEW_STATUS === $payment_status
            || Bill::REVIEW_STATUS === $payment_status
            || BankSlip::CODE === $subject->getMethod()) {
            $this->pendingStatus($subject);
        } else {
            $this->completeStatus($subject);
        }
        return $result;
    }

    /**
     * @param Payment $subject
     */
    private function pendingStatus(Payment $subject)
    {
        $order = $subject->getOrder();
        $order->setState('new')
            ->setStatus('pending');
    }

    /**
     * @param Payment $subject
     */
    private function completeStatus(Payment $subject)
    {
        $order = $subject->getOrder();
        $order->setState('new')
            ->setStatus($this->helperData->getStatusToOrderComplete())
            ->addCommentToStatusHistory(__('The payment was confirmed and the order is beeing processed'))
            ;
    }
}
