<?php
namespace Hotlink\Framework\Helper;

class Factory
{

    protected $universalFactory;
    protected $objectManager;

    function __construct(
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->universalFactory = $universalFactory;
        $this->objectManager = $objectManager;
    }

    function create( $class, $args = [] )
    {
        return $this->universalFactory->create( $class, $args );
    }

    // function create( $class )
    // {
    //     return $this->objectManager->create( $class );
    // }

    function get( $class )
    {
        return $this->objectManager->get( $class );
    }

    function singleton( $class )
    {
        return $this->get( $class );
    }

    function objectManager()
    {
        return $this->objectManager;
    }

    function objectManagerInstance()
    {
        return \Magento\Framework\App\ObjectManager::getInstance();
    }

    function get2( $class )
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get( $class );
    }

}
