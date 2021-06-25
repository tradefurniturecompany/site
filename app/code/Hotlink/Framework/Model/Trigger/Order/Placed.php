<?php
namespace Hotlink\Framework\Model\Trigger\Order;

class Placed extends \Hotlink\Framework\Model\Trigger\AbstractTrigger
{

    const CONTEXT_ON_ORDER_PLACED = 'on_order_placed';
    const CONTEXT_ON_ORDER_PLACED_ADMIN = 'on_order_placed_admin';

    const EVT_SUBMIT_SUCCESS = 'sales_model_service_quote_submit_success';

    protected $checkoutSession;
    protected $filterMagentoFactory;
    protected $streamReaderFactory;
    protected $scopeHelper;

    public function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Framework\Model\Config\Map $configMap,
        \Hotlink\Framework\Model\UserFactory $userFactory,

        \Magento\Checkout\Model\Session $checkoutSession,
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
        $this->checkoutSession = $checkoutSession;
        $this->filterMagentoFactory = $filterMagentoFactory;
        $this->streamReaderFactory = $streamReaderFactory;
        $this->scopeHelper = $scopeHelper;
    }

    protected function _getName()
    {
        return 'Order created';
    }

    public function getMagentoEvents()
    {
        return [ 'After Service Quote Submit Success' => self::EVT_SUBMIT_SUCCESS ];
    }

    public function getContexts()
    {
        return [ self::CONTEXT_ON_ORDER_PLACED       => 'On order placed (frontend)',
                 self::CONTEXT_ON_ORDER_PLACED_ADMIN => 'On order placed (admin)'];
    }

    public function getContext()
    {
        $context = false;
        if ( $this->scopeHelper->isAdmin() )
            {
                switch ( $this->getMagentoEvent()->getName() )
                    {
                        case self::EVT_SUBMIT_SUCCESS:
                            $context = self::CONTEXT_ON_ORDER_PLACED_ADMIN;
                            break;
                    }
            }
        else
            {
                switch ( $this->getMagentoEvent()->getName() )
                    {
                        case self::EVT_SUBMIT_SUCCESS:
                            $context = self::CONTEXT_ON_ORDER_PLACED;
                            break;
                    }
            }
        return $context;
    }

    protected function _execute()
    {
        $incrementIds = array();

        switch ( $this->getMagentoEvent()->getName() )
            {
                case self::EVT_SUBMIT_SUCCESS:
                    if ( $order = $this->getMagentoEvent()->getOrder() )
                        {
                            $incrementIds = array( $order->getIncrementId() );
                        }
                    break;
                default:
                    $incrementIds = array( $this->checkoutSession->getLastRealOrderId() );
                    break;
            }

        $filter = $this->filterMagentoFactory->create()
                ->setModel( '\Magento\Sales\Model\Order' )
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