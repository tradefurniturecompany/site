<?php
namespace Hotlink\Framework\Block\Adminhtml\Report\Log\Form\Button;

class Delete extends \Hotlink\Framework\Block\Adminhtml\Report\Log\Form\Button  implements \Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface
{
    public function getButtonData()
    {
        $data = [];
        if ( $id = $this->getReportLogId() ) {
            $data = [
                'label' => __('Delete'),
                'class' => 'delete',
                'on_click' => 'deleteConfirm(\'' . __(
                    'Are you sure you want to delete this report log ?'
                ) . '\', \'' . $this->getDeleteUrl( $id ) . '\')',
                'sort_order' => 20,
            ];
        }
        return $data;
    }

    public function getDeleteUrl( $id )
    {
        return $this->getUrl('*/*/delete', [ 'id' => $id ]);
    }
}
