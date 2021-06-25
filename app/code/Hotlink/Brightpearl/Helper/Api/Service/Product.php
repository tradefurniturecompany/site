<?php
namespace Hotlink\Brightpearl\Helper\Api\Service;

class Product extends \Hotlink\Brightpearl\Helper\Api\Service\AbstractService
{

    protected $transactionPriceListGetFactory;
    protected $platformDataBrightpearlPriceListFactory;
    protected $transactionChannelGetFactory;
    protected $transactionProductGetFactory;
    protected $brightpearlPlatformDataFactory;

    public function __construct(
        \Hotlink\Brightpearl\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Brightpearl\Model\Config\Api $brightpearlConfigApi,
        \Hotlink\Brightpearl\Model\Config\Authorisation $brightpearlConfigAuthorisation,
        \Hotlink\Brightpearl\Model\Config\OAuth2 $brightpearlConfigOAuth2,
        \Hotlink\Brightpearl\Model\Api\Service\TransportFactory $brightpearlApiServiceTransportFactory,

        \Hotlink\Brightpearl\Model\Api\Service\Product\Transaction\Price\ListPrice\GetFactory $transactionPriceListGetFactory,
        \Hotlink\Brightpearl\Model\Platform\Data\Brightpearl\Price\ListPriceFactory $platformDataBrightpearlPriceListFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Product\Transaction\Channel\GetFactory $transactionChannelGetFactory,
        \Hotlink\Brightpearl\Model\Api\Service\Product\Transaction\Product\GetFactory $transactionProductGetFactory,
        \Hotlink\Brightpearl\Model\Platform\DataFactory $brightpearlPlatformDataFactory
    )
    {
        $this->transactionPriceListGetFactory = $transactionPriceListGetFactory;
        $this->platformDataBrightpearlPriceListFactory = $platformDataBrightpearlPriceListFactory;
        $this->transactionChannelGetFactory = $transactionChannelGetFactory;
        $this->transactionProductGetFactory = $transactionProductGetFactory;
        $this->brightpearlPlatformDataFactory = $brightpearlPlatformDataFactory;
        parent::__construct(
            $exceptionHelper,
            $reportHelper,
            $brightpearlConfigApi,
            $brightpearlConfigAuthorisation,
            $brightpearlConfigOAuth2,
            $brightpearlApiServiceTransportFactory
        );
    }

    public function getName()
    {
        return 'Price API';
    }

    public function getPriceLists( $storeId, $accountCode, $idSet = null, $timeout = 5000 )
    {
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode);

        $transaction = $this->transactionPriceListGetFactory->create();
        $transaction
            ->setIdSet($idSet)
            ->setAccountCode($accountCode);

        $response = $this->submit($transaction, $this->_getTransport($storeId, $timeout));

        $priceLists = array();
        foreach ($response->getPriceLists() as $list) {
            $priceLists[] = $this->platformDataBrightpearlPriceListFactory->create()->map($list);
        }
        return $priceLists;
    }

    public function getChannels($storeId, $accountCode, $idSet = null, $timeout = 5000)
    {
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode);

        $transaction = $this->transactionChannelGetFactory->create();
        $transaction
            ->setIdSet($idSet)
            ->setAccountCode($accountCode);

        $response = $this->submit($transaction, $this->_getTransport($storeId, $timeout));

        $channels = array();
        foreach($response->getChannels() as $channel){
            $channels[] = $this->brightpearlPlatformDataFactory->create()->map($channel);
        }
        return $channels;
    }

    public function getProducts( $storeId, $accountCode, $idSet = null, $timeout = 5000 )
    {
        $this
            ->_assertNotEmpty( 'storeId', $storeId )
            ->_assertNotEmpty( 'accountCode', $accountCode );

        $transaction = $this->transactionProductGetFactory->create();
        $transaction
            ->setIdSet( $idSet )
            ->setAccountCode( $accountCode );

        $response = $this->submit( $transaction, $this->_getTransport( $storeId, $timeout ) );

        $products = array();
        foreach( $response->getProducts() as $product )
            {
                $products[] = $this->brightpearlPlatformDataFactory->create()->map( $product );
            }
        return $products;
    }

}