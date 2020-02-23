<?php
namespace Hotlink\Brightpearl\Model\Config\Source\Magento\Shipping\Method;

class Allowed extends \Magento\Shipping\Model\Config\Source\Allmethods
{

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $shippingConfig;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    protected $shippingHelper;

    function __construct(
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Psr\Log\LoggerInterface $logger,
        \Hotlink\Brightpearl\Helper\Shipping $shippingHelper
    )
    {
        $this->shippingConfig = $shippingConfig;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
        $this->shippingHelper = $shippingHelper;
    }

    /**
     * @internal this function overwrites the parent function.
     * reason: in some cases $carrierModel->getAllowedMethods(); throws an exception
     * and breaks our config screen.
     */
    function toOptionArray($isActiveOnlyFlag=false)
    {
        $methods = array( array( 'value' => '', 'label' => ' ' ) );
        try
            {
                $carriers = $this->shippingConfig->getAllCarriers();
                foreach ( $carriers as $carrierCode => $carrierModel )
                    {
                        if ( !$carrierModel->isActive() && (bool)$isActiveOnlyFlag===true )
                            {
                                continue;
                            }
                        $carrierMethods = $carrierModel->getAllowedMethods();
                        if ( !$carrierMethods )
                            {
                                continue;
                            }
                        $carrierTitle = $this->scopeConfig->getValue( 'carriers/' . $carrierCode . '/title' , \Magento\Store\Model\ScopeInterface::SCOPE_STORE );
                        $methods[$carrierCode] = array( 'label' => $carrierTitle,
                                                        'value' => array() );
                        foreach ( $carrierMethods as $methodCode => $methodTitle )
                            {
                                $encoded = $this->shippingHelper->encode( $carrierCode, $methodCode );
                                $methods[$carrierCode]['value'][] = array( 'value' => $encoded,
                                                                           'label' => '[' . $carrierCode . '] ' . $methodTitle );
                            }
                    }
            }
        catch ( \Exception $e )
            {
                $this->logger->critical( $e );
            }
        return $methods;
    }
}