<?php
namespace Hotlink\Brightpearl\Helper\Api\Service\Search;

abstract class AbstractSearch extends \Hotlink\Brightpearl\Helper\Api\Service\AbstractService
{

    protected $brightpearlPlatformDataFactory;
    protected $dataObjectFactory;
    protected $factoryHelper;

    abstract protected function _getTransactionModel();
    abstract protected function _getPlatformDataModel();

    function __construct(
        \Hotlink\Brightpearl\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Brightpearl\Model\Config\Api $brightpearlConfigApi,
        \Hotlink\Brightpearl\Model\Config\Authorisation $brightpearlConfigAuthorisation,
        \Hotlink\Brightpearl\Model\Config\OAuth2 $brightpearlConfigOAuth2,
        \Hotlink\Brightpearl\Model\Api\Service\TransportFactory $brightpearlApiServiceTransportFactory,

        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Hotlink\Brightpearl\Model\Platform\DataFactory $brightpearlPlatformDataFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory
    )
    {
        $this->factoryHelper = $factoryHelper;
        $this->dataObjectFactory = $dataObjectFactory;
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

    function search($storeId,
                           $accountCode,
                           $filters = null,
                           $pageSize = null,
                           $firstResult = null,
                           $sortBy = null,
                           $sortDirection = null,
                           $columns = null,
                           $timeout = 5000)
    {
        $this
            ->_assertNotEmpty('storeId', $storeId)
            ->_assertNotEmpty('accountCode', $accountCode);

        $transactionModel  = $this->_getTransactionModel();
        $platformDataModel = $this->_getPlatformDataModel();

        $transaction = $this->factoryHelper->create( $transactionModel );
        $transaction
            ->setAccountCode($accountCode)
            ->setColumns($columns)
            ->setPageSize($pageSize)
            ->setFirstResult($firstResult)
            ->setSortBy($sortBy)
            ->setSortDirection($sortDirection)
            ->setFilters($filters);

        $response = $this->submit( $transaction, $this->_getTransport( $storeId, $timeout ) );

        $instanceHeader = $response->getHeader( 'brightpearl-installed-integration-instance-id' );
        if ( is_null( $instanceHeader ) )
            {
                $instanceHeader = $response->getHeader( 'Brightpearl-installed-integration-instance-id' );
            }
        $instanceId = $instanceHeader->getFieldValue();

        $results    = $response->getResults() ? $response->getResults() : array();
        $pagination = $response->getPagination();

        $_results = array();
        foreach ( $results as $_result )
            {
                $_results[] = $this->factoryHelper->create( $platformDataModel )->map( $_result );
            }

        $data = [ 'results'     => $_results,
                  'instance_id' => $instanceId,
                  'pagination'  => $this->brightpearlPlatformDataFactory->create()->map( $pagination ) ];

        $wrap = $this->dataObjectFactory->create( [ 'data' => $data ] );

        return $wrap;
    }

}