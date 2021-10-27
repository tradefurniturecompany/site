<?php
/**
 * Copyright © 2019 Imegamedia. All rights reserved.
 */
namespace Imega\FinanceGateway\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;
use Imega\FinanceModule\Helper\Data as FinanceHelper;

class ConfigProvider implements ConfigProviderInterface
{
    const METHOD_CODE = 'financegateway';

    protected $methodInstance;

    protected $escaper;

    protected $financeHelper;

    public function __construct(
        PaymentHelper $paymentHelper,
        Escaper $escaper,
        FinanceHelper $financeHelper
    ) {
        $this->escaper = $escaper;
        $this->methodInstance = $paymentHelper->getMethodInstance(self::METHOD_CODE);
        $this->financeHelper = $financeHelper;
    }


    public function getConfig()
    {
        $config = [];
        $config['payment']['immFinanceGateway']['instructions'] = $this->getInstructions();
        $config['payment']['immFinanceGateway']['key'] = $this->financeHelper->getApiKey();
        $config['payment']['immFinanceGateway']['checkoutOnPayment'] = intval($this->financeHelper->checkoutOnPayment());
        $config['payment']['immFinanceGateway']['priceElement'] = $this->financeHelper->getPriceSelector();
        $config['payment']['immFinanceGateway']['priceElementInner'] = $this->financeHelper->getInnerPriceSelector();
        $config['payment']['immFinanceGateway']['element'] = $this->financeHelper->getPositionSelector();
        $config['payment']['immFinanceGateway']['insertion'] = $this->financeHelper->getPosition();
        return $config;
    }

    /**
     * Get instructions text from config
     *
     * @return string
     */
    protected function getInstructions()
    {
        return nl2br($this->methodInstance->getConfigData('instructions'));
    }
}
