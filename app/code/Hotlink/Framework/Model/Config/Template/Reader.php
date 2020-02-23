<?php
namespace Hotlink\Framework\Model\Config\Template;

class Reader extends \Magento\Framework\Config\Reader\Filesystem
{

    function __construct(
        \Magento\Framework\Config\FileResolverInterface $fileResolver,
        \Magento\Framework\Config\ValidationStateInterface $validationState,
        \Magento\Config\Model\Config\SchemaLocator $schemaLocator,
        \Magento\Config\Model\Config\Structure\Converter $converter,
        $fileName = 'template.xml',
        $idAttributes = [ '/config/system/tab' => 'id',
                          '/config/system/section' => 'id',
                          '/config/system/section(/group)+' => 'id',
                          '/config/system/section(/group)+/field' => 'id',
                          '/config/system/section(/group)+/field/depends/field' => 'id',
                          '/config/system/section(/group)+/field/options/option' => 'label'
        ],
        $domDocumentClass = 'Magento\Framework\Config\Dom',
        $defaultScope = 'adminhtml/hotlink'
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

/*
class Reader extends \Magento\Config\Model\Config\Structure\Reader
{

    function __construct(
        \Magento\Framework\Config\FileResolverInterface $fileResolver,
        \Magento\Config\Model\Config\Structure\Converter $converter,
        \Magento\Config\Model\Config\SchemaLocator $schemaLocator,
        \Magento\Framework\Config\ValidationStateInterface $validationState,
        \Magento\Framework\View\TemplateEngine\Xhtml\CompilerInterface $compiler,
        $fileName = 'template.xml',
        $idAttributes = [],
        $domDocumentClass = 'Magento\Framework\Config\Dom',
        $defaultScope = 'adminhtml/hotlink'
    )
    {
        parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $compiler,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
    }

}
*/