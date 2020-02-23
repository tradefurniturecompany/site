<?php
namespace Hotlink\Brightpearl\Model\Interaction\Creditmemo\Export;

class Environment extends \Hotlink\Brightpearl\Model\Interaction\Environment\AbstractEnvironment
{

    protected $sharedOrderConfig;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Hotlink\Framework\Helper\Html\Form\Environment $htmlFormEnvironmentHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,
        \Hotlink\Brightpearl\Model\Config\Authorisation $brightpearlConfigAuthorisation,
        \Hotlink\Brightpearl\Model\Config\OAuth2 $brightpearlConfigOAuth2,

        \Hotlink\Brightpearl\Model\Config\Shared\Order $sharedOrderConfig,

        $storeId
    )
    {
        parent::__construct( $exceptionHelper,
                             $reflectionHelper,
                             $reportHelper,
                             $factoryHelper,
                             $htmlFormEnvironmentHelper,
                             $storeManager,
                             $interaction,
                             $brightpearlConfigAuthorisation,
                             $brightpearlConfigOAuth2,
                             $storeId );
        $this->sharedOrderConfig = $sharedOrderConfig;
    }

    protected function _getParameterModels()
    {
        return [ '\Hotlink\Framework\Model\Interaction\Environment\Parameter\Stream\Reader',
                 '\Hotlink\Brightpearl\Model\Interaction\Creditmemo\Export\Environment\Filter',
                 '\Hotlink\Brightpearl\Model\Interaction\Creditmemo\Export\Environment\ForceSalesCredit',
                 '\Hotlink\Brightpearl\Model\Interaction\Creditmemo\Export\Environment\ForceRefund',
                 '\Hotlink\Brightpearl\Model\Interaction\Creditmemo\Export\Environment\ForceQuarantine'
        ];
    }

    function getCreditmemoChannelId()
    {
        $channelId = $this->getConfig()->getChannel( $this->getStoreId() );
        return $channelId ? ( int ) $channelId : null;
    }

    function formatDate( $date )
    {
        $result = null;
        if ( $milliseconds = strtotime( $date ) )
            {
                $result = date( DATE_W3C, $milliseconds ); // DATE_W3C = "Y-m-d\TH:i:sP"; (example: 2005-08-15T15:52:01+00:00)
            }
        return $result;
    }

    function getSalesCreditOrderStatus()
    {
        return $this->getConfig()->getSalesCreditOrderStatus( $this->getStoreId() );
    }

    function getQuarantineEnabled()
    {
        return $this->getConfig()->getQuarantineEnabled( $this->getStoreId() );
    }

    function getQuarantineWarehouse()
    {
        return $this->getConfig()->getQuarantineWarehouse( $this->getStoreId() );
    }

    function getQuarantineWarehouseLocation()
    {
        return $this->getConfig()->getQuarantineWarehouseLocation( $this->getStoreId() );
    }

    function getQuarantinePricelist()
    {
        return $this->getConfig()->getQuarantinePricelist( $this->getStoreId() );
    }

    function getUseCurrency()
    {
        return $this->sharedOrderConfig->getUseCurrency( $this->getStoreId() );
    }

    function getCurrencyCode( \Magento\Sales\Model\Order\Creditmemo $creditmemo )
    {
        $use = $this->sharedOrderConfig->getUseCurrency( $this->getStoreId() );
        switch ( $use )
            {
                case \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Currency::ORDER:
                    return $creditmemo->getOrderCurrencyCode();
                    break;
                case \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Currency::BASE:
                    return $creditmemo->getBaseCurrencyCode();
                    break;
            }
        return null;
    }

    function getGrandTotal( \Magento\Sales\Model\Order\Creditmemo $creditmemo )
    {
        $use = $this->sharedOrderConfig->getUseCurrency( $this->getStoreId() );
        switch ( $use )
            {
                case \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Currency::ORDER:
                    return $creditmemo->getGrandTotal();
                    break;
                case \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Currency::BASE:
                    return $creditmemo->getBaseGrandTotal();
                    break;
            }
        return false;
    }

    function getPaymentNominalCode( \Magento\Sales\Model\Order\Payment $payment )
    {
        $storeId = $this->getStoreId();
        $method  = ( string ) $payment->getMethod();
        $config  = $this->sharedOrderConfig;
        $map     = $config->getPaymentMethodMap( $storeId );

        $nominalCode = null;

        if ( is_array( $map ) )
            {
                foreach ( $map as $row )
                    {
                        $magento     = isset($row['magento'])     ? $row['magento']     : null;
                        $brightpearl = isset($row['brightpearl']) ? $row['brightpearl'] : null;

                        if ( $magento === $method )
                            {
                                $nominalCode = $brightpearl;
                                break;
                            }
                    }
            }
        if ( $nominalCode === null )
            {
                $nominalCode = $config->getPaymentMethodDefault( $storeId );
            }

        return $nominalCode ? $nominalCode : null;
    }

    function getAmountByOrderCurrencyUsage( \Magento\Sales\Model\Order\Creditmemo $creditmemo, $field, $baseField )
    {
        $use = $this->getConfig()->getUseCurrency( $this->getStoreId() );
        if ( $use === \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Currency::ORDER )
            {
                $amount = $creditmemo->getDataUsingMethod( $field );
            }
        else
            {
                $amount = $creditmemo->getDataUsingMethod( $baseField );
            }
        return ( double ) $amount;
    }

}
