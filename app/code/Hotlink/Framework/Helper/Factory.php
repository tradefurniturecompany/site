<?php
namespace Hotlink\Framework\Helper;

class Factory
{

    protected $universalFactory;
    protected $objectManager;

    public function __construct(
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->universalFactory = $universalFactory;
        $this->objectManager = $objectManager;
    }

    public function create( $class, $args = [] )
    {
        return $this->universalFactory->create( $class, $args );
    }

    // public function create( $class )
    // {
    //     return $this->objectManager->create( $class );
    // }

    public function get( $class )
    {
        return $this->objectManager->get( $class );
    }

    public function singleton( $class )
    {
        return $this->get( $class );
    }

    public function objectManager()
    {
        return $this->objectManager;
    }

    public function objectManagerInstance()
    {
        return \Magento\Framework\App\ObjectManager::getInstance();
    }

    public function get2( $class )
    {
        return \Magento\Framework\App\ObjectManager::getInstance()->get( $class );
    }

}
