<?php
namespace Hotlink\Brightpearl\Model\Api\Service\Workflow\Product\Message\Price\Get;

class Request extends \Hotlink\Brightpearl\Model\Api\Service\Message\Request\Get\AbstractGet
{

    public function getFunction()
    {
        return $this->getMethod(). " workflow-integration-service/product-pricing";
    }

    public function getAction()
    {
        $transaction = $this->getTransaction();

        $accountCode = $transaction->getAccountCode();
        $skus        = $transaction->getSkus();
        $pricelists  = $transaction->getPricelists();

        return $this->buildAction($accountCode, $pricelists, $skus);
    }

    public function buildAction($accountCode, $pricelists, $skus)
    {
        $params = array('skus'       => $this->_csv($skus),
                        'pricelists' => $this->_csv($pricelists));

        $query = http_build_query($params);

        return sprintf('/2.0.0/%s/workflow-integration-service/product-pricing?%s', $accountCode, $query);
    }

    public function validate()
    {
        parent::validate();

        $pricelists = $this->getTransaction()->getPricelists();
        $this
            ->_assertNotEmpty($pricelists , 'pricelists' )
            ->_assertType($pricelists, 'array', 'pricelists');

        $skus = $this->getTransaction()->getSkus();
        if ($skus)
            $this->_assertType($skus, 'array', 'skus');

        return $this;
    }
}
