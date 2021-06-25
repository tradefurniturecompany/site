<?php
namespace Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Order\Export\Order;

class CustomFields extends \Hotlink\Brightpearl\Model\Platform\Data
{

    /**
     * @var \Magento\GiftMessage\Helper\Message
     */
    protected $giftMessageMessageHelper;

    function __construct(
        \Magento\Framework\Simplexml\ElementFactory $xmlFactory,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Hotlink\Framework\Model\ReportFactory $reportFactory,
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Convention\Data\Helper $conventionDataHelperHelper,
        \Hotlink\Framework\Model\Config\Map $configMap,
        \Hotlink\Framework\Helper\Convention\Data $conventionDataHelper,
        \Hotlink\Framework\Model\Api\DataFactory $dataFactory,

        \Magento\GiftMessage\Helper\Message $giftMessageMessageHelper,
        array $data = []
    )
    {
        $this->giftMessageMessageHelper = $giftMessageMessageHelper;

        parent::__construct(
            $xmlFactory,
            $factoryHelper,
            $reportFactory,
            $exceptionHelper,
            $conventionDataHelperHelper,
            $configMap,
            $conventionDataHelper,
            $dataFactory,
            $data );
    }

    protected function _map_object_magento( \Magento\Sales\Model\Order $order )
    {
        $helper = $this->getHelper();
        $field = $helper->getOrderGiftMessageField();

        if ( is_string( $field ) )
            {
                $ret = '';
                if ( $order->getGiftMessageId() &&
                     ( $msg = $this->giftMessageMessageHelper->getGiftMessageForEntity( $order ) ) &&
                     ( $txt = trim( $msg->getMessage() ) )!=='' )
                    {
                        $ret = $txt;
                    }

                foreach ( $order->getAllVisibleItems() as $item )
                    {
                        if ( $item->getGiftMessageId() &&
                             ( $msg = $this->giftMessageMessageHelper->getGiftMessageForEntity( $item ) ) &&
                             ( $txt = trim( $msg->getMessage() ) )!=='' )
                            {
                                $sku = $helper->extractOrderItemOriginalSku( $item );

                                // adjust sku and name if modified by custom options (Hotlink_Interaction stores original values)
                                $opts = $helper->extractOrderItemProductOptions( $item );
                                if ( is_array( $opts ) )
                                    {
                                        if ( array_key_exists( 'hotlink_original_sku', $opts ) )
                                            {
                                                $sku = $opts['hotlink_original_sku'];
                                            }
                                    }

                                $ret .= "\n" . $sku . ': ' . $txt;
                            }
                    }

                if ( $ret !== '' )
                    {
                        $this[$field] = $ret;
                    }
            }
    }

}
