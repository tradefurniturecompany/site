<?php
namespace Hotlink\Brightpearl\Model\Interaction\Stock\Bulk\Import\Environment;

class Limit  extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\Config\AbstractConfig
{
    public function getDefault()
    {
        return 512;
    }

    public function getName()
    {
        return 'Query string limit';
    }

    public function getNote()
    {
        return 'Number of characters allowed by Brightpearl API in a GET request query string';
    }

    public function getKey()
    {
        return 'query_limit';
    }

    public function getValue()
    {
        if (!$this->_valueInitialised) {
            $limit = $this->getEnvironment()->getApiQueryLimit($this->getEnvironment()->getStoreId());
            if ($limit == null)
                $limit = $this->getDefault();

            $this->setValue($limit);
        }
        return $this->_value;
    }
}