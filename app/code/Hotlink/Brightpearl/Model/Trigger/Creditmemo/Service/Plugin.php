<?php
namespace Hotlink\Brightpearl\Model\Trigger\Creditmemo\Service;

class Plugin
{

    protected $eventManager;

    function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Sales\Api\CreditmemoRepositoryInterface $creditmemoRepository,
        \Magento\Framework\App\AreaList $areaList,
        \Magento\Framework\App\Request\Http $requestHttp
    )
    {
        $this->eventManager = $eventManager;
        $this->creditmemoRepository = $creditmemoRepository;
        $this->areaList = $areaList;
        $this->request = $requestHttp;
    }

    function afterRefund( $subject, $result )
    {
        if ( $result instanceof \Magento\Sales\Model\Order\Creditmemo )
            {
                $creditmemo = $result;
                $areaCode = $this->areaList->getCodeByFrontName( $this->request->getFrontName() );
                $event = "hotlink_brightpearl_creditmemo_created_byservice_" . $areaCode;
                $this->eventManager->dispatch( $event, [ 'creditmemo' => $creditmemo ] );
            }
        return $result;
    }

}
