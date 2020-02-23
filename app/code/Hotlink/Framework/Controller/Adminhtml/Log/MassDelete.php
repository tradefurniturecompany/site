<?php
namespace Hotlink\Framework\Controller\Adminhtml\Log;

class MassDelete extends \Hotlink\Framework\Controller\Adminhtml\Log\AbstractLog
{

    protected $filter;

    function __construct(
        \Magento\Backend\App\Action\Context $context,

        \Magento\Framework\Registry $registry,
        \Hotlink\Framework\Helper\Report $reportHelper,
        \Magento\Ui\Component\MassAction\Filter $filter
    )
    {
        parent::__construct( $context,
                             $registry,
                             $reportHelper
        );
        $this->filter = $filter;
    }

    function execute()
    {
        $component = $this->filter->getComponent();
        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();
        $dataProvider = $component->getContext()->getDataProvider();

        // Magento UI js does not submit paging data (therefore loads no more than 20 records).
        if ( $selected = $this->getRequest()->getParam( \Magento\Ui\Component\MassAction\Filter::SELECTED_PARAM ) )
            {
                $offset = 1;
                $pageSize = min( count( $selected ), 5000 );
                $dataProvider->setLimit( $offset, $pageSize );
            }

        $items = $dataProvider->getSearchResult()->getItems() ?: [];
        $entryIds = array_keys( $items );
        if ( !is_array( $entryIds ) ) {
            $this->messageManager->addError( __('Please select record(s)') );
        }
        else {
            try {
                foreach ( $entryIds as $entryId ) {
                    $this->reportHelper->delete( $entryId );
                }
                $this->messageManager->addSuccess( __('Total of %1 record(s) successfully deleted', count( $entryIds ) ) );
            }
            catch ( \Exception $e ) {
                $this->messageManager->addError( $e->getMessage() );
            }
        }

        return $this->_redirect( '*/*/' );
    }

}
