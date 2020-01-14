<?php
namespace Hotlink\Framework\Model\Interaction\Environment\Parameter\Filter;

class Magento extends \Hotlink\Framework\Model\Interaction\Environment\Parameter\AbstractParameter
{

    protected $filterMagentoFactory;
    protected $filterMagentoHelper;

    public function __construct(
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

    public function getName()
    {
        return 'Magento Filter';
    }

    public function getDefault()
    {
        return $this->filterMagentoFactory->create();
    }

    public function getKey()
    {
        return 'filter';
    }

    public function getFormHelper()
    {
        return $this->filterMagentoHelper;
    }

    public function asString()
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
