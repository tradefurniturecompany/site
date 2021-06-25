<?php
namespace Hotlink\Brightpearl\Model\Interaction\Order\Environment;

abstract class AbstractEnvironment extends \Hotlink\Brightpearl\Model\Interaction\Environment\AbstractEnvironment
{

    // ==========================================
    // ========= ORDER HELPER FUNCTIONS =========
    // ==========================================

    protected $_customer = null;
    protected $_subscriber = null;

    protected $taxConfig;
    protected $scopeConfig;
    protected $paymentHelper;
    protected $newsletterSubscriberFactory;
    protected $customerCustomerFactory;
    protected $priceRendererFactory;
    protected $_priceRenderer = null;
    protected $warehouseCollectionFactory;
    protected $_warehouseId = null;
    protected $shippingHelper;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Hotlink\Framework\Helper\Html\Form\Environment $htmlFormEnvironmentHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Framework\Model\Interaction\AbstractInteraction $interaction,
        \Hotlink\Brightpearl\Model\Config\Authorisation $brightpearlConfigAuthorisation,
        \Hotlink\Brightpearl\Model\Config\OAuth2 $brightpearlConfigOAuth2,

        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Payment\Helper\Data $paymentHelper,
        \Magento\Newsletter\Model\SubscriberFactory $newsletterSubscriberFactory,
        \Magento\Customer\Model\CustomerFactory $customerCustomerFactory,

        // \Magento\Tax\Block\Item\Price\RendererFactory $priceRendererFactory,
        \Magento\Weee\Block\Item\Price\RendererFactory $priceRendererFactory,
        \Hotlink\Brightpearl\Helper\Shipping $shippingHelper,
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Warehouse\CollectionFactory $warehouseCollectionFactory,
        $storeId
    )
    {
        parent::__construct(
            $exceptionHelper,
            $reflectionHelper,
            $reportHelper,
            $factoryHelper,
            $htmlFormEnvironmentHelper,
            $storeManager,
            $interaction,
            $brightpearlConfigAuthorisation,
            $brightpearlConfigOAuth2,
            $storeId
        );
        $this->taxConfig = $taxConfig;
        $this->scopeConfig = $scopeConfig;
        $this->paymentHelper = $paymentHelper;
        $this->newsletterSubscriberFactory = $newsletterSubscriberFactory;
        $this->customerCustomerFactory = $customerCustomerFactory;
        $this->priceRendererFactory = $priceRendererFactory;
        $this->shippingHelper = $shippingHelper;
        $this->warehouseCollectionFactory = $warehouseCollectionFactory;
    }

    public function getPriceRenderer()
    {
        if ( is_null( $this->_priceRenderer ) )
            {
                $this->_priceRenderer = $this->priceRendererFactory->create();
            }
        return $this->_priceRenderer;
    }

    public function getUpdateExistingCustomers()
    {
        return $this->getConfig()->getUpdateExistingCustomers( $this->getStoreId() );
    }

    public function getIncludeMarketing()
    {
        return $this->getConfig()->getIncludeMarketing($this->getStoreId());
    }

    public function isCustomerSubscribedToNewsletter(\Magento\Sales\Model\Order $order)
    {
        $subscribed = false;

        if ($subscriber = $this->getSubscriber($order)) {
            $subscribed = ($subscriber->getSubscriberStatus() == \Magento\Newsletter\Model\Subscriber::STATUS_SUBSCRIBED);
        }

        return $subscribed;
    }

    public function getOrderGiftMessageField()
    {
        $giftMessage = $this->getConfig()->getGiftMessageField($this->getStoreId());
        return $giftMessage ? $giftMessage : null;
    }

    public function getOrderChannelId()
    {
        $channelId = $this->getConfig()->getChannel( $this->getStoreId() );
        return $channelId ? (int) $channelId : null;
    }

    public function getDefaultAllocationWarehouse()
    {
        if ( is_null( $this->_warehouseId ) )
            {
                $this->_warehouseId = false;
                if ( $warehouseId = $this->getConfig()->getDefaultAllocationWarehouse( $this->getStoreId() ) )
                    {
                        $warehouseCollection = $this->warehouseCollectionFactory->create();
                        $warehouse =
                                   $warehouseCollection
                                   ->addFieldToFilter( 'id', $warehouseId )
                                   ->load()
                                   ->getFirstItem();
                        $this->_warehouseId = $warehouse->getBrightpearlId();
                    }
            }
        return $this->_warehouseId;
    }

    public function isDiscountAppliedOnPricesIncludingTax(\Magento\Sales\Model\Order $order)
    {
        return $this->taxConfig->discountTax($this->getStoreId());
    }

    public function isPriceEnteredIncludingTax(\Magento\Sales\Model\Order $order)
    {
        return $this->scopeConfig->getValue(\Magento\Tax\Model\Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $this->getStoreId());
    }

    public function useOrderCurrency(\Magento\Sales\Model\Order $order)
    {
        $currency = $this->getConfig()->getUseCurrency($this->getStoreId());
        return ($currency === \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Currency::ORDER);
    }

    public function getOrderGiftCardsAmount(\Magento\Sales\Model\Order $order)
    {
        $giftCardsAmount = $this->useOrderCurrency($order)
            ? $order->getGiftCardsAmount()
            : $order->getBaseGiftCardsAmount();

        return $giftCardsAmount ? (double) $giftCardsAmount : null;
    }

    protected function getOrderCustomerField(\Magento\Sales\Model\Order $order, $field)
    {
        $fieldValue = $order->getDataUsingMethod('customer_'.$field);

        if (!$fieldValue && $order->getCustomerIsGuest()) {
            if ($billAddr = $order->getBillingAddress() and $billAddr->getDataUsingMethod($field)) {
                $fieldValue = $billAddr->getDataUsingMethod($field);
            }
            else if ($shipAddr = $order->getShippingAddress() and $shipAddr->getData($field)) {
                $fieldValue = $shipAddr->getDataUsingMethod($field);
            }
        }
        if (!$fieldValue and $customer = $this->getCustomer($order)) {
            $fieldValue = $customer->getDataUsingMethod($field);
        }

        return $fieldValue;
    }

    public function getOrderCustomerEmail(\Magento\Sales\Model\Order $order)
    {
        return $this->getOrderCustomerField($order, 'email');
    }

    public function getOrderCustomerSalutation(\Magento\Sales\Model\Order $order)
    {
        return $this->getOrderCustomerField($order, 'prefix');
    }

    public function getOrderCustomerFirstName( \Magento\Sales\Model\Order $order )
    {
        return $this->getOrderCustomerField($order, 'firstname');

    }
    public function getOrderCustomerLastName(\Magento\Sales\Model\Order $order)
    {
        return $this->getOrderCustomerField($order, 'lastname');
    }

    public function getOrderCustomerCompany( \Magento\Sales\Model\Order $order )
    {
        $company = '';

        $map = $this->getConfig()->getCompanyFromMap( $this->getStoreId() );
        if (is_array($map)) {
            foreach ($map as $row) {
                $type = isset($row['type']) ? $row['type'] : null;
                $code = isset($row['code']) ? $row['code'] : null;

                switch ($type) {

                case \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Customer\Company::CUSTOMER:
                    if ($customer = $this->getCustomer($order)) {
                        $company = $customer->getDataUsingMethod($code);
                        break 2;
                    }
                    break;
                case \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Customer\Company::BILLING:
                    if ($addr = $order->getBillingAddress()) {
                        $company = $addr->getDataUsingMethod($code);
                        break 2;
                    }
                    break;
                case \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Customer\Company::SHIPPING:
                    if ($addr = $order->getShippingAddress()) {
                        $company = $addr->getDataUsingMethod($code);
                        break 2;
                    }
                    break;
                }
            }
        }
        return $company;
    }

    public function getOrderExternalReference(\Magento\Sales\Model\Order $order)
    {
        return $order->getIncrementId();
    }

    public function getOrderStatus( \Magento\Sales\Model\Order $order )
    {
        $storeId     = $this->getStoreId();
        $config      = $this->getConfig();
        $orderStatus = $order->getStatus();

        $status = null;
        $map = $config->getOrderStatusMap( $storeId );

        if (is_array($map)) {
            foreach ($map as $row) {
                $mapMageStatus = isset($row['magento'])     ? $row['magento']     : null;
                $mapBPStatus   = isset($row['brightpearl']) ? $row['brightpearl'] : null;

                if ($mapMageStatus == $orderStatus) {
                    $status = $mapBPStatus;
                    break;
                }
            }
        }
        if ($status === null) {
            $status = $config->getOrderStatusDefault($storeId);
        }

        return $status ? (int) $status : null;
    }

    public function getOrderCreatedAt(\Magento\Sales\Model\Order $order)
    {
        $time = null;
        if ($createdAt = strtotime($order->getCreatedAt())) {
            $time = date(DATE_W3C, $createdAt); // DATE_W3C = "Y-m-d\TH:i:sP"; (example: 2005-08-15T15:52:01+00:00)
        }
        return $time;
    }

    public function getOrderCurrencyCode(\Magento\Sales\Model\Order $order)
    {
        $use = $this->getConfig()->getUseCurrency($this->getStoreId());

        if ($use === \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Currency::ORDER) {
            $currencyCode = $order->getOrderCurrencyCode();
        }
        else {
            $currencyCode = $order->getBaseCurrencyCode();
        }
        return $currencyCode ? $currencyCode : null;
    }

    public function hasShippingMethod(\Magento\Sales\Model\Order $order)
    {
        return $order->hasShippingMethod();
    }

    public function getOrderShippingMethodId( \Magento\Sales\Model\Order $order )
    {
        $storeId = $this->getStoreId();
        $config  = $this->getConfig();
        $method  = $order->getShippingMethod();
        $brightpearlId = null;

        $map = $config->getShippingMethodMapIsomorphic( $storeId );
        if ( is_array( $map ) )
            {
                foreach ( $map as $row )
                    {
                        $magento     = isset( $row[ 'magento'     ] ) ? $row[ 'magento'     ] : null;
                        $brightpearl = isset( $row[ 'brightpearl' ] ) ? $row[ 'brightpearl' ] : null;
                        if ( $this->shippingHelper->toMagento( $magento ) === $method )
                            {
                                $brightpearlId = $brightpearl;
                                break;
                            }
                    }
            }
        if ( is_null( $brightpearlId ) )
            {
                $map = $config->getShippingMethodMapNonisomorphic( $storeId );
                if ( is_array( $map ) )
                    {
                        foreach ( $map as $row )
                            {
                                $magento     = isset( $row[ 'magento'     ] ) ? $row[ 'magento'     ] : null;
                                $brightpearl = isset( $row[ 'brightpearl' ] ) ? $row[ 'brightpearl' ] : null;
                                if ( $this->shippingHelper->toMagento( $magento ) === $method )
                                    {
                                        $brightpearlId = $brightpearl;
                                        break;
                                    }
                                if ( strpos($magento, '/' ) === 0 && @preg_match( $magento, $method ) )
                                    {
                                        $brightpearlId = $brightpearl;
                                        break;
                                    }
                            }
                    }
            }
        if ( is_null( $brightpearlId ) )
            {
                $brightpearlId = $config->getShippingMethodDefault( $storeId );
            }
        return $brightpearlId ? (int) $brightpearlId : null;
    }

    protected function getOrderAmountByCurrencyUsage(\Magento\Sales\Model\Order $order, $orderfield, $baseField)
    {
        $use = $this->getConfig()->getUseCurrency($this->getStoreId());

        if ($use === \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Currency::ORDER) {
            $amount = $order->getDataUsingMethod($orderfield);
        }
        else {
            $amount = $order->getDataUsingMethod($baseField);
        }

        return (double) $amount;
    }

    public function getOrderShippingAmountExcludingTax(\Magento\Sales\Model\Order $order)
    {
        return $this->getOrderAmountByCurrencyUsage($order, 'shipping_amount', 'base_shipping_amount');
    }

    public function getOrderShippingAmountIncludingTax(\Magento\Sales\Model\Order $order)
    {
        return $this->getOrderAmountByCurrencyUsage($order, 'shipping_incl_tax', 'base_shipping_incl_tax');
    }

    public function getOrderShippingTax(\Magento\Sales\Model\Order $order)
    {
        return $this->getOrderShippingAmountIncludingTax($order) - $this->getOrderShippingAmountExcludingTax($order);
    }

    /**
     * Note: it is uncertain if this value includes taxes or not, this may depend on
     * the Magento tax calculation settings at the time the order was placed.
     */
    public function getOrderShippingDiscountAmount(\Magento\Sales\Model\Order $order)
    {
        return $this->getOrderAmountByCurrencyUsage($order, 'shipping_discount_amount', 'base_shipping_discount_amount');
    }

    /**
       DS@20151130
       Note: This is as per specification. I would suggest to calculate correct amounts based on Magento Tax Calculation settings
       at the time the order is being sent to BP (there's no historical reference to clear value in Magento, so changing those
       settings can easily lead to errors).
    */
    public function getOrderDiscountTaxAmount( \Magento\Sales\Model\Order $order )
    {
        return (double) 0.0;
    }

    /**
       DS@20160314
       Note: Ticket #17284: Order row prices should exclude any discount
       SPECS: order.total.amountExcludingTax =
                  sum[ order.row.price.amountExcludingTax * order.row.quantity ]
                  + order.shipping.price.amountExcludingTax
                  - order.gift_cards_amount
                  - order.shipping.discount.amount
                  - order.discount.amount
       DS@20160321
       Note: Ticket #17284: Based on Discount_tax_analysis.docx document attached
           by Deepak, totals are supposed to be provided as shown in Magento.
           Reverted to fetching totals from Magento order records.
    */
    public function getOrderTotalExclTax( \Magento\Sales\Model\Order $order )
    {
        $incl = $this->getOrderTotalInclTax($order);
        $tax  = $this->getOrderTotalTax($order);
        return (is_null($incl) || is_null($tax))
            ? null
            : ($incl - $tax);
    }

    /**
       DS@20160314
       Note: Ticket #17284: Order row prices should exclude any discount
       SPECS: order.total.amountIncludingTax =
                  sum[ order.row.price.amountIncludingTax * order.row.quantity ]
                      + order.shipping.price.amountIncludingTax
                      - order.gift_cards_amount
                      - order.shipping.discount.amount
                      - order.discount.amount
       DS@20160321
       Note: Ticket #17284: Based on Discount_tax_analysis.docx document attached
           by Deepak, totals are supposed to be provided as shown in Magento.
           Reverted to fetching totals from Magento order records.
    */
    public function getOrderTotalInclTax( \Magento\Sales\Model\Order $order )
    {
        return $this->getOrderAmountByCurrencyUsage($order, 'grand_total', 'base_grand_total');
    }

    /**
       DS@20160314
       Note: Ticket #17284: Order row prices should exclude any discount
       SPECS: order.total.tax =
                  sum[ order.row.price.tax * order.row.quantity ]
                  + order.shipping.price.tax
       DS@20160321
       Note: Ticket #17284: Based on Discount_tax_analysis.docx document attached
           by Deepak, totals are supposed to be provided as shown in Magento.
           Reverted to fetching totals from Magento order records.
     */
    public function getOrderTotalTax( \Magento\Sales\Model\Order $order )
    {
        return $this->getOrderAmountByCurrencyUsage($order, 'tax_amount', 'base_tax_amount');
    }

    public function getOrderTotalAmountPaid( \Magento\Sales\Model\Order $order )
    {
        return $this->getOrderAmountByCurrencyUsage($order, 'total_paid', 'base_total_paid');
    }

    public function getOrderGrandTotal( \Magento\Sales\Model\Order $order )
    {
        return $this->getOrderAmountByCurrencyUsage($order, 'grand_total', 'base_grand_total');
    }

    /**
     * DS@20160321
     * Changed against the specs, as this new method takes in account EE GiftCards without too many calculations
     * made necessary.
     */
    public function getOrderFullyPaid( \Magento\Sales\Model\Order $order )
    {
        $total = $this->getOrderGrandTotal($order) * 10000.0;
        $paid  = $this->getOrderTotalAmountPaid($order) * 10000.0;
        return $paid >= $total;
    }

    // ==================================================
    // ========= ORDER PAYMENT HELPER FUNCTIONS =========
    // ==================================================

    public function getPaymentCreateSalesReceipts(\Magento\Sales\Model\Order\Payment $payment)
    {
        $storeId = $this->getStoreId();
        $config  = $this->getConfig();
        $method  = $payment->getMethod();
        $map     = $config->getPaymentMethodMap( $storeId );

        $create = null;

        if (is_array($map)) {
            foreach ($map as $row) {
                $magento = isset($row['magento']) ? $row['magento'] : null;
                $receipt = isset($row['receipt']) ? $row['receipt'] : null;

                if ($magento === $method) {
                    $create = $receipt;
                    break;
                }
            }
        }
        if ($create === null ) {
            $create = $config->getPaymentMethodDefaultCreateReceipts($storeId);
        }

        return $create ? $create : null;
    }

    protected function getPaymentAmountByCurrencyUsage(\Magento\Sales\Model\Order\Payment $payment, $paymentfield, $baseField)
    {
        $use = $this->getConfig()->getUseCurrency($this->getStoreId());

        if ($use === \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Currency::ORDER) {
            $amount = $payment->getDataUsingMethod($paymentfield);
        }
        else {
            $amount = $payment->getDataUsingMethod($baseField);
        }

        return (double) $amount;
    }

    public function getPaymentAmountPaid(\Magento\Sales\Model\Order\Payment $payment)
    {
        return $this->getPaymentAmountByCurrencyUsage($payment, 'amount_paid', 'base_amount_paid');
    }

    public function getPaymentDescription( \Magento\Sales\Model\Order\Payment $payment )
    {
        $method = $payment->getMethod();

        $description = '';

        if ($instance = $this->paymentHelper->getMethodInstance($method)) {
            $description = $instance->getTitle();
        }
        else {
            $description = $method;
        }

        if ($tid = $payment->getLastTransId()) {
            $description .= ': ' . $tid;
        }

        return $description;
    }

    public function getPaymentNominalCode( \Magento\Sales\Model\Order\Payment $payment )
    {
        $storeId = $this->getStoreId();
        $method  = (string)$payment->getMethod();
        $config  = $this->getConfig();
        $map     = $config->getPaymentMethodMap($storeId);

        $nominalCode = null;

        if (is_array($map)) {
            foreach ( $map as $row ) {
                $magento     = isset($row['magento'])     ? $row['magento']     : null;
                $brightpearl = isset($row['brightpearl']) ? $row['brightpearl'] : null;

                if ($magento === $method) {
                    $nominalCode = $brightpearl;
                    break;
                }
            }
        }
        if ($nominalCode === null) {
            $nominalCode = $config->getPaymentMethodDefault($storeId);
        }

        return $nominalCode ? $nominalCode : null;
    }

    // ===============================================
    // ========= ORDER ITEM HELPER FUNCTIONS =========
    // ===============================================

    protected function getItemAmountByCurrencyUsage(\Magento\Sales\Model\Order\Item $item, $itemfield, $baseField)
    {
        $use = $this->getConfig()->getUseCurrency($this->getStoreId());

        if ($use === \Hotlink\Brightpearl\Model\Config\Source\Brightpearl\Order\Currency::ORDER) {
            $amount = $item->getDataUsingMethod($itemfield);
        }
        else {
            $amount = $item->getDataUsingMethod($baseField);
        }

        return (double) $amount;
    }

    public function getOrderItemAmountExclTax( \Magento\Sales\Model\Order\Item $item )
    {
        return $this->getItemAmountByCurrencyUsage($item, 'price', 'base_price');
    }

    public function getOrderItemAmountInclTax( \Magento\Sales\Model\Order\Item $item )
    {
        return $this->getItemAmountByCurrencyUsage($item, 'price_incl_tax', 'base_price_incl_tax');
    }

    public function getOrderItemTaxAmount( \Magento\Sales\Model\Order\Item $item )
    {
        $inc = $this->getOrderItemAmountInclTax($item);
        $exc = $this->getOrderItemAmountExclTax($item);

        return $inc - $exc;
    }

    public function getOrderItemRowTotalExclTax( \Magento\Sales\Model\Order\Item $item )
    {
        $inclTax = $this->getOrderItemRowTotalInclTax( $item );
        $tax = $this->getOrderItemRowTotalTax( $item );
        $exclTax = $inclTax - $tax;
        return $exclTax;
    }

    public function getOrderItemRowTotalInclTax( \Magento\Sales\Model\Order\Item $item )
    {
        return $this->getItemAmountByCurrencyUsage($item, 'row_total_incl_tax', 'base_row_total_incl_tax');
    }

    public function getOrderItemRowTotalTax( \Magento\Sales\Model\Order\Item $item )
    {
        return $this->getItemAmountByCurrencyUsage($item, 'tax_amount', 'base_tax_amount');
    }

    public function extractOrderItemOriginalSku( \Magento\Sales\Model\Order\Item $item )
    {
        $sku = $item->getSku();

        $opts = $item->getProductOptions();
        if (is_array($opts) && isset($opts['hotlink_original_sku'])) {
            $sku = $opts['hotlink_original_sku'];
        }

        return $sku;
    }

    public function extractOrderItemProductOptions( \Magento\Sales\Model\Order\Item $item )
    {
        $productOptions = array();
        $opts = $item->getProductOptions();

        if (is_array($opts)) {
            if (isset($opts['options'])) {
                foreach ($opts['options'] as &$opt) {
                    $label = isset($opt['label']) ? $opt['label'] : null;
                    $value = isset($opt['value']) ? $opt['value'] : null;
                    if ($label) {
                        if (!isset($productOptions[$label])) {
                            $productOptions[$label] = $value;
                        }
                        else if ($value) {
                            $productOptions[$label] .= '; ' . $value;
                        }
                    }
                }
            }
        }
        return $productOptions;
    }

    protected function getSubscriber(\Magento\Sales\Model\Order $order)
    {
        if (is_null($this->_subscriber)) {

            if ($email = $this->getOrderCustomerEmail($order)) {
                $subscriber = $this->newsletterSubscriberFactory->create()->loadByEmail($email);

                $this->_subscriber = $subscriber->getId()
                    ? $subscriber
                    : false;
            }
        }

        return $this->_subscriber;
    }


    protected function getCustomer(\Magento\Sales\Model\Order $order)
    {
        if (is_null($this->_customer)) {

            if ($customer = $order->getCustomer()) {
                $this->_customer = $customer;
            }
            else if ($order->hasCustomerId()) {
                $customer = $this->customerCustomerFactory->create()
                    ->setStoreId($this->getStoreId())
                    ->load($order->getCustomerId());

                if ($customer->getId()) {
                    $order->setCustomer($customer);
                }
                else {
                    $customer = false; // false means failed to load
                }

                $this->_customer = $customer;
            }
        }

        return $this->_customer;
    }
}
