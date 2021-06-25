<?php
namespace Hotlink\Brightpearl\Model\Trigger\Creditmemo\Refund;

class Plugin
{

    protected $eventManager;
    protected $creditmemoRepository;
    protected $areaList;
    protected $request;

    public function __construct(
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

    public function afterExecute( $subject, $result )
    {
        if ( is_numeric( $result ) )
            {
                $creditmemo = $this->creditmemoRepository->get( $result );
                $areaCode = $this->areaList->getCodeByFrontName( $this->request->getFrontName() );
                $event = "hotlink_brightpearl_creditmemo_created_byrefund_" . $areaCode;
                $this->eventManager->dispatch( $event, [ 'creditmemo' => $creditmemo ] );
            }
        return $result;
    }

}
