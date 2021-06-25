<?php
namespace Hotlink\Framework\Model\Trigger;

abstract class AbstractTrigger extends \Hotlink\Framework\Model\AbstractModel implements \Magento\Framework\Event\ObserverInterface
{

    /*
      This class listens for Magento events, and invokes Interactions.
     */
    protected $storeManager;
    protected $configMap;
    protected $userFactory;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,

        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Framework\Model\Config\Map $configMap,
        \Hotlink\Framework\Model\UserFactory $userFactory
    )
    {
        parent::__construct( $exceptionHelper, $reflectionHelper, $reportHelper, $factoryHelper );
        $this->storeManager = $storeManager;
        $this->configMap = $configMap;
        $this->userFactory = $userFactory;
    }

    abstract public function getMagentoEvents();     // Returns an array of Magento event names that this object handles.
    abstract public function getContexts();          // Returns an array of Context names that this object responds to.
    abstract protected function _getName();          // Returns the name of the trigger.

    protected $_user = null;
    protected $_magentoEvent = null;
    protected $_source = null;
    protected $_interactions = false;
    protected $_executing = false;

    public function getName()
    {
        return __( $this->_getName() );
    }

    public function storeManager()
    {
        return $this->storeManager;
    }

    //
    //   \Magento\Framework\Event\ObserverInterface
    //
    public function execute( \Magento\Framework\Event\Observer $observer )
    {
        // protection from "Maximum function nesting level reached" error, as interactions launched by
        // this trigger may load product collections (especially in frontend), causing an "infinite" loop
        if ( ! $this->_executing )
            {
                $this->_executing = true;
                $this->setMagentoEvent( $observer->getEvent() );
                $name = $observer->getEvent()->getName();
                $events = $this->getMagentoEvents();
                if ( ! in_array( $name, $events ) )
                    {
                        $this->exception()->throwImplementation( "Event $name is not permitted by [class]", $this );
                    }
                $lookup = array_flip( $events );
                if ( array_key_exists( $name, $lookup ) )
                    {
                        $observer->getEvent()->setDescription( $lookup[ $name ] );
                    }
                else
                    {
                        $observer->getEvent()->setDescription( $name );
                    }
                $this->unsInteractions(); // #3974 - triggers are singletons but should always use interactions as models, so clear them here
                $this->_execute();
                $this->_executing = false;
            }
    }

    //
    //  Returns true if any interactions are enabled and can be executed
    //
    public function getExecutableInteractions()
    {
        $result = [];
        foreach ( $this->getInteractions() as $interaction )
            {
                $interaction->setTrigger( $this );
                if ( !$interaction->hasEnvironment( $this->getStoreId() ) )
                    {
                        $interaction->createEnvironment( $this->getStoreId() );
                    }
                if ( $interaction->canExecute() )
                    {
                        $result[] = $interaction;
                    }
            }
        return $result;
    }

    //
    //  Overload this for non-standard trigger behaviour
    //
    protected function _execute()
    {
        foreach ( $this->getInteractions() as $interaction )
            {
                $interaction->setTrigger( $this );
                if ( !$interaction->hasEnvironment( $this->getStoreId() ) )
                    {
                        $interaction->createEnvironment( $this->getStoreId() );
                    }
                $interaction->execute();
            }
    }

    //
    //  Overload this to control the setup of environments for stores other than Magento current
    //
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }

    public function getInteractions()
    {
        if ( !$this->_interactions )
            {
                $this->_interactions = [];
                $interactions = $this->configMap->getInteractions( $this );
                foreach ( $interactions as $interaction )
                    {
                        $this->_interactions[ $interaction ] = $this->factory()->create( $interaction );
                    }
            }
        return $this->_interactions;
    }

    protected function setInteractions( $interactions )
    {
        $this->_interactions = is_array( $interactions ) ? : [ $interactions ];
        return $this;
    }

    protected function unsInteractions()
    {
        $this->_interactions = false;
        return $this;
    }

    //
    //  Overload this to implement contextual triggers.
    //
    public function getContext()
    {
        return $this->getMagentoEvent()->getName();
    }

    public function getMagentoEvent()
    {
        return $this->_magentoEvent;
    }

    protected function setMagentoEvent( $value )
    {
        $this->_magentoEvent = $value;
    }

    public function getUser()
    {
        if ( is_null ( $this->_user ) )
            {
                $this->_user = $this->userFactory->create();
            }
        return $this->_user;
    }

    public function getContextLabel()
    {
        $context = $this->getContext();
        $contexts = $this->getContexts();
        if ( isset( $contexts[ $context ] ) )
            {
                return $contexts[ $context ];
            }
        return '*unknown*';
    }

    //
    //  IReport
    //
    public function getReportSection()
    {
        return 'trigger';
    }

}