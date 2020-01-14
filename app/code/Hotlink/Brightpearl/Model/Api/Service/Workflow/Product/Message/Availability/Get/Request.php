<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Message\Availability\Get;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{
    public function getFunction()
    {
        return $this->getMethod(). " workflow-integration-service/product-availability";
    }

    public function buildAction( $accountCode, array $warehouses, array $skus)
    {
        $params = array( 'skus'       => $this->_csv( $skus ),
                         'warehouses' => $this->_csv( $warehouses ) );

        $query = http_build_query($params);

        return sprintf( '/2.0.0/%s/workflow-integration-service/product-availability?%s', $accountCode, $query );
    }


    public function getAction()
    {
        $accountCode = $this->getTransaction()->getAccountCode();
        $skus = $this->getTransaction()->getSkus();
        $warehouses = $this->getTransaction()->getWarehouses();

        return $this->buildAction( $accountCode, $warehouses, $skus );
    }

    public function validate()
    {
        parent::validate();

        return $this
            ->_assertNotEmpty($this->getTransaction()->getSkus(), 'skus')
            ->_assertNotEmpty($this->getTransaction()->getWarehouses(), 'warehouses');
    }
}