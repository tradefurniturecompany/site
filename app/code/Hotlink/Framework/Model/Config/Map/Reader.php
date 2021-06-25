<?php
namespace Hotlink\Framework\Model\Config\Map;

class Reader extends \Magento\Framework\Config\Reader\Filesystem
{

    function __construct(
        \Magento\Framework\Config\FileResolverInterface $fileResolver,
        \Magento\Framework\Config\ValidationStateInterface $validationState,
        \Hotlink\Framework\Model\Config\Map\Schema\Locator $schemaLocator,
        \Hotlink\Framework\Model\Config\Map\Converter $converter,
        $fileName = 'hotlink.xml',
        $idAttributes = [ '/hotlink/platform' => 'class',
                          '/hotlink/platform/interaction' => 'class' ],
        $domDocumentClass = 'Magento\Framework\Config\Dom',
        $defaultScope = 'global'
    )
    {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }

}
