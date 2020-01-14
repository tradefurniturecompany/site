<?php
namespace Hotlink\Brightpearl\Model\Interaction\Lookups\Import;

class Implementation extends \Hotlink\Framework\Model\Interaction\Implementation\AbstractImplementation
{

    protected $apiServiceWarehouseHelper;
    protected $warehouseCollectionFactory;
    protected $apiServiceOrderHelper;
    protected $orderStatusCollectionFactory;
    protected $apiServiceProductHelper;
    protected $priceListItemCollectionFactory;
    protected $channelCollectionFactory;
    protected $apiServiceAccountingHelper;
    protected $nominalCodeCollectionFactory;
    protected $shippingMethodCollectionFactory;
    protected $brightpearlResourceOrderFieldCollectionFactory;
    protected $apiServiceIntegrationHelper;
    protected $interactionCreditmemoExport;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Hotlink\Brightpearl\Helper\Api\Service\Warehouse $apiServiceWarehouseHelper,
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Warehouse\CollectionFactory $warehouseCollectionFactory,
        \Hotlink\Brightpearl\Helper\Api\Service\Order $apiServiceOrderHelper,
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Order\Status\CollectionFactory $orderStatusCollectionFactory,
        \Hotlink\Brightpearl\Helper\Api\Service\Product $apiServiceProductHelper,
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Price\ListPrice\Item\CollectionFactory $priceListItemCollectionFactory,
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Channel\CollectionFactory $channelCollectionFactory,
        \Hotlink\Brightpearl\Helper\Api\Service\Accounting $apiServiceAccountingHelper,
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Nominal\Code\CollectionFactory $nominalCodeCollectionFactory,
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Shipping\Method\CollectionFactory $shippingMethodCollectionFactory,
        \Hotlink\Brightpearl\Model\ResourceModel\Lookup\Order\Custom\Field\CollectionFactory $brightpearlResourceOrderFieldCollectionFactory,

        \Hotlink\Brightpearl\Helper\Api\Service\Integration $apiServiceIntegrationHelper,
        \Hotlink\Brightpearl\Model\Interaction\Creditmemo\Export $interactionCreditmemoExport
    )
    {
        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );
        $this->apiServiceWarehouseHelper = $apiServiceWarehouseHelper;
        $this->warehouseCollectionFactory = $warehouseCollectionFactory;
        $this->apiServiceOrderHelper = $apiServiceOrderHelper;
        $this->orderStatusCollectionFactory = $orderStatusCollectionFactory;
        $this->apiServiceProductHelper = $apiServiceProductHelper;
        $this->priceListItemCollectionFactory = $priceListItemCollectionFactory;
        $this->channelCollectionFactory = $channelCollectionFactory;
        $this->apiServiceAccountingHelper = $apiServiceAccountingHelper;
        $this->nominalCodeCollectionFactory = $nominalCodeCollectionFactory;
        $this->shippingMethodCollectionFactory = $shippingMethodCollectionFactory;
        $this->brightpearlResourceOrderFieldCollectionFactory = $brightpearlResourceOrderFieldCollectionFactory;
        $this->apiServiceIntegrationHelper = $apiServiceIntegrationHelper;
        $this->interactionCreditmemoExport = $interactionCreditmemoExport;
    }

    protected function _getName()
    {
        return 'Hotlink Brightpearl settings Importer';
    }

    public function execute()
    {
        $this->getReport()->debug( 'Using accountcode : ' . $this->getEnvironment()->getAccountCode() );

        $this
            ->_importCreditMemoRefundsShippingNominalCode()
            ->_importWarehouses()
            ->_importWarehousesQuarantineLocations()
            ->_importOrderStatuses()
            ->_importPriceLists()
            ->_importChannels()
            ->_importNominalCodes()
            ->_importShippingMethods()
            ->_importOrderCustomFields();
    }

    protected function _importCreditMemoRefundsShippingNominalCode()
    {
        $report = $this->getReport()->info( 'Fetching Credit Memo Refunds Shipping Nominal Code' )->indent();
        $env    = $this->getEnvironment();
        $api    = $this->apiServiceIntegrationHelper;

        $response = $report( $api, 'getConfiguration', $env->getStoreId(), $env->getAccountCode() );
        $shippingNominalCode = $response->getShippingNominalCode();
        $report->info( 'Setting code ' . $shippingNominalCode );

        $config = $this->interactionCreditmemoExport->getConfig();
        $config->saveRefundsShippingNominalCode( $shippingNominalCode );

        $report->unindent();
        return $this;
    }

    protected function _importWarehouses()
    {
        $report = $this->getReport()->info( 'Fetching Warehouses' )->indent();
        $env    = $this->getEnvironment();
        $api    = $this->apiServiceWarehouseHelper;

        $bpWarehouses = $report( $api, 'getWarehouses', $env->getStoreId(), $env->getAccountCode(), null );
        if ( !is_array($bpWarehouses) ) {
            return $this;
        }

        $bpIndexedWarehouses   = $this->_index($bpWarehouses, 'id');
        $mageIndexedWarehouses = $this->_index($this->warehouseCollectionFactory->create()->getItems(), 'brightpearl_id');
        list($inserts, $deletes, $updates) = $this->_split($mageIndexedWarehouses, $bpIndexedWarehouses);

        $this
            ->_insert($inserts, '\Hotlink\Brightpearl\Model\Lookup\Warehouse')
            ->_delete($deletes)
            ->_update($updates,
                      array('name' => 'name', 'brightpearl_id' => 'id'),
                      $bpIndexedWarehouses);

        $report->unindent();
        return $this;
    }

    protected function _importWarehousesQuarantineLocations()
    {
        $report = $this->getReport()->info( 'Fetching Warehouses Quarantine Locations' )->indent();
        $env    = $this->getEnvironment();
        $api    = $this->apiServiceWarehouseHelper;

        $warehouses = $this->warehouseCollectionFactory->create();
        foreach ( $warehouses as $warehouse )
            {
                $name = $warehouse->getName();
                $bpid = $warehouse->getBrightpearlId();
                $report
                    ->info( "warehouse $name ($bpid)" )
                    ->indent();
                $newValue = $report( $api, 'getWarehouseLocationQuarantine', $env->getStoreId(), $env->getAccountCode(), $bpid );

                $oldValue = $warehouse->getQuarantineLocationId();
                $warehouse->setQuarantineLocationId( $newValue );
                $warehouse->save();
                $report
                    ->incSuccess()
                    ->info( "quarantine location id updated from [$oldValue] to [$newValue]" )
                    ->unindent();
            }
        $report->unindent();
        return $this;
    }

    protected function _importOrderStatuses()
    {
        $report = $this->getReport()->info('Fetching Order Statuses')->indent();
        $env    = $this->getEnvironment();
        $api    = $this->apiServiceOrderHelper;

        $bpStatuses = $report( $api, 'getStatuses', $env->getStoreId(), $env->getAccountCode(), null );

        if ( !is_array($bpStatuses) ) {
            return $this;
        }

        $bpIndexedStatuses   = $this->_index($bpStatuses, 'statusId');
        $mageIndexedStatuses = $this->_index($this->orderStatusCollectionFactory->create()->getItems(), 'brightpearl_id');
        list($inserts, $deletes, $updates) = $this->_split($mageIndexedStatuses, $bpIndexedStatuses);

        $fieldmap = array( 'name' => 'name',
                           'brightpearl_id' => 'statusId',
                           'order_type_code' => 'orderTypeCode' );
        $this
            ->_insert( $inserts, '\Hotlink\Brightpearl\Model\Lookup\Order\Status', $fieldmap, '_getOrderStatusMessage' )
            ->_delete( $deletes )
            ->_update( $updates, $fieldmap, $bpIndexedStatuses );

        $report->unindent();
        return $this;
    }

    protected function _importPriceLists()
    {
        $report = $this->getReport()->info('Fetching Price Lists')->indent();
        $env    = $this->getEnvironment();
        $api    = $this->apiServiceProductHelper;

        $bpPriceLists = $report( $api, 'getPriceLists', $env->getStoreId(), $env->getAccountCode(), null );

        if (!is_array($bpPriceLists)) {
            return $this;
        }

        $bpIndexedPriceLists   = $this->_index($bpPriceLists, 'id');
        $mageIndexedPriceLists = $this->_index($this->priceListItemCollectionFactory->create()->getItems(), 'brightpearl_id');
        list($inserts, $deletes, $updates) = $this->_split($mageIndexedPriceLists, $bpIndexedPriceLists);

        $fieldmap = array( 'name'                 => array( 'name' => 'text' ),
                           'brightpearl_id'       => 'id',
                           'code'                 => 'code',
                           'currency_code'        => 'currencyCode',
                           'price_list_type_code' => 'priceListTypeCode' );

        $this
            ->_insert( $inserts, '\Hotlink\Brightpearl\Model\Lookup\Price\ListPrice\Item', $fieldmap, '_getPriceListItemMessage' )
            ->_delete( $deletes )
            ->_update( $updates, $fieldmap, $bpIndexedPriceLists );

        $report->unindent();
        return $this;
    }

    protected function _importChannels()
    {
        $report = $this->getReport()->info('Fetching Channels')->indent();
        $env    = $this->getEnvironment();
        $api    = $this->apiServiceProductHelper;

        $bpChannels = $report( $api, 'getChannels', $env->getStoreId(), $env->getAccountCode(), null );

        if (!is_array($bpChannels)) {
            return $this;
        }

        $bpIndexedChannels   = $this->_index($bpChannels, 'id');
        $integrationChannels = array_filter($bpIndexedChannels, array($this, '_integrationChannel'));

        $rejectedChannels = array_diff_key($bpIndexedChannels, $integrationChannels);
        if ($rejectedChannels) {
            $message = array();
            foreach ($rejectedChannels as $channel) {
                $message[] = $channel['name'] . ' ['. $channel['id'] .']';
            }
            $report->debug( "Rejected channels (non Magento integration type): ". implode(',', $message) );
        }
        $mageIndexedChannels = $this->_index($this->channelCollectionFactory->create()->getItems(), 'brightpearl_id');
        list($inserts, $deletes, $updates) = $this->_split($mageIndexedChannels, $integrationChannels);

        $this
            ->_insert($inserts, '\Hotlink\Brightpearl\Model\Lookup\Channel')
            ->_delete($deletes)
            ->_update($updates, array('name' => 'name', 'brightpearl_id' => 'id'), $integrationChannels);

        $report->unindent();
        return $this;
    }

    protected function _importNominalCodes()
    {
        $report = $this->getReport()->info('Fetching Nominal Codes')->indent();
        $env    = $this->getEnvironment();
        $api    = $this->apiServiceAccountingHelper;

        $bpNominalCodes = $report( $api, 'getNominalCodes', $env->getStoreId(), $env->getAccountCode(), null );

        if (!is_array($bpNominalCodes)) {
            return $this;
        }

        $bpIndexedCodes   = $this->_index($bpNominalCodes, 'id');
        $mageIndexedCodes = $this->_index($this->nominalCodeCollectionFactory->create()->getItems(), 'brightpearl_id');
        list($inserts, $deletes, $updates) = $this->_split($mageIndexedCodes, $bpIndexedCodes);

        $this
            ->_insert($inserts,
                      '\Hotlink\Brightpearl\Model\Lookup\Nominal\Code',
                      array('name' => 'name',
                            'brightpearl_id' => 'id',
                            'code' => 'code'))
            ->_delete($deletes)
            ->_update($updates,
                      array('name' => 'name',
                            'brightpearl_id' => 'id',
                            'code' => 'code'),
                      $bpIndexedCodes);

        $report->unindent();
        return $this;
    }

    protected function _importShippingMethods()
    {
        $report = $this->getReport()
            ->info('Fetching Shipping Methods')
            ->indent();

        $bpShippingMethods = $report( $this->apiServiceWarehouseHelper, 'getShippingMethods',
                                      $this->getEnvironment()->getStoreId(),
                                      $this->getEnvironment()->getAccountCode(),
                                      null );

        if (!is_array($bpShippingMethods)) {
            return $this;
        }

        $bpIndexedMethods   = $this->_index($bpShippingMethods, 'id');
        $mageIndexedMethods = $this->_index($this->shippingMethodCollectionFactory->create()->getItems(), 'brightpearl_id');
        list($inserts, $deletes, $updates) = $this->_split($mageIndexedMethods, $bpIndexedMethods);

        $this
            ->_insert($inserts,
                      '\Hotlink\Brightpearl\Model\Lookup\Shipping\Method',
                      array('name' => 'name',
                            'brightpearl_id' => 'id',
                            'code' => 'code'))
            ->_delete($deletes)
            ->_update($updates,
                      array('name' => 'name',
                            'brightpearl_id' => 'id',
                            'code' => 'code'),
                      $bpIndexedMethods);

        $report->unindent();
        return $this;
    }

    protected function _importOrderCustomFields()
    {
        $report = $this->getReport()->info('Fetching Order Custom Fields')->indent();

        $bpOrderFields = $report( $this->apiServiceOrderHelper, 'getCustomFields',
                                  $this->getEnvironment()->getStoreId(),
                                  $this->getEnvironment()->getAccountCode(),
                                  null );

        // filter by type text
        $nbefore = count($bpOrderFields);
        $bpOrderFields = array_filter($bpOrderFields, array($this, '_orderFieldText'));
        $nafter = count($bpOrderFields);

        if ( $nbefore - $nafter > 0 ) {
            $report->warn($nbefore - $nafter . ' of ' . $nbefore . ' custom fields filtered out because are not of type TEXT_AREA');
        }

        if (!is_array($bpOrderFields)) {
            return $this;
        }

        $bpIndexedFields   = $this->_index($bpOrderFields, 'id');
        $mageIndexedFields = $this->_index($this->brightpearlResourceOrderFieldCollectionFactory->create()->getItems(), 'brightpearl_id');
        list($inserts, $deletes, $updates) = $this->_split($mageIndexedFields, $bpIndexedFields);

        $this
            ->_insert($inserts,
                      '\Hotlink\Brightpearl\Model\Lookup\Order\Custom\Field',
                      array('name' => 'name',
                            'brightpearl_id' => 'id',
                            'code' => 'code'))
            ->_delete($deletes)
            ->_update($updates,
                      array('name' => 'name',
                            'brightpearl_id' => 'id',
                            'code' => 'code'),
                      $bpIndexedFields);

        $report->unindent();
        return $this;
    }

    protected function _index(array $source, $by)
    {
        $indexed = array();
        foreach($source as $item){
            $indexed[ $this->_indexify($this->_getDataValue($by, $item)) ] = $item;
        }
        return $indexed;
    }

    protected function _split(array $magentoIndexed, array $bpIndexed)
    {
        // Delete: all items in $magentoIndexed and not in $bpIndexed
        $deletes = array_diff_key($magentoIndexed, $bpIndexed);
        return array(array_diff_key($bpIndexed, $magentoIndexed), // Insert: all items in $bpIndexed and not in $magentoIndexed
                     $deletes,
                     array_diff_key($magentoIndexed, $deletes));  // Update: all items in $magentoIndexed and not in $deletes
    }

    protected function _insert(array $inserts, $model, array $fieldsMap = array('name' => 'name', 'brightpearl_id' => 'id'), $messageFun = '_getMessage')
    {
        foreach($inserts as $item){
            $this->factory()->create( $model )
                ->setData($this->_getItemDataArray($fieldsMap, $item))
                ->save();
            $this->getReport()->incSuccess()
                              ->info($this->{$messageFun}('created', $item));
        }
        return $this;
    }

    protected function _delete(array $deletes, $messageFun = '_getMessage')
    {
        foreach($deletes as $item){
            $item->setDeleted(1)->save();
            $this->getReport()->incSuccess()
                              ->info($this->{$messageFun}('deleted', $item));
        }
        return $this;
    }

    protected function _update(array $updates, array $fieldsMap, array $bpIndexed, $identifier = 'brightpearl_id', $messageFun = '_getMessage')
    {
        foreach($updates as $item){
            $newData = $this->_getItemDataArray($fieldsMap,
                                                $bpIndexed[$this->_indexify($item->getData($identifier))]);
            foreach($newData as $name => $value){
                $item->setData($name, $value);
            }
            $item->setDeleted(0)->save();
            $this->getReport()->incSuccess()
                             ->info($this->{$messageFun}('updated', $item));
        }

        return $this;
    }

    protected function _getItemDataArray($fieldsMap, $item, $excludes = array())
    {
        $data = array();
        foreach($fieldsMap as $mageKey => $bpKey){
            if (in_array($mageKey, $excludes)) continue;
            $data[$mageKey] = $this->_getDataValue($bpKey, $item);
        }
        return $data;
    }

    protected function _getDataValue($key, $source)
    {
        if(is_array($key))
            return $this->_getDataValue(current($key), $source[current(array_keys($key))]);
        return $source[$key];
    }

    protected function _indexify($string)
    {
        return strtolower($string);
    }

    protected function _orderFieldText($field)
    {
        return strtolower($field['customFieldType']) == "text_area";
    }

    protected function _getMessage($type, $item)
    {
        return $type.' "['.$item->getData('id').'] '.$item->getData('name'). '"';
    }

    protected function _getOrderStatusMessage($type, \Hotlink\Brightpearl\Model\Platform\Data $status)
    {
        return $type.' "['.$status->getData('statusId').'] '.$status->getData('name'). '"';
    }

    protected function _getPriceListItemMessage($type, \Hotlink\Brightpearl\Model\Platform\Data $item)
    {
        return $type.' "['.$item->getData('id').'] '.$this->_getDataValue(array('name' => 'text'), $item). '"';
    }

    /**
     * Returns true if $channel has providerCode ==  'bpmagento' and is active flase otherwise.
     */
    protected function _integrationChannel(\Hotlink\Brightpearl\Model\Platform\Data $channel)
    {
        $data = $channel->getData();

        if (isset($data['integrationDetail']))
            {
                $integration = $data['integrationDetail'];
                $integrationData = $integration->getData();
                return
                    isset($integrationData['providerCode'], $integrationData['active'])
                    and
                    ( ( $integrationData['providerCode'] == \Hotlink\Brightpearl\Model\Platform::APP_REF_M2 )
                      || ( $integrationData['providerCode'] == \Hotlink\Brightpearl\Model\Platform::APP_REF_M1 )
                    )
                    and
                    $integrationData['active'] == 'true';
        }

        return false;
    }
}
