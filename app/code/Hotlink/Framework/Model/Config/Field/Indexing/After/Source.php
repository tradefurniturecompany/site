<?php
namespace Hotlink\Framework\Model\Config\Field\Indexing\After;

class Source
{
    protected $configIndexer;

    function __construct(
        \Magento\Framework\Indexer\ConfigInterface $configIndexer
    ) {
        $this->configIndexer = $configIndexer;
    }

    function toOptionArray()
    {
        $indexers = array_keys( $this->configIndexer->getIndexers() );
        $result = array();
        foreach ( $indexers as $code )
            {
                $result[] = [ 'value' => $code, 'label' => $code ];
            }
        return $result;
    }

}