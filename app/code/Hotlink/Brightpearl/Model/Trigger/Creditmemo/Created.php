<?php
namespace Hotlink\Brightpearl\Model\Trigger\Creditmemo;

class Created extends \Hotlink\Framework\Model\Trigger\AbstractTrigger
{

    const CONTEXT_ON_CREDITMEMO_CREATED_ADMIN = 'on_creditmemo_created_admin';

    const EVT_CREATED_ADMIN = 'hotlink_brightpearl_creditmemo_created_admin';

    protected $filterMagentoFactory;
    protected $streamReaderFactory;
    protected $scopeHelper;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Framework\Model\Config\Map $configMap,
        \Hotlink\Framework\Model\UserFactory $userFactory,

        \Hotlink\Framework\Model\Filter\MagentoFactory $filterMagentoFactory,
        \Hotlink\Framework\Model\Interaction\Environment\Parameter\Stream\ReaderFactory $streamReaderFactory,
        \Hotlink\Framework\Helper\Scope $scopeHelper
    )
    {
        parent::__construct(
            $exceptionHelper,
            $reflectionHelper,
            $reportHelper,
            $factoryHelper,
            $storeManager,
            $configMap,
            $userFactory
        );
        $this->filterMagentoFactory = $filterMagentoFactory;
        $this->streamReaderFactory = $streamReaderFactory;
        $this->scopeHelper = $scopeHelper;
    }

    protected function _getName()
    {
        return 'Creditmemo created';
    }

    function getMagentoEvents()
    {
        return [ 'After Credit Memo Created (Admin)' => self::EVT_CREATED_ADMIN ];
    }

    function getContexts()
    {
        return [ self::CONTEXT_ON_CREDITMEMO_CREATED_ADMIN => 'On creditmemo created (admin)' ];
    }

    function getContext()
    {
        $context = false;
        if ( $this->scopeHelper->isAdmin() )
            {
                switch ( $this->getMagentoEvent()->getName() )
                    {
                        case self::EVT_CREATED_ADMIN:
                            $context = self::CONTEXT_ON_CREDITMEMO_CREATED_ADMIN;
                            break;
                    }
            }
        return $context;
    }

    protected function _execute()
    {
        $incrementIds = [];

        switch ( $this->getMagentoEvent()->getName() )
            {
                case self::EVT_CREATED_ADMIN:
                    if ( $creditmemo = $this->getMagentoEvent()->getCreditmemo() )
                        {
                            $incrementIds = [ $creditmemo->getIncrementId() ];
                        }
                    break;
                default:
                    break;
            }

        $filter = $this->filterMagentoFactory->create()
                ->setModel( '\Magento\Sales\Model\Order\Creditmemo' )
                ->setField( 'increment_id' )
                ->setIdentifiers( $incrementIds )
                ->setRequired (true );

        foreach ( $this->getInteractions() as $interaction )
            {
                if ( !$interaction->hasEnvironment( $this->storeManager()->getStore()->getStoreId() ) )
                    {
                        $interaction->createEnvironment( $this->storeManager()->getStore()->getStoreId() );
                    }
                foreach ( $interaction->getEnvironments() as $environment )
                    {
                        $parameter = $this->streamReaderFactory->create();
                        $parameter->getValue()->open( $filter );
                        $environment->addParameter( $parameter );
                    }
                $interaction->setTrigger( $this )->execute();
            }
    }

}