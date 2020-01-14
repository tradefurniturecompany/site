<?php

namespace Imega\FinanceModule\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\ObjectManagerInterface;

class CreateOrderInvoice implements ObserverInterface
{
    protected $invoiceService;
    protected $transactionFactory;
    protected $orderRepository;

    public function __construct(

        OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\TransactionFactory $transactionFactory

    ) {
        $this->orderRepository = $orderRepository;
        $this->invoiceService = $invoiceService;
        $this->transactionFactory = $transactionFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $newStatus = $order->getData('status');
        $paymentCode = $order->getPayment()->getMethodInstance()->getCode();

        if ($paymentCode == 'financegateway' && $newStatus == 'processing') {

            $order = $this->orderRepository->get($order->getId());

            if (!$order->hasInvoices()) {
              $invoice = $this->invoiceService->prepareInvoice($order);
              $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_OFFLINE);
              $invoice->setState(\Magento\Sales\Model\Order\Invoice::STATE_OPEN);
              $invoice->register();

              $transaction = $this->transactionFactory->create()
                ->addObject($invoice)
                ->addObject($invoice->getOrder());

              $transaction->save();
            }
        }
    }

}
