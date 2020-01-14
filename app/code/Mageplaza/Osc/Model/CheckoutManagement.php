<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Osc\Model;

use Exception;
use Magento\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Checkout\Api\ShippingInformationManagementInterface;
use Magento\Checkout\Model\Session;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Framework\UrlInterface;
use Magento\GiftMessage\Model\GiftMessageManager;
use Magento\GiftMessage\Model\Message;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Api\CartTotalRepositoryInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\EstimateAddressInterface;
use Magento\Quote\Api\PaymentMethodManagementInterface;
use Magento\Quote\Api\ShippingMethodManagementInterface;
use Magento\Quote\Model\Cart\ShippingMethodConverter;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\TotalsCollector;
use Mageplaza\Osc\Api\CheckoutManagementInterface;
use Mageplaza\Osc\Api\Data\OscDetailsInterface;
use Mageplaza\Osc\Helper\Data as OscHelper;

/**
 * Class CheckoutManagement
 * @package Mageplaza\Osc\Model
 */
class CheckoutManagement implements CheckoutManagementInterface
{
    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @type OscDetailFactory
     */
    protected $oscDetailsFactory;

    /**
     * @var ShippingMethodManagementInterface
     */
    protected $shippingMethodManagement;

    /**
     * @var PaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    /**
     * @var CartTotalRepositoryInterface
     */
    protected $cartTotalsRepository;

    /**
     * Url Builder
     *
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Checkout session
     *
     * @type Session
     */
    protected $checkoutSession;

    /**
     * @var ShippingInformationManagementInterface
     */
    protected $shippingInformationManagement;

    /**
     * @var OscHelper
     */
    protected $oscHelper;

    /**
     * @var Message
     */
    protected $giftMessage;

    /**
     * @var GiftMessageManager
     */
    protected $giftMessageManagement;

    /**
     * @var CustomerSession
     */
    protected $_customerSession;

    /**
     * @var TotalsCollector
     */
    protected $_totalsCollector;

    /**
     * @var AddressInterface
     */
    protected $_addressInterface;

    /**
     * @var ShippingMethodConverter
     */
    protected $_shippingMethodConverter;

    /**
     * Customer Address repository
     *
     * @var AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var DataObjectProcessor $dataProcessor
     */
    private $dataProcessor;

    /**
     * CheckoutManagement constructor.
     *
     * @param CartRepositoryInterface $cartRepository
     * @param OscDetailsFactory $oscDetailsFactory
     * @param ShippingMethodManagementInterface $shippingMethodManagement
     * @param PaymentMethodManagementInterface $paymentMethodManagement
     * @param CartTotalRepositoryInterface $cartTotalsRepository
     * @param UrlInterface $urlBuilder
     * @param Session $checkoutSession
     * @param ShippingInformationManagementInterface $shippingInformationManagement
     * @param OscHelper $oscHelper
     * @param Message $giftMessage
     * @param GiftMessageManager $giftMessageManager
     * @param CustomerSession $customerSession
     * @param TotalsCollector $totalsCollector
     * @param AddressInterface $addressInterface
     * @param ShippingMethodConverter $shippingMethodConverter
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        OscDetailsFactory $oscDetailsFactory,
        ShippingMethodManagementInterface $shippingMethodManagement,
        PaymentMethodManagementInterface $paymentMethodManagement,
        CartTotalRepositoryInterface $cartTotalsRepository,
        UrlInterface $urlBuilder,
        Session $checkoutSession,
        ShippingInformationManagementInterface $shippingInformationManagement,
        OscHelper $oscHelper,
        Message $giftMessage,
        GiftMessageManager $giftMessageManager,
        customerSession $customerSession,
        TotalsCollector $totalsCollector,
        AddressInterface $addressInterface,
        ShippingMethodConverter $shippingMethodConverter,
        AddressRepositoryInterface $addressRepository
    ) {
        $this->cartRepository = $cartRepository;
        $this->oscDetailsFactory = $oscDetailsFactory;
        $this->shippingMethodManagement = $shippingMethodManagement;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->cartTotalsRepository = $cartTotalsRepository;
        $this->_urlBuilder = $urlBuilder;
        $this->checkoutSession = $checkoutSession;
        $this->shippingInformationManagement = $shippingInformationManagement;
        $this->oscHelper = $oscHelper;
        $this->giftMessage = $giftMessage;
        $this->giftMessageManagement = $giftMessageManager;
        $this->_customerSession = $customerSession;
        $this->_totalsCollector = $totalsCollector;
        $this->_addressInterface = $addressInterface;
        $this->_shippingMethodConverter = $shippingMethodConverter;
        $this->addressRepository = $addressRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function updateItemQty($cartId, $itemId, $itemQty)
    {
        if ($itemQty == 0) {
            return $this->removeItemById($cartId, $itemId);
        }

        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);
        $quoteItem = $quote->getItemById($itemId);
        if (!$quoteItem) {
            throw new NoSuchEntityException(
                __('Cart %1 doesn\'t contain item  %2', $cartId, $itemId)
            );
        }

        try {
            $quoteItem->setQty($itemQty)->save();
            $this->cartRepository->save($quote);
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Could not update item from quote'));
        }

        return $this->getResponseData($quote);
    }

    /**
     * {@inheritDoc}
     */
    public function removeItemById($cartId, $itemId)
    {
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);
        $quoteItem = $quote->getItemById($itemId);
        if (!$quoteItem) {
            throw new NoSuchEntityException(
                __('Cart %1 doesn\'t contain item  %2', $cartId, $itemId)
            );
        }
        try {
            $quote->removeItem($itemId);
            $this->cartRepository->save($quote);
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Could not remove item from quote'));
        }

        return $this->getResponseData($quote);
    }

    /**
     * {@inheritDoc}
     */
    public function getPaymentTotalInformation($cartId)
    {
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);

        return $this->getResponseData($quote);
    }

    /**
     * {@inheritDoc}
     */
    public function updateGiftWrap($cartId, $isUseGiftWrap)
    {
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);
        $quote->getShippingAddress()->setUsedGiftWrap($isUseGiftWrap);

        try {
            $this->cartRepository->save($quote);
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('Could not add gift wrap for this quote'));
        }

        return $this->getResponseData($quote);
    }

    /**
     * Response data to update osc block
     *
     * @param Quote $quote
     * @param ExtensibleDataInterface $address
     *
     * @return OscDetailsInterface
     * @throws NoSuchEntityException
     */
    public function getResponseData(Quote $quote, $address = null)
    {
        /** @var OscDetailsInterface $oscDetails */
        $oscDetails = $this->oscDetailsFactory->create();

        if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
            $oscDetails->setRedirectUrl($this->_urlBuilder->getUrl('checkout/cart'));
        } else {
            if ($quote->getShippingAddress()->getCountryId()) {
                $oscDetails->setShippingMethods($this->getShippingMethods($quote, $address));
            }
            $oscDetails->setPaymentMethods($this->paymentMethodManagement->getList($quote->getId()));
            $oscDetails->setTotals($this->cartTotalsRepository->get($quote->getId()));
        }

        return $oscDetails;
    }

    /**
     * {@inheritDoc}
     */
    public function saveCheckoutInformation(
        $cartId,
        ShippingInformationInterface $addressInformation,
        $customerAttributes = [],
        $additionInformation = []
    ) {
        try {
            $additionInformation['customerAttributes'] = $customerAttributes;
            $this->checkoutSession->setOscData($additionInformation);
            $this->addGiftMessage($cartId, $additionInformation);

            if ($addressInformation->getShippingAddress()) {
                if ($this->_customerSession->isLoggedIn() && isset($additionInformation['billing-same-shipping']) && !$additionInformation['billing-same-shipping']) {
                    $addressInformation->getShippingAddress()->setSaveInAddressBook(0);
                }
                $this->shippingInformationManagement->saveAddressInformation($cartId, $addressInformation);
            }
        } catch (Exception $e) {
            throw new InputException(__('Unable to save order information. Please check input data.'));
        }

        return true;
    }

    /**
     * @param Quote $quote
     * @param ExtensibleDataInterface $address
     *
     * @return array
     */
    public function getShippingMethods(Quote $quote, $address = null)
    {
        $result = [];
        $shippingAddress = $quote->getShippingAddress();
        $addressData = $address === null ? $this->_addressInterface->getData() : $this->extractAddressData($address);
        $shippingAddress->addData($addressData);
        $shippingAddress->setCollectShippingRates(true);
        $this->_totalsCollector->collectAddressTotals($quote, $shippingAddress);
        $shippingRates = $shippingAddress->getGroupedAllShippingRates();
        foreach ($shippingRates as $carrierRates) {
            foreach ($carrierRates as $rate) {
                $result[] = $this->_shippingMethodConverter->modelToDataObject($rate, $quote->getQuoteCurrencyCode());
            }
        }

        return $result;
    }

    /**
     * @param $cartId
     * @param $additionInformation
     *
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function addGiftMessage($cartId, $additionInformation)
    {
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);

        if (!$this->oscHelper->isDisabledGiftMessage() && isset($additionInformation['giftMessage'])) {
            $giftMessage = OscHelper::jsonDecode($additionInformation['giftMessage']);
            $this->giftMessage->setSender(isset($giftMessage['sender']) ? $giftMessage['sender'] : '');
            $this->giftMessage->setRecipient(isset($giftMessage['recipient']) ? $giftMessage['recipient'] : '');
            $this->giftMessage->setMessage(isset($giftMessage['message']) ? $giftMessage['message'] : '');
            $this->giftMessageManagement->setMessage($quote, 'quote', $this->giftMessage);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function estimateByAddressId($cartId, $addressId, $isAddressSameAsShipping)
    {
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);
        $address = null;
        if ($isAddressSameAsShipping) {
            $address = $this->addressRepository->getById($addressId);
            try {
                //Save the billing address country id to get the list of payment method restricted by country correctly
                $quote->getBillingAddress()->setCountryId(
                    $address->getCountryId()
                )->save();
            } catch (Exception $e) {
                throw new CouldNotSaveException(__('Could not estimate for this address'));
            }
        }

        return $this->getResponseData($quote, $address);
    }

    /**
     * {@inheritDoc}
     */
    public function estimateByExtendedAddress($cartId, $address, $isAddressSameAsShipping)
    {
        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);

        return $this->processEstimateByExtendAddress($quote, $address, $isAddressSameAsShipping);
    }

    /**
     * @param $quote
     * @param $address
     * @param $isAddressSameAsShipping
     *
     * @return OscDetailsInterface
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function processEstimateByExtendAddress($quote, $address, $isAddressSameAsShipping)
    {
        if ($isAddressSameAsShipping) {
            try {
                //Save the billing address country id to get the list of payment method restricted by country correctly
                $quote->getBillingAddress()->setCountryId(
                    $address->getCountryId()
                )->save();
            } catch (Exception $e) {
                throw new CouldNotSaveException(__('Could not estimate for this address'));
            }
        }

        return $this->getResponseData($quote, $address);
    }

    /**
     * Get transform address interface into Array
     *
     * @param ExtensibleDataInterface $address
     *
     * @return array
     */
    private function extractAddressData($address)
    {
        $className = \Magento\Customer\Api\Data\AddressInterface::class;
        if ($address instanceof AddressInterface) {
            $className = AddressInterface::class;
        } elseif ($address instanceof EstimateAddressInterface) {
            $className = EstimateAddressInterface::class;
        }

        return $this->getDataObjectProcessor()->buildOutputDataArray(
            $address,
            $className
        );
    }

    /**
     * Gets the data object processor
     *
     * @return DataObjectProcessor
     * @deprecated 101.0.0
     */
    private function getDataObjectProcessor()
    {
        if ($this->dataProcessor === null) {
            $this->dataProcessor = ObjectManager::getInstance()
                ->get(DataObjectProcessor::class);
        }

        return $this->dataProcessor;
    }
}
