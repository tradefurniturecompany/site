<?php
namespace Hotlink\Brightpearl\Model\Trigger\Monitor\Creditmemo;

class Queue extends \Hotlink\Framework\Model\Trigger\AbstractTrigger
{

    /**
     * @var \Hotlink\Framework\Model\Stream\Magento\Model\ReaderFactory
     */
    protected $interactionStreamMagentoModelReaderFactory;

    function __construct(
        \Hotlink\Framework\Helper\Exception $exceptionHelper,
        \Hotlink\Framework\Helper\Reflection $reflectionHelper,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Hotlink\Framework\Helper\Factory $factoryHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Hotlink\Framework\Model\Config\Map $configMap,
        \Hotlink\Framework\Model\UserFactory $userFactory,

        \Hotlink\Framework\Model\Stream\Magento\Model\ReaderFactory $interactionStreamMagentoModelReaderFactory
    )
    {
        $this->interactionStreamMagentoModelReaderFactory = $interactionStreamMagentoModelReaderFactory;

        parent::__construct(
            $exceptionHelper,
            $reflectionHelper,
            $reportHelper,
            $factoryHelper,
            $storeManager,
            $configMap,
            $userFactory
        );
    }

    function getMagentoEvents()
    {
        return [ 'hotlink_framework_monitor_creditmemo_queue' ];
    }

    function getContexts()
    {
        return [ 'on_creditmemo_queued' => 'On creditmemo queued' ];
    }

    function getContext()
    {
        return 'on_creditmemo_queued';
    }

    protected function _getName()
    {
        return 'Creditmemo queued';
    }

    protected function _execute()
    {
        $interaction = $this->getMagentoEvent()->getInteraction();
        $this->setInteractions( $interaction );

        $creditmemos = $this->getMagentoEvent()->getCollection();
        $stream = $this->interactionStreamMagentoModelReaderFactory->create()->open( $creditmemos );

        // env created for admin store. this is ok as interaction itself creates separate envs for each creditmemo.

        $environment = ( $interaction->hasEnvironment( $this->getStoreId() ) )
            ? $interaction->getEnvironment( $this->getStoreId() )
            : $interaction->newEnvironment( $this->getStoreId() );


        $environment->getParameter( 'stream' )->setValue( $stream );

        return parent::_execute();
    }

}