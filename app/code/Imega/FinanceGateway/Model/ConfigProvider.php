<?php
/**
 * Copyright © 2019 Imegamedia. All rights reserved.
 */
namespace Imega\FinanceGateway\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\Escaper;
use Magento\Payment\Helper\Data as PaymentHelper;

class ConfigProvider implements ConfigProviderInterface
{
    const METHOD_CODE = 'financegateway';

    /**
     * @var \Magento\Payment\Model\Method\AbstractMethod
     */
    protected $methodInstance;

    /**
     * @var Escaper
     */
    protected $escaper;

    /**
     * @param PaymentHelper $paymentHelper
     * @param Escaper $escaper
     */
    public function __construct(
        PaymentHelper $paymentHelper,
        Escaper $escaper
    ) {
        $this->escaper = $escaper;
        $this->methodInstance = $paymentHelper->getMethodInstance(self::METHOD_CODE);

    }

    /**
     * {@inheritdoc}
     */
    public function getConfig()
    {
        $config = [];
        $config['payment']['instructions'][self::METHOD_CODE] = $this->getInstructions();

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
