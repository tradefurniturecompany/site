<?php
namespace Hotlink\Framework\Model\Interaction\Environment\Parameter\Filter;

class Magento extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{

    protected $filterMagentoFactory;
    protected $filterMagentoHelper;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Html\Form\Environment\Parameter $parameterHelper,

        \Hotlink\Framework\Model\Filter\MagentoFactory $filterMagentoFactory,
        \Hotlink\Framework\Html\Form\Environment\Parameter\Filter\Magento $filterMagentoHelper
    )
    {
        parent::__construct( $exceptionHelper, $parameterHelper );
        $this->filterMagentoFactory = $filterMagentoFactory;
        $this->filterMagentoHelper = $filterMagentoHelper;
    }

    function getName()
    {
        return 'Magento Filter';
    }

    function getDefault()
    {
        return $this->filterMagentoFactory->create();
    }

    function getKey()
    {
        return 'filter';
    }

    function getFormHelper()
    {
        return $this->filterMagentoHelper;
    }

    function asString()
    {
        $identifiers = '';
        if ( $filter = $this->getValue() )
            {
                $identifiers = implode( ",", $filter->getIdentifiers() );
            }
        $output = $this->getName() . ' = ' . $identifiers;
        return $output;
    }

}
